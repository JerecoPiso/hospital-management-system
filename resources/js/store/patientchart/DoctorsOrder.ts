import { defineStore } from "pinia";
import { ref } from "vue";
import { DoctorsOrder } from "@/interface/Interfaces";
import axios from "axios";
export const useDoctorsOrderStore = defineStore("doctorsOrder", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const doctorsOrders = ref<DoctorsOrder[]>([])
    const doctorsOrder = ref<DoctorsOrder>({
        pid: "",
        patient_case_pid: "",
        order: "",
        progress_notes: ""
    })
    const create = async (data: DoctorsOrder) => {
        await axios.post(`${baseUrl}api/doctors-order`, data);
        read(data.patient_case_pid);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/doctors-order`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        doctorsOrders.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/doctors-order/${pid}`);
        doctorsOrder.value = response.data.data;
    }
    const update = async (data: DoctorsOrder) => {
        await axios.put(`${baseUrl}api/doctors-order/${data.pid}`, data);
        read(data.patient_case_pid);
    }
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/doctors-order/${pid}`);
        read(patient_case_pid);
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