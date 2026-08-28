import { defineStore } from "pinia";
import { ref } from "vue";
import { Diet } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const useDietStore = defineStore("diet", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const diets = ref<Diet[]>([]);
    const diet = ref<Diet>({
        name: "",
        description: "",
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    const create = async (data: Diet) => {
        await axios.post(`${baseUrl}api/diets`, data);
    };
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/diets`, { params });
        diets.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/diets/${pid}`);
        diet.value = response.data.data;
    };
    const update = async (data: Diet) => {
        await axios.put(`${baseUrl}api/diets/${data.pid}`, data);
    };
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/diets/${pid}`);
    };

    return { create, read, view, update, archive, diets, diet, meta };
});
