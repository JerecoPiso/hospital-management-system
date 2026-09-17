<?php

namespace App\Repositories;

use App\Models\PatientCase;
use App\Models\RadiologyOrder;
use App\Models\RadiologyProcedure;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RadiologyOrderRepositories
{
    private array $with = ['patientCase.patient', 'doctor', 'procedure.modality', 'technician', 'report.radiologist'];

    public function list($filter = [])
    {
        $query = RadiologyOrder::with($this->with)->orderBy('id', 'desc');

        if (!empty($filter['patient_case_pid'])) {
            $query->whereHas('patientCase', function ($q) use ($filter) {
                $q->where('pid', $filter['patient_case_pid']);
            });
        }

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (!empty($filter['priority'])) {
            $query->where('priority', $filter['priority']);
        }


        return api_list($query, $filter, [
            'order_number',
            'status',
            'priority',
            'procedure.name',
            'procedure.code',
            'patientCase.case_number',
            'patientCase.patient.firstname',
            'patientCase.patient.lastname',
        ]);
    }

    public function searchByPid($pid)
    {
        try {
            $order = RadiologyOrder::with($this->with)->where('pid', $pid)->first();

            if (!$order) {
                return [];
            }

            return $order;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $patientCase = PatientCase::where('pid', $data['patient_case_pid'])->firstOrFail();
            $procedure = RadiologyProcedure::where('pid', $data['procedure_pid'])->firstOrFail();

            $order = RadiologyOrder::create([
                'order_number' => 'RAD-' . strtoupper(Str::random(8)),
                'patient_case_id' => $patientCase->id,
                'doctor_id' => $data['doctor_id'],
                'procedure_id' => $procedure->id,
                'price' => $procedure->price,
                'status' => 'ordered',
                'priority' => $data['priority'] ?? 'routine',
                'clinical_history' => $data['clinical_history'] ?? null,
                'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            ]);

            return $order->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function updateStatus($order_id, $data)
    {
        try {
            $order = RadiologyOrder::findOrFail($order_id);

            $update = ['status' => $data['status']];

            if (!empty($data['scheduled_at'])) {
                $update['scheduled_at'] = Carbon::parse($data['scheduled_at']);
            }

            if (!empty($data['performed_at'])) {
                $update['performed_at'] = Carbon::parse($data['performed_at']);
            }

            if (!empty($data['technician_pid'])) {
                $update['technician_id'] = User::where('pid', $data['technician_pid'])->firstOrFail()->id;
            }

            $order->update($update);

            return $order->load($this->with);
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * Finalizing a report also marks the parent order completed, since a
     * finalized radiology report is the terminal state of an order.
     */
    public function saveReport($order_id, $data)
    {
        try {
            return DB::transaction(function () use ($order_id, $data) {
                $order = RadiologyOrder::findOrFail($order_id);
                $status = $data['status'] ?? 'draft';

                $order->report()->updateOrCreate(
                    ['radiology_order_id' => $order->id],
                    [
                        'radiologist_id' => auth()->id(),
                        'findings' => $data['findings'],
                        'impression' => $data['impression'],
                        'status' => $status,
                        'finalized_at' => $status === 'finalized' ? now() : null,
                    ]
                );

                if ($status === 'finalized') {
                    $order->update(['status' => 'completed']);
                }

                return $order->load($this->with);
            });
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->report()->delete();
            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
