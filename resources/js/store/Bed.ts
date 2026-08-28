import { defineStore } from "pinia";
import { ref } from "vue";
import { Bed } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useBedStore = defineStore("bed", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const beds = ref<Bed[]>([])
    const bed = ref<Bed>({
        room_pid: '',
        bed_number: '',
        status: 'available'
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Bed) => {
        await axios.post(`${baseUrl}api/beds`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/beds`, { params });
        beds.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/beds/${pid}`);
        bed.value = response.data.data;
    }
    const update = async (data: Bed) => {
        await axios.put(`${baseUrl}api/beds/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/beds/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        beds,
        bed,
        meta
    }
})
