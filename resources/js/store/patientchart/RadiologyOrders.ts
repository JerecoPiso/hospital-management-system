import { defineStore } from "pinia";
import { ref } from "vue";
import { RadiologyOrder } from "@/interface/Interfaces";
import axios from "axios";
export const useRadiologyOrderStore = defineStore("radiologyOrder", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const radiologyOrders = ref<RadiologyOrder[]>([])
    const radiologyOrder = ref<RadiologyOrder>({
        patient_case_pid: "",
        procedure_pid: "",
        priority: "routine",
        clinical_history: "",
        scheduled_at: null
    })
    const create = async (data: RadiologyOrder) => {
        await axios.post(`${baseUrl}api/radiology-orders`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/radiology-orders`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        radiologyOrders.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/radiology-orders/${pid}`);
        radiologyOrder.value = response.data.data;
    }
    const updateStatus = async (pid: string, data: Record<string, any>) => {
        await axios.put(`${baseUrl}api/radiology-orders/${pid}/status`, data);
    }
    const saveReport = async (pid: string, data: { findings: string; impression: string; status?: string }) => {
        await axios.put(`${baseUrl}api/radiology-orders/${pid}/report`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/radiology-orders/${pid}`);
    }
    return {
        create,
        read,
        view,
        updateStatus,
        saveReport,
        archive,
        radiologyOrders,
        radiologyOrder
    }
})
