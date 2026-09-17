import { defineStore } from "pinia";
import { ref } from "vue";
import { FeeCategory } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useFeeCategoryStore = defineStore("feeCategory", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const categories = ref<FeeCategory[]>([])
    const category = ref<FeeCategory>({
        name: '',
        description: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: FeeCategory) => {
        await axios.post(`${baseUrl}api/fee-categories`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/fee-categories`, { params });
        categories.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/fee-categories/${pid}`);
        category.value = response.data.data;
    }
    const update = async (data: FeeCategory) => {
        await axios.put(`${baseUrl}api/fee-categories/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/fee-categories/${pid}`);
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
