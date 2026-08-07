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

    public function getSummary()
    {
        return [
            'stats' => $this->getStats(),
            'weekly_admissions' => $this->getWeeklyAdmissions(),
            'patient_type_distribution' => $this->getPatientTypeDistribution(),
            'recent_admissions' => $this->getRecentAdmissions(),
            'recent_users' => $this->getRecentUsers(),
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
