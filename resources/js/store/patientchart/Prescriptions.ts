import { defineStore } from "pinia";
import { ref } from "vue";
import { Prescription } from "@/interface/Interfaces";
import axios from "axios";
export const usePrescriptionStore = defineStore("prescription", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const prescriptions = ref<Prescription[]>([])
    const prescription = ref<Prescription>({
        patient_case_pid: "",
        prescription_date: "",
        remarks: "",
        status: "requested",
        items: []
    })
    const create = async (data: Prescription) => {
        await axios.post(`${baseUrl}api/prescriptions`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/prescriptions`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        prescriptions.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/prescriptions/${pid}`);
        prescription.value = response.data.data;
    }
    const update = async (data: Prescription) => {
        await axios.put(`${baseUrl}api/prescriptions/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/prescriptions/${pid}`);
    }
    return {
        create,
        read,
        view,
        update,
        archive,
        prescriptions,
        prescription
    }
})
