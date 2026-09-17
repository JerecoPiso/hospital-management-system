import { defineStore } from "pinia";
import { ref } from "vue";
import { LabRequest } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const useLabRequestWorklistStore = defineStore("labRequestWorklist", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const labRequests = ref<LabRequest[]>([]);
    const labRequest = ref<LabRequest | null>(null);
    const meta = ref<ApiTableMeta>(emptyMeta());

    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/lab-requests`, { params });
        labRequests.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/lab-requests/${pid}`);
        labRequest.value = response.data.data;
    };
    const updateStatus = async (pid: string, status: string) => {
        await axios.put(`${baseUrl}api/lab-requests/${pid}/status`, { status });
    };
    const saveResults = async (pid: string, results: Array<{ parameter_pid: string; result_value: string; is_abnormal?: boolean }>) => {
        await axios.put(`${baseUrl}api/lab-requests/${pid}/results`, { results });
    };

    return { labRequests, labRequest, meta, read, view, updateStatus, saveResults };
});
