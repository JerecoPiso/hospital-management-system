import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientCaseBed } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const usePatientCaseBedStore = defineStore("patientCaseBed", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientCaseBeds = ref<PatientCaseBed[]>([]);
    const patientCaseBed = ref<PatientCaseBed>({
        patient_case_pid: "",
        bed_pid: "",
        started_at: "",
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    // Accepts either a patient_case_pid string (patient chart usage) or a
    // params object (management usage with search + pagination).
    const read = async (arg: string | Record<string, any> = {}) => {
        const params = typeof arg === "string" ? { patient_case_pid: arg } : arg;
        const response = await axios.get(`${baseUrl}api/patient-case-beds`, { params });
        patientCaseBeds.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-case-beds/${pid}`);
        patientCaseBed.value = response.data.data;
    };
    const create = async (data: PatientCaseBed) => {
        await axios.post(`${baseUrl}api/patient-case-beds`, data);
        await read(data.patient_case_pid);
    };
    const update = async (data: PatientCaseBed) => {
        await axios.put(`${baseUrl}api/patient-case-beds/${data.pid}`, data);
        await read(data.patient_case_pid);
    };
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/patient-case-beds/${pid}`);
        await read(patient_case_pid ?? {});
    };

    return {
        patientCaseBeds,
        patientCaseBed,
        meta,
        read,
        view,
        create,
        update,
        archive,
    };
});
