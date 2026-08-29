import { defineStore } from "pinia";
import { ref } from "vue";
import { Soap } from "@/interface/Interfaces";
import axios from "axios";

export const useSoapStore = defineStore("soap", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const soaps = ref<Soap[]>([]);
    const soap = ref<Soap>({
        pid: "",
        patient_case_pid: "",
        icd_pid: "",
        subjective: "",
        objective: "",
        assessment: "",
        plan: "",
        remarks: "",
    });

    const create = async (data: Soap) => {
        await axios.post(`${baseUrl}api/soaps`, data);
        read(data.patient_case_pid);
    };
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/soaps`, {
            params: patient_case_pid ? { patient_case_pid } : {},
        });
        soaps.value = response.data.data;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/soaps/${pid}`);
        soap.value = response.data.data;
    };
    const update = async (data: Soap) => {
        await axios.put(`${baseUrl}api/soaps/${data.pid}`, data);
        read(data.patient_case_pid);
    };
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/soaps/${pid}`);
        read(patient_case_pid);
    };

    return { archive, create, read, view, update, soaps, soap };
});
