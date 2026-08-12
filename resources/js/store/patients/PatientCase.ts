import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientCase } from "@/interface/Interfaces";
import axios from "axios";
export const usePatientCaseStore = defineStore("patientCase", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientCase = ref<PatientCase | null>(null);
    const create = async (data: PatientCase) => {
        const response = await axios.post(`${baseUrl}api/patient-cases`, data);
        return response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-cases/${pid}`);
        patientCase.value = response.data.data;
        return patientCase.value;
    }
    return {
        create,
        view,
        patientCase
    }
})
