import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineStockMovement } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useMedicineStockMovementStore = defineStore("medicineStockMovement", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const medicineStockMovements = ref<MedicineStockMovement[]>([])
    const medicineStockMovement = ref<MedicineStockMovement>({
        medicine_pid: '',
        type: 'IN',
        quantity: 0,
        reference: '',
        remarks: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: MedicineStockMovement) => {
        await axios.post(`${baseUrl}api/medicine-stock-movements`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/medicine-stock-movements`, { params });
        medicineStockMovements.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-stock-movements/${pid}`);
        medicineStockMovement.value = response.data.data;
    }
    return {
        create,
        read,
        view,
        medicineStockMovements,
        medicineStockMovement,
        meta
    }
})
