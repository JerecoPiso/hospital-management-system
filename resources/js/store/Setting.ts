import { defineStore } from "pinia";
import { ref } from "vue";
import { Setting } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useSettingStore = defineStore("setting", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const settings = ref<Setting[]>([])
    const setting = ref<Setting>({
        name: '',
        value: '',
        description: ''
    })
    const pf = ref<number>(0);
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Setting) => {
        await axios.post(`${baseUrl}api/settings`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/settings`, { params });
        settings.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/settings/${pid}`);
        setting.value = response.data.data;
    }
    const getByName = async (name: string) => {
        const response = await axios.get(`${baseUrl}api/settings/getByname/${name}`);
        if(response.status === 200){
            pf.value = parseFloat(response.data?.data || 0);
        }
    }
    const update = async (data: Setting) => {
        await axios.put(`${baseUrl}api/settings/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/settings/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        getByName,
        update,
        pf,
        settings,
        setting,
        meta
    }
})
