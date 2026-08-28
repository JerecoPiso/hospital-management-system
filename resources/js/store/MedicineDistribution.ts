import { defineStore } from "pinia";
import { ref } from "vue";
import { MedicineDistribution } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useMedicineDistributionStore = defineStore("medicineDistribution", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const medicineDistributions = ref<MedicineDistribution[]>([])
    const medicineDistribution = ref<MedicineDistribution>({
        medicine_stock_pid: '',
        station_pid: '',
        quantity: 0
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: MedicineDistribution) => {
        await axios.post(`${baseUrl}api/medicine-distributions`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/medicine-distributions`, { params });
        medicineDistributions.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
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
        medicineDistribution,
        meta
    }
})
