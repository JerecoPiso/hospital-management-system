import { defineStore } from "pinia";
import { ref } from "vue";
import { SupplyMovement } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useSupplyMovementStore = defineStore("supplyMovement", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const supplyMovements = ref<SupplyMovement[]>([])
    const supplyMovement = ref<SupplyMovement>({
        supply_stock_pid: '',
        quantity: 0,
        type: 'IN',
        used_for: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: SupplyMovement) => {
        await axios.post(`${baseUrl}api/supply-movements`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/supply-movements`, { params });
        supplyMovements.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supply-movements/${pid}`);
        supplyMovement.value = response.data.data;
    }
    return {
        create,
        read,
        view,
        supplyMovements,
        supplyMovement,
        meta
    }
})
