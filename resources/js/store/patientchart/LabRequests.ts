import { defineStore } from "pinia";
import { ref } from "vue";
import { LabRequest } from "@/interface/Interfaces";
import axios from "axios";
export const useLabRequestStore = defineStore("labRequest", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const labRequests = ref<LabRequest[]>([])
    const labRequest = ref<LabRequest>({
        patient_case_pid: "",
        lab_test_pid: "",
        priority: "routine",
        clinical_notes: ""
    })
    const create = async (data: LabRequest) => {
        await axios.post(`${baseUrl}api/lab-requests`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/lab-requests`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        labRequests.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/lab-requests/${pid}`);
        labRequest.value = response.data.data;
    }
    const updateStatus = async (pid: string, status: string) => {
        await axios.put(`${baseUrl}api/lab-requests/${pid}/status`, { status });
    }
    const saveResults = async (pid: string, results: Array<{ parameter_pid: string; result_value: string; is_abnormal?: boolean }>) => {
        await axios.put(`${baseUrl}api/lab-requests/${pid}/results`, { results });
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/lab-requests/${pid}`);
    }
    return {
        create,
        read,
        view,
        updateStatus,
        saveResults,
        archive,
        labRequests,
        labRequest
    }
})
