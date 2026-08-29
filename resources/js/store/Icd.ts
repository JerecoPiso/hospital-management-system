import { defineStore } from "pinia";
import { ref } from "vue";
import { Icd } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const useIcdStore = defineStore("icd", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const icds = ref<Icd[]>([]);
    const icd = ref<Icd>({
        code: "",
        name: "",
        status: true,
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    const create = async (data: Icd) => {
        await axios.post(`${baseUrl}api/icds`, data);
    };
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/icds`, { params });
        icds.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/icds/${pid}`);
        icd.value = response.data.data;
    };
    const update = async (data: Icd) => {
        await axios.put(`${baseUrl}api/icds/${data.pid}`, data);
    };
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/icds/${pid}`);
    };

    return { create, read, view, update, archive, icds, icd, meta };
});
