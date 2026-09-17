import { defineStore } from "pinia";
import { ref } from "vue";
import { RadiologyProcedure } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useRadiologyProcedureStore = defineStore("radiologyProcedure", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const procedures = ref<RadiologyProcedure[]>([])
    const procedure = ref<RadiologyProcedure>({
        modality_pid: '',
        code: '',
        name: '',
        body_part: '',
        price: 0,
        estimated_duration_minutes: 30
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: RadiologyProcedure) => {
        await axios.post(`${baseUrl}api/radiology-procedures`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/radiology-procedures`, { params });
        procedures.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/radiology-procedures/${pid}`);
        procedure.value = response.data.data;
    }
    const update = async (data: RadiologyProcedure) => {
        await axios.put(`${baseUrl}api/radiology-procedures/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/radiology-procedures/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        procedures,
        procedure,
        meta
    }
})
