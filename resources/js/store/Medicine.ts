import { defineStore } from "pinia";
import { ref } from "vue";
import { Medicines } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useMedicineStore = defineStore("medicine", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
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
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Medicines) => {
        await axios.post(`${baseUrl}api/medicine`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/medicine`, { params });
        medicines.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine/${pid}`);
        medicine.value = response.data.data;
    }
    const update = async (data: Medicines) => {
        await axios.put(`${baseUrl}api/medicine/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/medicine/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        medicines,
        medicine,
        meta
    }
})
