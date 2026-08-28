import { defineStore } from "pinia";
import { ref } from "vue";
import { Floor } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useFloorStore = defineStore("floor", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const floors = ref<Floor[]>([])
    const floor = ref<Floor>({
        building_pid: '',
        floor_number: '',
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Floor) => {
        await axios.post(`${baseUrl}api/floors`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/floors`, { params });
        floors.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/floors/${pid}`);
        floor.value = response.data.data;
    }
    const update = async (data: Floor) => {
        await axios.put(`${baseUrl}api/floors/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/floors/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        floors,
        floor,
        meta
    }
})
