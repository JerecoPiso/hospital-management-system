import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineDistribution } from "@/interface/Interfaces";
import axios from "axios";
export const useMedicineDistributionStore = defineStore("medicineDistribution", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const medicineDistributions = ref<MedicineDistribution[]>([])
    const medicineDistribution = ref<MedicineDistribution>({
        medicine_stock_pid: '',
        station_pid: '',
        quantity: 0
    })
    const create = async (data: MedicineDistribution) => {
        await axios.post(`${baseUrl}api/medicine-distributions`, data);
        read();
    }
    const read = async (medicine_stock_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-distributions`, {
            params: medicine_stock_pid ? { medicine_stock_pid } : {}
        });
        medicineDistributions.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/medicine-distributions/${pid}`);
        medicineDistribution.value = response.data.data;
    }
    return {
        create,
        read,
        view,
        medicineDistributions,
        medicineDistribution
    }
})
