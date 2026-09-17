import { defineStore } from "pinia";
import { ref } from "vue";
import { LabTestCategory } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useLabTestCategoryStore = defineStore("labTestCategory", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const categories = ref<LabTestCategory[]>([])
    const category = ref<LabTestCategory>({
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: LabTestCategory) => {
        await axios.post(`${baseUrl}api/lab-test-categories`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/lab-test-categories`, { params });
        categories.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/lab-test-categories/${pid}`);
        category.value = response.data.data;
    }
    const update = async (data: LabTestCategory) => {
        await axios.put(`${baseUrl}api/lab-test-categories/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/lab-test-categories/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        categories,
        category,
        meta
    }
})
