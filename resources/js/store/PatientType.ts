import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientType } from "@/interface/Interfaces";
import axios from "axios";
export const usePatientTypeStore = defineStore("patientType", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientTypes = ref<PatientType[]>([])
    const patientType = ref<PatientType>({
        code: '',
        name: '',
        description: ''
    })
    const create = async (data: PatientType) => {
        await axios.post(`${baseUrl}api/patient-types`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/patient-types`);
        patientTypes.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-types/${pid}`);
        patientType.value = response.data.data;
    }
    const update = async (data: PatientType) => {
        await axios.put(`${baseUrl}api/patient-types/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/patient-types/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        patientTypes,
        patientType
    }
})
