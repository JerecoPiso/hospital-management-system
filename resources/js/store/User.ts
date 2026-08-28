import { defineStore } from "pinia";
import { ref } from "vue";
import { User } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useUserStore = defineStore("user", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const users = ref<User[]>([])
    const user = ref<User>({
        pid: '',
        email: '',
        firstname: '',
        middlename: '',
        lastname: '',
        suffix: '',
        license_no: '',
        gender: '',
        date_of_birth: new Date(),
        password: ''
    })
    const genders = ref(['Male', 'Female'])
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: User) => {
        await axios.post(`${baseUrl}api/user/register`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/user/list`, { params });
        users.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/user/${pid}`);
        user.value = response.data.data;
    }
    const update = async (data: User) => {
        await axios.put(`${baseUrl}api/user/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/user/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        users,
        user,
        genders,
        meta
    }
})
