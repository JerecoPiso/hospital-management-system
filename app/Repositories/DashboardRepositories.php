<?php

namespace App\Repositories;

use App\Models\Bed;
use App\Models\MedicineStock;
use App\Models\Patient;
use App\Models\PatientCase;
use App\Models\SupplyStock;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepositories
{
    private const NEAR_EXPIRY_DAYS = 30;

    public function getStats()
    {
        $startOfThisMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $patientsThisMonth = Patient::where('created_at', '>=', $startOfThisMonth)->count();
        $patientsLastMonth = Patient::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $admissionsThisMonth = PatientCase::where('admission_datetime', '>=', $startOfThisMonth)->count();
        $admissionsLastMonth = PatientCase::whereBetween('admission_datetime', [$startOfLastMonth, $endOfLastMonth])->count();

        $bedsTotal = Bed::count();
        $bedsOccupied = Bed::where('status', 'occupied')->count();

        $lowStockMedicine = MedicineStock::whereColumn('quantity', '<=', 'reorder_level')->count();
        $lowStockSupply = SupplyStock::whereColumn('quantity', '<=', 'reorder_level')->count();

        return [
            'total_patients' => Patient::count(),
            'total_patients_change' => $this->percentChange($patientsLastMonth, $patientsThisMonth),
            'total_admissions' => $admissionsThisMonth,
            'total_admissions_change' => $this->percentChange($admissionsLastMonth, $admissionsThisMonth),
            'beds_total' => $bedsTotal,
            'beds_occupied' => $bedsOccupied,
            'bed_occupancy_rate' => $bedsTotal > 0 ? round(($bedsOccupied / $bedsTotal) * 100, 1) : 0,
            'low_stock_count' => $lowStockMedicine + $lowStockSupply,
        ];
    }

    public function getWeeklyAdmissions()
    {
        $days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = PatientCase::whereDate('admission_datetime', $date->toDateString())->count();
            $days->push([
                'day' => $date->format('D'),
                'date' => $date->toDateString(),
                'count' => $count,
            ]);
        }

        return $days->toArray();
    }

    public function getPatientTypeDistribution()
    {
        $total = PatientCase::count();

        if ($total === 0) {
            return [];
        }

        $rows = PatientCase::query()
            ->join('patient_types', 'patient_types.id', '=', 'patient_cases.patient_type_id')
            ->select('patient_types.name as name', DB::raw('count(*) as total'))
            ->groupBy('patient_types.name')
            ->orderByDesc('total')
            ->get();

        return $rows->map(function ($row) use ($total) {
            return [
                'name' => $row->name,
                'count' => (int) $row->total,
                'percentage' => round(($row->total / $total) * 100, 1),
            ];
        })->toArray();
    }

    public function getRecentAdmissions($limit = 5)
    {
        $cases = PatientCase::with('patient')
            ->orderByDesc('admission_datetime')
            ->limit($limit)
            ->get();

        return $cases->map(function ($case) {
            $patient = $case->patient;

            return [
                'pid' => $case->pid,
                'case_number' => $case->case_number,
                'patient_name' => $patient ? trim($patient->firstname . ' ' . $patient->lastname) : 'Unknown',
                'medical_record_number' => $patient->medical_record_number ?? null,
                'chief_complaint' => $case->chief_complaint,
                'admission_datetime' => $case->admission_datetime,
            ];
        })->toArray();
    }

    public function getRecentUsers($limit = 5)
    {
        return User::orderByDesc('created_at')
            ->limit($limit)
            ->get(['firstname', 'lastname', 'email', 'created_at'])
            ->map(function ($user) {
                return [
                    'name' => trim($user->firstname . ' ' . $user->lastname),
                    'email' => $user->email,
                    'joined_at' => $user->created_at,
                ];
            })->toArray();
    }

    /**
     * Reorder and near-expiry alerts per stock batch. Reorder uses the same rule
     * as the Low Stock Alerts stat (quantity at or below the batch's reorder
     * level). Near expiry covers batches that still hold stock and expire within
     * NEAR_EXPIRY_DAYS, including ones already expired. Each side is only
     * returned to users allowed to view that stock module.
     */
    public function getInventoryAlerts()
    {
        $user = auth()->user();
        $canMedicine = $user?->hasPermission('medicine-stocks', 'view') ?? false;
        $canSupply = $user?->hasPermission('supply-stocks', 'view') ?? false;

        return [
            'near_expiry_days' => self::NEAR_EXPIRY_DAYS,
            'medicine_reorder' => $canMedicine ? $this->reorderAlerts(MedicineStock::class, 'medicine') : null,
            'supply_reorder' => $canSupply ? $this->reorderAlerts(SupplyStock::class, 'supply') : null,
            'medicine_near_expiry' => $canMedicine ? $this->nearExpiryAlerts(MedicineStock::class, 'medicine') : null,
            'supply_near_expiry' => $canSupply ? $this->nearExpiryAlerts(SupplyStock::class, 'supply') : null,
        ];
    }

    private function reorderAlerts(string $stockModel, string $itemRelation): array
    {
        return $stockModel::with($itemRelation)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderByRaw('quantity - reorder_level')
            ->get()
            ->map(fn($stock) => [
                'pid' => $stock->pid,
                'name' => $stock->{$itemRelation}->name ?? '—',
                'batch_number' => $stock->batch_number,
                'unit_type' => $stock->unit_type,
                'quantity' => (int) $stock->quantity,
                'reorder_level' => (int) $stock->reorder_level,
            ])
            ->all();
    }

    private function nearExpiryAlerts(string $stockModel, string $itemRelation): array
    {
        $today = Carbon::today();

        return $stockModel::with($itemRelation)
            ->where('quantity', '>', 0)
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', $today->copy()->addDays(self::NEAR_EXPIRY_DAYS))
            ->orderBy('expiration_date')
            ->get()
            ->map(fn($stock) => [
                'pid' => $stock->pid,
                'name' => $stock->{$itemRelation}->name ?? '—',
                'batch_number' => $stock->batch_number,
                'unit_type' => $stock->unit_type,
                'quantity' => (int) $stock->quantity,
                'expiration_date' => Carbon::parse($stock->expiration_date)->toDateString(),
                'days_left' => (int) $today->diffInDays(Carbon::parse($stock->expiration_date)->startOfDay(), false),
            ])
            ->all();
    }

    public function getSummary()
    {
        return [
            'stats' => $this->getStats(),
            'weekly_admissions' => $this->getWeeklyAdmissions(),
            'patient_type_distribution' => $this->getPatientTypeDistribution(),
            'recent_admissions' => $this->getRecentAdmissions(),
            'recent_users' => $this->getRecentUsers(),
            'inventory_alerts' => $this->getInventoryAlerts(),
        ];
    }

    private function percentChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
