import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineStock } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useMedicineStockStore = defineStore("medicineStock", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const medicineStocks = ref<MedicineStock[]>([])
    const medicineStock = ref<MedicineStock>({
        medicine_pid: '',
        quantity: 0,
        purchase_price: null,
        reorder_level: 100,
        unit_type: 'box',
        units_per_package: 1,
        expiration_date: null,
        batch_number: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: MedicineStock) => {
        await axios.post(`${baseUrl}api/medicine-stocks`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/medicine-stocks`, { params });
        medicineStocks.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-stocks/${pid}`);
        medicineStock.value = response.data.data;
    }
    const update = async (data: MedicineStock) => {
        await axios.put(`${baseUrl}api/medicine-stocks/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/medicine-stocks/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        medicineStocks,
        medicineStock,
        meta
    }
})
