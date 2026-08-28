import { defineStore } from "pinia";
import { ref } from "vue";
import { Building } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useBuildingStore = defineStore("building", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const buildings = ref<Building[]>([])
    const building = ref<Building>({
        code: '',
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Building) => {
        await axios.post(`${baseUrl}api/buildings`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/buildings`, { params });
        buildings.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/buildings/${pid}`);
        building.value = response.data.data;
    }
    const update = async (data: Building) => {
        await axios.put(`${baseUrl}api/buildings/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/buildings/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        buildings,
        building,
        meta
    }
})
