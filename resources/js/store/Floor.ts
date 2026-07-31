import { defineStore } from "pinia";
import { ref } from "vue";
import { Floor } from "@/interface/Interfaces";
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
    const create = async (data: Floor) => {
        await axios.post(`${baseUrl}api/floors`, data);
        read();
    }
    const read = async (building_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/floors`, {
            params: building_pid ? { building_pid } : {}
        });
        floors.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/floors/${pid}`);
        floor.value = response.data.data;
    }
    const update = async (data: Floor) => {
        await axios.put(`${baseUrl}api/floors/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/floors/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        floors,
        floor
    }
})
