import { defineStore } from "pinia";
import { ref } from "vue";
import { Ward } from "@/interface/Interfaces";
import axios from "axios";
export const useWardStore = defineStore("ward", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const wards = ref<Ward[]>([])
    const ward = ref<Ward>({
        floor_pid: '',
        code: '',
        name: ''
    })
    const create = async (data: Ward) => {
        await axios.post(`${baseUrl}api/wards`, data);
        read();
    }
    const read = async (floor_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/wards`, {
            params: floor_pid ? { floor_pid } : {}
        });
        wards.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/wards/${pid}`);
        ward.value = response.data.data;
    }
    const update = async (data: Ward) => {
        await axios.put(`${baseUrl}api/wards/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/wards/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        wards,
        ward
    }
})
