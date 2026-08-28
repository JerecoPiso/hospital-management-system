import { defineStore } from "pinia";
import { ref } from "vue";
import { Supply } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useSupplyStore = defineStore("supply", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const supplies = ref<Supply[]>([])
    const supply = ref<Supply>({
        name: '',
        unit: '',
        selling_price: null,
        is_active: true
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Supply) => {
        await axios.post(`${baseUrl}api/supplies`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/supplies`, { params });
        supplies.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supplies/${pid}`);
        supply.value = response.data.data;
    }
    const update = async (data: Supply) => {
        await axios.put(`${baseUrl}api/supplies/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/supplies/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        supplies,
        supply,
        meta
    }
})
