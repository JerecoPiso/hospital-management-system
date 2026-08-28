import { defineStore } from "pinia";
import { ref } from "vue";
import { Ward } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useWardStore = defineStore("ward", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const wards = ref<Ward[]>([])
    const ward = ref<Ward>({
        floor_pid: '',
        code: '',
        name: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Ward) => {
        await axios.post(`${baseUrl}api/wards`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/wards`, { params });
        wards.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/wards/${pid}`);
        ward.value = response.data.data;
    }
    const update = async (data: Ward) => {
        await axios.put(`${baseUrl}api/wards/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/wards/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        wards,
        ward,
        meta
    }
})
