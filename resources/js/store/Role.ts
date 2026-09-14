import { defineStore } from "pinia";
import { ref } from "vue";
import { ModuleDefinition, Role, RoleAccess } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const useRoleStore = defineStore("role", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const roles = ref<Role[]>([]);
    const role = ref<Role>({ name: "", description: "", accesses: [] });
    const modules = ref<ModuleDefinition[]>([]);
    const meta = ref<ApiTableMeta>(emptyMeta());

    const create = async (data: Role) => {
        await axios.post(`${baseUrl}api/roles`, data);
    };
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/roles`, { params });
        roles.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/roles/${pid}`);
        role.value = response.data.data;
    };
    const update = async (data: Role) => {
        await axios.put(`${baseUrl}api/roles/${data.pid}`, data);
    };
    const updateAccess = async (pid: string, accesses: RoleAccess[]) => {
        await axios.put(`${baseUrl}api/roles/${pid}/access`, { accesses });
    };
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/roles/${pid}`);
    };
    const readModules = async () => {
        const response = await axios.get(`${baseUrl}api/roles/modules`);
        modules.value = response.data.data;
    };

    return {
        roles,
        role,
        modules,
        meta,
        create,
        read,
        view,
        update,
        updateAccess,
        archive,
        readModules,
    };
});
