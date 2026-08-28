import { defineStore } from "pinia";
import { ref } from "vue";
import { SupplyStock } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useSupplyStockStore = defineStore("supplyStock", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const supplyStocks = ref<SupplyStock[]>([])
    const supplyStock = ref<SupplyStock>({
        supply_pid: '',
        quantity: 0,
        purchase_price: null,
        reorder_level: 100,
        unit_type: 'box',
        units_per_package: 1,
        expiration_date: null,
        batch_number: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: SupplyStock) => {
        await axios.post(`${baseUrl}api/supply-stocks`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/supply-stocks`, { params });
        supplyStocks.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supply-stocks/${pid}`);
        supplyStock.value = response.data.data;
    }
    const update = async (data: SupplyStock) => {
        await axios.put(`${baseUrl}api/supply-stocks/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/supply-stocks/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        supplyStocks,
        supplyStock,
        meta
    }
})
