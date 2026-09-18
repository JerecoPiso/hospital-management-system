import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientCaseDischarge } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const usePatientCaseDischargeStore = defineStore("patientCaseDischarge", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientCaseDischarges = ref<PatientCaseDischarge[]>([]);
    const patientCaseDischarge = ref<PatientCaseDischarge>({
        patient_case_pid: "",
        discharge_datetime: "",
        disposition: "",
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    // Accepts either a patient_case_pid string (patient chart usage) or a
    // params object (management usage with search + pagination).
    const read = async (arg: string | Record<string, any> = {}) => {
        const params = typeof arg === "string" ? { patient_case_pid: arg } : arg;
        const response = await axios.get(`${baseUrl}api/patient-case-discharges`, { params });
        patientCaseDischarges.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-case-discharges/${pid}`);
        patientCaseDischarge.value = response.data.data;
    };
    const create = async (data: PatientCaseDischarge) => {
        await axios.post(`${baseUrl}api/patient-case-discharges`, data);
        await read(data.patient_case_pid);
    };
    const update = async (data: PatientCaseDischarge) => {
        await axios.put(`${baseUrl}api/patient-case-discharges/${data.pid}`, data);
        await read(data.patient_case_pid);
    };
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/patient-case-discharges/${pid}`);
        await read(patient_case_pid ?? {});
    };

    return {
        patientCaseDischarges,
        patientCaseDischarge,
        meta,
        read,
        view,
        create,
        update,
        archive,
    };
});
