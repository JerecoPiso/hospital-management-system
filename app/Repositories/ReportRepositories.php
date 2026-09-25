<?php

namespace App\Repositories;

use App\Models\DoctorFee;
use App\Models\Invoice;
use App\Models\MedicineStock;
use App\Models\MedicineStockMovement;
use App\Models\PatientCase;
use App\Models\PrescriptionItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportRepositories
{
    private const DISPENSED_STATUSES = ['done', 'picked-up'];

    // Invoice item category => section label, in display order.
    private const INVOICE_SECTIONS = [
        'Professional Fee' => 'PROFESSIONAL FEE',
        'Fee' => 'FEES',
        'Laboratory' => 'LABORATORY',
        'Radiology' => 'RADIOLOGY',
        'Medicine' => 'MEDICINES',
        'Supply' => 'SUPPLIES',
    ];

    private const CASE_TYPE_LABELS = [
        'outpatient' => 'OPD',
        'inpatient' => 'IPD',
    ];

    /**
     * One row per dispensed prescription item, grouped by patient case (ordered
     * by patient_case_id), with each case's summed doctor professional fees shown
     * on its first row.
     *
     * STOCKS is the medicine's on-hand quantity just before the dispense. It is
     * rebuilt by walking backwards from the current stock through every later
     * event: other dispenses, plus ledger movements not tied to a prescription
     * item (deliveries, station distributions, manual adjustments). Movements
     * tied to prescription items are skipped because the dispense rows already
     * represent them, and restored (deleted) items net to zero.
     */
    public function dispenseMedicineStocks(array $filter = []): array
    {
        $from = !empty($filter['date_from']) ? Carbon::parse($filter['date_from'])->startOfDay() : null;
        $to = !empty($filter['date_to']) ? Carbon::parse($filter['date_to'])->endOfDay() : null;
        $inRange = fn(Carbon $date) => (!$from || $date->gte($from)) && (!$to || $date->lte($to));

        $dispenses = $this->dispenseEvents();
        $this->applyRunningStock($dispenses);

        $dispenses = $dispenses->filter(fn($row) => $inRange($row['dispensed_at']));

        $doctorFees = DoctorFee::query()
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('patient_case_id, SUM(professional_fee) as total')
            ->groupBy('patient_case_id')
            ->pluck('total', 'patient_case_id');

        $caseIds = $dispenses->pluck('patient_case_id')->merge($doctorFees->keys())->unique()->sort()->values();
        $cases = PatientCase::with('patient')->whereIn('id', $caseIds)->get()->keyBy('id');

        $rows = [];
        foreach ($caseIds as $caseId) {
            $case = $cases->get($caseId);
            $caseRows = $dispenses->where('patient_case_id', $caseId)->sortBy('dispensed_at')->values();
            $doctorFee = round((float) ($doctorFees[$caseId] ?? 0), 2);

            if ($caseRows->isEmpty()) {
                $rows[] = $this->reportRow($case, null, $doctorFee, true);
                continue;
            }

            foreach ($caseRows as $index => $dispense) {
                $rows[] = $this->reportRow($case, $dispense, $index === 0 ? $doctorFee : null, $index === 0);
            }
        }

        $totalSold = round(collect($rows)->sum('sold_amount'), 2);
        $totalDoctorFee = round(collect($rows)->sum('doctor_fee'), 2);

        return [
            'rows' => $rows,
            'totals' => [
                'out_pcs' => collect($rows)->sum('out_pcs'),
                'sold_amount' => $totalSold,
                'doctor_fee' => $totalDoctorFee,
                'grand_total' => round($totalSold + $totalDoctorFee, 2),
            ],
        ];
    }

    /**
     * Invoice-level "cash in" per patient case: every invoice raised in the
     * period is merged per case, and its items are split into labelled
     * sections. Professional fees collapse into a single PF line; other
     * sections sum identical descriptions so repeated charges read as one line.
     */
    public function patientInvoice(array $filter = []): array
    {
        $from = !empty($filter['date_from']) ? Carbon::parse($filter['date_from'])->startOfDay() : null;
        $to = !empty($filter['date_to']) ? Carbon::parse($filter['date_to'])->endOfDay() : null;

        $invoices = Invoice::with(['items', 'patientCase.patient', 'patientCase.patientType'])
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to))
            ->when(!empty($filter['status']), fn($q) => $q->where('status', $filter['status']))
            ->when(!empty($filter['case_type']), fn($q) => $q->whereHas('patientCase', fn($c) => $c->where('type', $filter['case_type'])))
            ->orderBy('patient_case_id')
            ->orderBy('id')
            ->get();

        $cases = $invoices->groupBy('patient_case_id')->map(function (Collection $caseInvoices) {
            $case = $caseInvoices->first()->patientCase;
            $items = $caseInvoices->flatMap->items;

            $sections = collect(self::INVOICE_SECTIONS)
                ->map(function ($label, $category) use ($items) {
                    $sectionItems = $items->where('category', $category);
                    if ($sectionItems->isEmpty()) {
                        return null;
                    }

                    $lines = $category === 'Professional Fee'
                        ? collect([['description' => 'PF', 'quantity' => 1, 'amount' => round($sectionItems->sum('subtotal'), 2)]])
                        : $sectionItems->groupBy('description')->map(fn($group, $description) => [
                            'description' => $description,
                            'quantity' => $group->sum('quantity'),
                            'amount' => round($group->sum('subtotal'), 2),
                        ])->values();

                    return [
                        'category' => $category,
                        'label' => $label,
                        'items' => $lines,
                        'subtotal' => round($sectionItems->sum('subtotal'), 2),
                    ];
                })
                ->filter()
                ->values();

            $otherItems = $items->whereNotIn('category', array_keys(self::INVOICE_SECTIONS));
            if ($otherItems->isNotEmpty()) {
                $sections->push([
                    'category' => 'Other',
                    'label' => 'OTHERS',
                    'items' => $otherItems->map(fn($item) => [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'amount' => round($item->subtotal, 2),
                    ])->values(),
                    'subtotal' => round($otherItems->sum('subtotal'), 2),
                ]);
            }

            $patient = $case?->patient;

            return [
                'patient_case_pid' => $case?->pid,
                'case_number' => $case?->case_number,
                'case_type' => $case?->type,
                'case_type_label' => self::CASE_TYPE_LABELS[$case?->type] ?? strtoupper((string) $case?->type),
                'patient_name' => $patient ? trim("{$patient->firstname} {$patient->lastname}") : '—',
                'patient_type' => $case?->patientType?->code,
                'patient_type_name' => $case?->patientType?->name,
                'invoice_numbers' => $caseInvoices->pluck('invoice_number')->values(),
                'sections' => $sections,
                'subtotal' => round($caseInvoices->sum('subtotal'), 2),
                'discount' => round($caseInvoices->sum('discount_amount'), 2),
                'tax' => round($caseInvoices->sum('tax_amount'), 2),
                'total' => round($caseInvoices->sum('total_amount'), 2),
                'paid' => round($caseInvoices->sum('paid_amount'), 2),
                'balance' => round($caseInvoices->sum('balance'), 2),
            ];
        })->values();

        $byType = $cases->groupBy('case_type_label')->map(fn($group, $label) => [
            'label' => $label,
            'total' => round($group->sum('total'), 2),
        ])->values();

        $bySection = $cases->flatMap->sections->groupBy('label')->map(fn($group, $label) => [
            'label' => $label,
            'total' => round($group->sum('subtotal'), 2),
        ])->values();

        return [
            'cases' => $cases,
            'totals' => [
                'by_type' => $byType,
                'by_section' => $bySection,
                'discount' => round($cases->sum('discount'), 2),
                'tax' => round($cases->sum('tax'), 2),
                'grand_total' => round($cases->sum('total'), 2),
                'paid' => round($cases->sum('paid'), 2),
                'balance' => round($cases->sum('balance'), 2),
            ],
        ];
    }

    private function dispenseEvents(): Collection
    {
        $items = PrescriptionItem::with(['medicine', 'prescription'])
            ->whereHas('prescription', fn($q) => $q->whereIn('status', self::DISPENSED_STATUSES))
            ->get();

        $firstOutAt = MedicineStockMovement::whereIn('prescription_item_id', $items->pluck('id'))
            ->where('type', 'OUT')
            ->selectRaw('prescription_item_id, MIN(created_at) as dispensed_at')
            ->groupBy('prescription_item_id')
            ->pluck('dispensed_at', 'prescription_item_id');

        return $items
            ->map(function ($item) use ($firstOutAt) {
                $quantity = (int) round((float) ($item->quantity ?? 0));
                if ($quantity <= 0) {
                    return null;
                }

                return [
                    'prescription_item_id' => $item->id,
                    'patient_case_id' => $item->prescription->patient_case_id,
                    'medicine_id' => $item->medicine_id,
                    'medicine' => $item->medicine->name ?? '—',
                    'out_pcs' => $quantity,
                    'price' => (float) $item->price,
                    'dispensed_at' => Carbon::parse($firstOutAt[$item->id] ?? $item->prescription->prescription_date),
                ];
            })
            ->filter()
            ->values();
    }

    private function applyRunningStock(Collection &$dispenses): void
    {
        $medicineIds = $dispenses->pluck('medicine_id')->unique();

        $currentStock = MedicineStock::whereIn('medicine_id', $medicineIds)
            ->selectRaw('medicine_id, SUM(quantity) as quantity')
            ->groupBy('medicine_id')
            ->pluck('quantity', 'medicine_id');

        $ledger = MedicineStockMovement::whereNull('prescription_item_id')
            ->join('medicine_stocks', 'medicine_stocks.id', '=', 'medicine_stock_movements.medicine_stock_id')
            ->whereIn('medicine_stocks.medicine_id', $medicineIds)
            ->get(['medicine_stocks.medicine_id', 'medicine_stock_movements.type', 'medicine_stock_movements.quantity', 'medicine_stock_movements.created_at'])
            ->map(fn($m) => [
                'medicine_id' => $m->medicine_id,
                // Walking backwards, undoing an OUT adds stock back and undoing an IN removes it.
                'undo' => $m->type === 'OUT' ? (int) $m->quantity : -(int) $m->quantity,
                'at' => Carbon::parse($m->created_at),
                'dispense_key' => null,
            ]);

        $events = $dispenses
            ->map(fn($row, $key) => [
                'medicine_id' => $row['medicine_id'],
                'undo' => $row['out_pcs'],
                'at' => $row['dispensed_at'],
                'dispense_key' => $key,
            ])
            ->merge($ledger)
            ->sort(fn($a, $b) => $b['at'] <=> $a['at'] ?: ($b['dispense_key'] ?? -1) <=> ($a['dispense_key'] ?? -1))
            ->groupBy('medicine_id');

        $dispenses = $dispenses->map(function ($row) {
            return $row + ['stock_before' => 0, 'stock_after' => 0];
        });

        foreach ($events as $medicineId => $medicineEvents) {
            $balance = (int) ($currentStock[$medicineId] ?? 0);

            foreach ($medicineEvents as $event) {
                if ($event['dispense_key'] !== null) {
                    $row = $dispenses[$event['dispense_key']];
                    $row['stock_after'] = $balance;
                    $row['stock_before'] = $balance + $event['undo'];
                    $dispenses[$event['dispense_key']] = $row;
                }
                $balance += $event['undo'];
            }
        }
    }

    private function reportRow(?PatientCase $case, ?array $dispense, ?float $doctorFee, bool $isFirstOfCase): array
    {
        $patient = $case?->patient;
        $patientName = $patient
            ? trim("{$patient->lastname}, {$patient->firstname}" . ($patient->middlename ? ' ' . mb_substr($patient->middlename, 0, 1) . '.' : ''))
            : '—';

        return [
            'patient_case_pid' => $case?->pid,
            'case_number' => $case?->case_number,
            'patient_name' => $patientName,
            'is_first_of_case' => $isFirstOfCase,
            'date' => $dispense ? $dispense['dispensed_at']->format('Y-m-d H:i:s') : null,
            'medicine' => $dispense['medicine'] ?? null,
            'stocks' => $dispense['stock_before'] ?? null,
            'out_pcs' => $dispense['out_pcs'] ?? 0,
            'amount' => $dispense['price'] ?? null,
            'dispense_balance' => $dispense ? round($dispense['stock_after'] * $dispense['price'], 2) : null,
            'doctor_fee' => $doctorFee,
            'sold_amount' => $dispense ? round($dispense['out_pcs'] * $dispense['price'], 2) : 0,
        ];
    }
}
