import { defineStore } from "pinia";
import { ref } from "vue";
import { Supply } from "@/interface/Interfaces";
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
    const create = async (data: Supply) => {
        await axios.post(`${baseUrl}api/supplies`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/supplies`);
        supplies.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supplies/${pid}`);
        supply.value = response.data.data;
    }
    const update = async (data: Supply) => {
        await axios.put(`${baseUrl}api/supplies/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/supplies/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        supplies,
        supply
    }
})
