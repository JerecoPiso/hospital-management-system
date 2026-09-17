import { defineStore } from "pinia";
import { ref } from "vue";
import { FeeCharge } from "@/interface/Interfaces";
import axios from "axios";
export const useFeeChargeStore = defineStore("feeCharge", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const feeCharges = ref<FeeCharge[]>([])
    const feeCharge = ref<FeeCharge>({
        patient_case_pid: "",
        charge_date: "",
        remarks: "",
        items: []
    })
    const create = async (data: FeeCharge) => {
        await axios.post(`${baseUrl}api/fee-charges`, data);
    }
    const update = async (data: FeeCharge) => {
        await axios.put(`${baseUrl}api/fee-charges/${data.pid}`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/fee-charges`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        feeCharges.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/fee-charges/${pid}`);
        feeCharge.value = response.data.data;
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/fee-charges/${pid}`);
    }
    return {
        create,
        update,
        read,
        view,
        archive,
        feeCharges,
        feeCharge
    }
})
