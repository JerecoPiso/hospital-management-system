import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineStockMovement } from "@/interface/Interfaces";
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
    const create = async (data: MedicineStockMovement) => {
        await axios.post(`${baseUrl}api/medicine-stock-movements`, data);
        read();
    }
    const read = async (medicine_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-stock-movements`, {
            params: medicine_pid ? { medicine_pid } : {}
        });
        medicineStockMovements.value = response.data.data;
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
        medicineStockMovement
    }
})
