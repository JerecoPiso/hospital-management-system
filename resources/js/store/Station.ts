import { defineStore } from "pinia";
import { ref } from "vue";
import { Station } from "@/interface/Interfaces";
import axios from "axios";
export const useStationStore = defineStore("station", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const stations = ref<Station[]>([])
    const station = ref<Station>({
        ward_pid: '',
        name: '',
        description: ''
    })
    const create = async (data: Station) => {
        await axios.post(`${baseUrl}api/stations`, data);
        read();
    }
    const read = async (ward_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/stations`, {
            params: ward_pid ? { ward_pid } : {}
        });
        stations.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/stations/${pid}`);
        station.value = response.data.data;
    }
    const update = async (data: Station) => {
        await axios.put(`${baseUrl}api/stations/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/stations/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        stations,
        station
    }
})
