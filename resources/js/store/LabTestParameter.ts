import { defineStore } from "pinia";
import { ref } from "vue";
import { LabTestParameter } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useLabTestParameterStore = defineStore("labTestParameter", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const parameters = ref<LabTestParameter[]>([])
    const parameter = ref<LabTestParameter>({
        lab_test_pid: '',
        parameter_name: '',
        unit: '',
        reference_range: '',
        min_val: null,
        max_val: null
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: LabTestParameter) => {
        await axios.post(`${baseUrl}api/lab-test-parameters`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/lab-test-parameters`, { params });
        parameters.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/lab-test-parameters/${pid}`);
        parameter.value = response.data.data;
    }
    const update = async (data: LabTestParameter) => {
        await axios.put(`${baseUrl}api/lab-test-parameters/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/lab-test-parameters/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        parameters,
        parameter,
        meta
    }
})
