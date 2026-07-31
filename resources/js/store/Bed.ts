import { defineStore } from "pinia";
import { ref } from "vue";
import { Bed } from "@/interface/Interfaces";
import axios from "axios";
export const useBedStore = defineStore("bed", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const beds = ref<Bed[]>([])
    const bed = ref<Bed>({
        room_pid: '',
        bed_number: '',
        status: 'available'
    })
    const create = async (data: Bed) => {
        await axios.post(`${baseUrl}api/beds`, data);
        read();
    }
    const read = async (room_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/beds`, {
            params: room_pid ? { room_pid } : {}
        });
        beds.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/beds/${pid}`);
        bed.value = response.data.data;
    }
    const update = async (data: Bed) => {
        await axios.put(`${baseUrl}api/beds/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/beds/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        beds,
        bed
    }
})
