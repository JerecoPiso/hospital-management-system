import { defineStore } from "pinia";
import { ref } from "vue";
import { RadiologyOrder } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const useRadiologyOrderWorklistStore = defineStore("radiologyOrderWorklist", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const radiologyOrders = ref<RadiologyOrder[]>([]);
    const radiologyOrder = ref<RadiologyOrder | null>(null);
    const meta = ref<ApiTableMeta>(emptyMeta());

    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/radiology-orders`, { params });
        radiologyOrders.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/radiology-orders/${pid}`);
        radiologyOrder.value = response.data.data;
    };
    const updateStatus = async (pid: string, data: Record<string, any>) => {
        await axios.put(`${baseUrl}api/radiology-orders/${pid}/status`, data);
    };
    const saveReport = async (pid: string, data: { findings: string; impression: string; status?: string }) => {
        await axios.put(`${baseUrl}api/radiology-orders/${pid}/report`, data);
    };

    return { radiologyOrders, radiologyOrder, meta, read, view, updateStatus, saveReport };
});
