<?php

namespace App\Repositories;

use App\Models\DoctorFee;
use App\Models\FeeChargeItem;
use App\Models\Invoice;
use App\Models\LabRequest;
use App\Models\PatientCase;
use App\Models\PrescriptionItem;
use App\Models\RadiologyOrder;
use App\Models\SupplyChargeItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceRepositories
{
    private array $with = ['patientCase.patient', 'createdBy', 'items', 'payments.receivedBy'];

    public function list($filter = [])
    {
        $query = Invoice::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        return api_list($query, $filter, [
            'invoice_number',
            'status',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $invoice = Invoice::with($this->with)->where('pid', $pid)->first();

            if (!$invoice) {
                return [];
            }

            return $invoice;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Aggregates every not-yet-invoiced, priced charge recorded against the
     * case (lab requests, radiology orders, fee charge items, doctor
     * professional fees, prescription items, supply charge items) into a single new invoice + line items, so
     * billing staff don't have to manually re-enter what's already been
     * ordered/charged elsewhere in the chart. Each source is linked to its
     * invoice item via the polymorphic billable relation so it can't be
     * pulled into a second invoice later.
     */
    public function generate($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();

                $sources = $this->collectBillableItems($patientCase->id);

                if ($sources->isEmpty()) {
                    throw new \Exception('No new billable charges found for this patient case.');
                }

                $subtotal = round((float) $sources->sum('subtotal'), 2);
                $discount = (float) ($data['discount_amount'] ?? 0);
                $tax = (float) ($data['tax_amount'] ?? 0);
                $total = max(round($subtotal - $discount + $tax, 2), 0);

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'patient_case_id' => $patientCase->id,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $tax,
                    'total_amount' => $total,
                    'paid_amount' => 0,
                    'balance' => $total,
                    'status' => 'unpaid',
                    'created_by' => auth()->id(),
                ]);

                foreach ($sources as $source) {
                    $invoice->items()->create($source);
                }

                return $invoice->load($this->with);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function collectBillableItems($patientCaseId): Collection
    {
        $items = collect();

        LabRequest::where('patient_case_id', $patientCaseId)
            ->where('status', '!=', 'cancelled')
            ->whereDoesntHave('invoiceItem')
            ->with('labTest')
            ->get()
            ->each(function ($request) use ($items) {
                $items->push([
                    'billable_type' => LabRequest::class,
                    'billable_id' => $request->id,
                    'category' => 'Laboratory',
                    'description' => $request->labTest->name ?? $request->request_number,
                    'quantity' => 1,
                    'unit_price' => $request->price,
                    'subtotal' => $request->price,
                ]);
            });

        RadiologyOrder::where('patient_case_id', $patientCaseId)
            ->where('status', '!=', 'cancelled')
            ->whereDoesntHave('invoiceItem')
            ->with('procedure')
            ->get()
            ->each(function ($order) use ($items) {
                $items->push([
                    'billable_type' => RadiologyOrder::class,
                    'billable_id' => $order->id,
                    'category' => 'Radiology',
                    'description' => $order->procedure->name ?? $order->order_number,
                    'quantity' => 1,
                    'unit_price' => $order->price,
                    'subtotal' => $order->price,
                ]);
            });

        FeeChargeItem::whereHas('feeCharge.patientCase', fn($q) => $q->where('id', $patientCaseId))
            ->whereDoesntHave('invoiceItem')
            ->with('feeSchedule')
            ->get()
            ->each(function ($item) use ($items) {
                $items->push([
                    'billable_type' => FeeChargeItem::class,
                    'billable_id' => $item->id,
                    'category' => 'Fee',
                    'description' => $item->feeSchedule->name ?? 'Charge',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_fee,
                    'subtotal' => round($item->quantity * $item->unit_fee, 2),
                ]);
            });

        DoctorFee::where('patient_case_id', $patientCaseId)
            ->whereDoesntHave('invoiceItem')
            ->where('professional_fee', '>', 0)
            ->with('doctor')
            ->get()
            ->each(function ($fee) use ($items) {
                $doctorName = trim(($fee->doctor->firstname ?? '') . ' ' . ($fee->doctor->lastname ?? ''));
                $items->push([
                    'billable_type' => DoctorFee::class,
                    'billable_id' => $fee->id,
                    'category' => 'Professional Fee',
                    'description' => $doctorName ? "Professional fee - Dr. {$doctorName}" : 'Professional fee',
                    'quantity' => 1,
                    'unit_price' => $fee->professional_fee,
                    'subtotal' => $fee->professional_fee,
                ]);
            });

        PrescriptionItem::whereHas('prescription', function ($q) use ($patientCaseId) {
            $q->where('patient_case_id', $patientCaseId)->where('status', '!=', 'cancelled');
        })
            ->whereDoesntHave('invoiceItem')
            ->where('price', '>', 0)
            ->with('medicine')
            ->get()
            ->each(function ($item) use ($items) {
                $quantity = $item->quantity ?: 1;
                $items->push([
                    'billable_type' => PrescriptionItem::class,
                    'billable_id' => $item->id,
                    'category' => 'Medicine',
                    'description' => $item->medicine->name ?? 'Prescription item',
                    'quantity' => $quantity,
                    'unit_price' => $item->price,
                    'subtotal' => round($quantity * $item->price, 2),
                ]);
            });

        SupplyChargeItem::whereHas('supplyCharge.patientCase', fn($q) => $q->where('id', $patientCaseId))
            ->whereDoesntHave('invoiceItem')
            ->where('price', '>', 0)
            ->with('supply')
            ->get()
            ->each(function ($item) use ($items) {
                $items->push([
                    'billable_type' => SupplyChargeItem::class,
                    'billable_id' => $item->id,
                    'category' => 'Supply',
                    'description' => $item->supply->name ?? 'Supply item',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'subtotal' => round($item->quantity * $item->price, 2),
                ]);
            });

        return $items;
    }

    /**
     * Records a payment and recalculates paid_amount/balance/status on the
     * invoice; rejects overpayment so balance never goes negative.
     */
    public function pay($invoice_id, $data)
    {
        try {
            return DB::transaction(function () use ($invoice_id, $data) {
                $invoice = Invoice::findOrFail($invoice_id);

                $amount = round((float) $data['amount_paid'], 2);
                if ($amount > (float) $invoice->balance) {
                    throw new \Exception('Payment amount exceeds the remaining balance.');
                }

                $invoice->payments()->create([
                    'receipt_number' => 'OR-' . strtoupper(Str::random(8)),
                    'amount_paid' => $amount,
                    'payment_method' => $data['payment_method'],
                    'reference_number' => $data['reference_number']
                        ?? 'REF-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                    'received_by' => auth()->id(),
                    'paid_at' => !empty($data['paid_at']) ? Carbon::parse($data['paid_at']) : now(),
                ]);

                $paidAmount = round((float) $invoice->paid_amount + $amount, 2);
                $balance = round((float) $invoice->total_amount - $paidAmount, 2);

                $invoice->update([
                    'paid_amount' => $paidAmount,
                    'balance' => max($balance, 0),
                    'status' => $balance <= 0 ? 'paid' : ($paidAmount > 0 ? 'partially_paid' : 'unpaid'),
                ]);

                return $invoice->load($this->with);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
