import { defineStore } from "pinia";
import { ref } from "vue";
import { SupplyDistribution } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useSupplyDistributionStore = defineStore("supplyDistribution", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const supplyDistributions = ref<SupplyDistribution[]>([])
    const supplyDistribution = ref<SupplyDistribution>({
        supply_stock_pid: '',
        station_pid: '',
        quantity: 0
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: SupplyDistribution) => {
        await axios.post(`${baseUrl}api/supply-distributions`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/supply-distributions`, { params });
        supplyDistributions.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supply-distributions/${pid}`);
        supplyDistribution.value = response.data.data;
    }
    return {
        create,
        read,
        view,
        supplyDistributions,
        supplyDistribution,
        meta
    }
})
