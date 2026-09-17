import { defineStore } from "pinia";
import { ref } from "vue";
import { LabTest } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useLabTestStore = defineStore("labTest", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const tests = ref<LabTest[]>([])
    const test = ref<LabTest>({
        category_pid: '',
        code: '',
        name: '',
        price: 0,
        is_active: true
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: LabTest) => {
        await axios.post(`${baseUrl}api/lab-tests`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/lab-tests`, { params });
        tests.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/lab-tests/${pid}`);
        test.value = response.data.data;
    }
    const update = async (data: LabTest) => {
        await axios.put(`${baseUrl}api/lab-tests/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/lab-tests/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        tests,
        test,
        meta
    }
})
