import { defineStore } from "pinia";
import { ref } from "vue";
import { RadiologyModality } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useRadiologyModalityStore = defineStore("radiologyModality", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const modalities = ref<RadiologyModality[]>([])
    const modality = ref<RadiologyModality>({
        code: '',
        name: '',
        room_number: '',
        is_active: true
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: RadiologyModality) => {
        await axios.post(`${baseUrl}api/radiology-modalities`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/radiology-modalities`, { params });
        modalities.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/radiology-modalities/${pid}`);
        modality.value = response.data.data;
    }
    const update = async (data: RadiologyModality) => {
        await axios.put(`${baseUrl}api/radiology-modalities/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/radiology-modalities/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        modalities,
        modality,
        meta
    }
})
