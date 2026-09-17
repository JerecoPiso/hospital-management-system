import { defineStore } from "pinia";
import { ref } from "vue";
import { FeeSchedule } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useFeeScheduleStore = defineStore("feeSchedule", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const schedules = ref<FeeSchedule[]>([])
    const schedule = ref<FeeSchedule>({
        fee_category_pid: '',
        code: '',
        name: '',
        standard_fee: 0,
        is_active: true
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: FeeSchedule) => {
        await axios.post(`${baseUrl}api/fee-schedules`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/fee-schedules`, { params });
        schedules.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/fee-schedules/${pid}`);
        schedule.value = response.data.data;
    }
    const update = async (data: FeeSchedule) => {
        await axios.put(`${baseUrl}api/fee-schedules/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/fee-schedules/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        schedules,
        schedule,
        meta
    }
})
