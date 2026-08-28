import { defineStore } from "pinia";
import { ref } from "vue";
import { PertinentSignsAndSymptomsList } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const usePertinentSignsAndSymptomsListStore = defineStore("pertinentSignsAndSymptomsList", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const items = ref<PertinentSignsAndSymptomsList[]>([]);
    const item = ref<PertinentSignsAndSymptomsList>({
        code: "",
        name: "",
        status: true,
        others: "",
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    const create = async (data: PertinentSignsAndSymptomsList) => {
        await axios.post(`${baseUrl}api/pertinent-signs-and-symptoms-lists`, data);
    };
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/pertinent-signs-and-symptoms-lists`, { params });
        items.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/pertinent-signs-and-symptoms-lists/${pid}`);
        item.value = response.data.data;
    };
    const update = async (data: PertinentSignsAndSymptomsList) => {
        await axios.put(`${baseUrl}api/pertinent-signs-and-symptoms-lists/${data.pid}`, data);
    };
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/pertinent-signs-and-symptoms-lists/${pid}`);
    };

    return { create, read, view, update, archive, items, item, meta };
});
