import { defineStore } from "pinia";
import { ref } from "vue";
import { Prescription } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const usePharmacyStore = defineStore("pharmacy", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const prescriptions = ref<Prescription[]>([]);
    const prescription = ref<Prescription | null>(null);
    const meta = ref<ApiTableMeta>(emptyMeta());

    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/prescriptions`, { params });
        prescriptions.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/prescriptions/${pid}`);
        prescription.value = response.data.data;
    };
    const updateStatus = async (pid: string, status: string) => {
        await axios.patch(`${baseUrl}api/prescriptions/${pid}/status`, { status });
    };

    return { prescriptions, prescription, meta, read, view, updateStatus };
});
