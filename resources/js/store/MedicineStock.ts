import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineStock } from "@/interface/Interfaces";
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
    const create = async (data: MedicineStock) => {
        await axios.post(`${baseUrl}api/medicine-stocks`, data);
        read();
    }
    const read = async (medicine_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-stocks`, {
            params: medicine_pid ? { medicine_pid } : {}
        });
        medicineStocks.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-stocks/${pid}`);
        medicineStock.value = response.data.data;
    }
    const update = async (data: MedicineStock) => {
        await axios.put(`${baseUrl}api/medicine-stocks/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/medicine-stocks/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        medicineStocks,
        medicineStock
    }
})
