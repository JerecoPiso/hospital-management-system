import { defineStore } from "pinia";
import { ref } from "vue";
import { PertinentSignsAndSymptoms } from "@/interface/Interfaces";
import axios from "axios";

export const usePertinentSignsAndSymptomsStore = defineStore("pertinentSignsAndSymptoms", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const records = ref<PertinentSignsAndSymptoms[]>([]);
    const record = ref<PertinentSignsAndSymptoms>({
        pid: "",
        patient_case_pid: "",
        values: "",
        pain: "",
        others: "",
        remarks: "",
    });

    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/pertinent-signs-and-symptoms`, {
            params: patient_case_pid ? { patient_case_pid } : {},
        });
        records.value = response.data.data;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/pertinent-signs-and-symptoms/${pid}`);
        record.value = response.data.data;
    };
    const create = async (data: PertinentSignsAndSymptoms) => {
        await axios.post(`${baseUrl}api/pertinent-signs-and-symptoms`, data);
        await read(data.patient_case_pid);
    };
    const update = async (data: PertinentSignsAndSymptoms) => {
        await axios.put(`${baseUrl}api/pertinent-signs-and-symptoms/${data.pid}`, data);
        await read(data.patient_case_pid);
    };
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/pertinent-signs-and-symptoms/${pid}`);
        await read(patient_case_pid);
    };

    return { records, record, read, view, create, update, archive };
});
