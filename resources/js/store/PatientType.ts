import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientType } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const usePatientTypeStore = defineStore("patientType", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patientTypes = ref<PatientType[]>([])
    const patientType = ref<PatientType>({
        code: '',
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: PatientType) => {
        await axios.post(`${baseUrl}api/patient-types`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/patient-types`, { params });
        patientTypes.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-types/${pid}`);
        patientType.value = response.data.data;
    }
    const update = async (data: PatientType) => {
        await axios.put(`${baseUrl}api/patient-types/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/patient-types/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        patientTypes,
        patientType,
        meta
    }
})
