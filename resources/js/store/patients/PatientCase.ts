import { defineStore } from "pinia";
import { PatientCase } from "@/interface/Interfaces";
import axios from "axios";
export const usePatientCaseStore = defineStore("patientCase", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const create = async (data: PatientCase) => {
        const response = await axios.post(`${baseUrl}api/patient-cases`, data);
        return response.data.data;
    }
    return {
        create
    }
})
