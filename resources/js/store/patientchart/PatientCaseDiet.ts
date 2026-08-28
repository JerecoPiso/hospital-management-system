import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientCaseDiet } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const usePatientCaseDietStore = defineStore("patientCaseDiet", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientCaseDiets = ref<PatientCaseDiet[]>([]);
    const patientCaseDiet = ref<PatientCaseDiet>({
        patient_case_pid: "",
        diet_pid: "",
        remarks: "",
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    // Accepts either a patient_case_pid string (patient chart usage) or a
    // params object (management "Dietary List" usage with search + pagination).
    const read = async (arg: string | Record<string, any> = {}) => {
        const params = typeof arg === "string" ? { patient_case_pid: arg } : arg;
        const response = await axios.get(`${baseUrl}api/patient-case-diets`, { params });
        patientCaseDiets.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-case-diets/${pid}`);
        patientCaseDiet.value = response.data.data;
    };
    const create = async (data: PatientCaseDiet) => {
        await axios.post(`${baseUrl}api/patient-case-diets`, data);
        await read(data.patient_case_pid);
    };
    const update = async (data: PatientCaseDiet) => {
        await axios.put(`${baseUrl}api/patient-case-diets/${data.pid}`, data);
        await read(data.patient_case_pid);
    };
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/patient-case-diets/${pid}`);
        await read(patient_case_pid ?? {});
    };
    const serve = async (
        pid: string,
        payload: { served_at?: string | null; remarks?: string | null },
        patient_case_pid?: string
    ) => {
        await axios.post(`${baseUrl}api/patient-case-diets/${pid}/serve`, payload);
        await read(patient_case_pid ?? {});
    };

    return {
        patientCaseDiets,
        patientCaseDiet,
        meta,
        read,
        view,
        create,
        update,
        archive,
        serve,
    };
});
