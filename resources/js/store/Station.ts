import { defineStore } from "pinia";
import { ref } from "vue";
import { Station } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useStationStore = defineStore("station", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const stations = ref<Station[]>([])
    const station = ref<Station>({
        ward_pid: '',
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Station) => {
        await axios.post(`${baseUrl}api/stations`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/stations`, { params });
        stations.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/stations/${pid}`);
        station.value = response.data.data;
    }
    const update = async (data: Station) => {
        await axios.put(`${baseUrl}api/stations/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/stations/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        stations,
        station,
        meta
    }
})
