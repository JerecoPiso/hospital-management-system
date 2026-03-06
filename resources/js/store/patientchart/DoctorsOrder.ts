import { defineStore } from "pinia";
import { ref } from "vue";
import { DoctorsOrder } from "@/interface/Interfaces";
import axios from "axios";
export const useDoctorsOrderStore = defineStore("doctorsOrder", () => {
    const baseUrl = import.meta.env.VITE_APP_URL;
    const doctorsOrders = ref<DoctorsOrder[]>([])
    const doctorsOrder = ref<DoctorsOrder>({
        pid: "",
        order: "",
        progress_notes: ""
    })
    const create = async (data: DoctorsOrder) => {
        await axios.post(`${baseUrl}api/doctors-order`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/doctors-order`);
        doctorsOrders.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/doctors-order/${pid}`);
        doctorsOrder.value = response.data.data;
    }
    const update = async (data: DoctorsOrder) => {
        await axios.put(`${baseUrl}api/doctors-order/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/doctors-order/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        doctorsOrders,
        doctorsOrder
    }
})