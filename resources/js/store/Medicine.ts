import { defineStore } from "pinia";
import { ref } from "vue";
import { Medicines } from "@/interface/Interfaces";
import axios from "axios";
export const useMedicineStore = defineStore("medicine", () => {
    const baseUrl = import.meta.env.VITE_APP_URL;
    const medicines = ref<Medicines[]>([])
    const medicine = ref<Medicines>({
        name: '',
        generic_name: '',
        brand_name: '',
        dosage: 0,
        dosage_unit: '',
        form: '',
        administration_route: '',
        price: 0
    })
    const create = async (data: Medicines) => {
        await axios.post(`${baseUrl}api/medicine`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/medicine`);
        medicines.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine/${pid}`);
        medicine.value = response.data.data;
    }
    const update = async (data: Medicines) => {
        await axios.put(`${baseUrl}api/medicine/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/medicine/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        medicines,
        medicine
    }
})