import { defineStore } from "pinia";
import { ref } from "vue";
import { SupplyCharge } from "@/interface/Interfaces";
import axios from "axios";
export const useSupplyChargeStore = defineStore("supplyCharge", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const supplyCharges = ref<SupplyCharge[]>([])
    const supplyCharge = ref<SupplyCharge>({
        patient_case_pid: "",
        charge_date: "",
        remarks: "",
        items: []
    })
    const create = async (data: SupplyCharge) => {
        await axios.post(`${baseUrl}api/supply-charges`, data);
    }
    const update = async (data: SupplyCharge) => {
        await axios.put(`${baseUrl}api/supply-charges/${data.pid}`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/supply-charges`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        supplyCharges.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/supply-charges/${pid}`);
        supplyCharge.value = response.data.data;
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/supply-charges/${pid}`);
    }
    return {
        create,
        update,
        read,
        view,
        archive,
        supplyCharges,
        supplyCharge
    }
})
