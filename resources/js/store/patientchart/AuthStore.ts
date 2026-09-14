import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { PermissionsMap, User } from '@/interface/Interfaces'

export const useAuthStore = defineStore('auth', () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const user = ref<User | null>(null)
    const permissions = ref<PermissionsMap>({})
    const getUser = async () => {
        try {
            const response = await axios.get(`${baseUrl}api/user`, { withCredentials: true });
            user.value = response.data.data.user;
            permissions.value = response.data.data.permissions ?? {};
        } catch (err) {
            user.value = null;
            permissions.value = {};
        }
    }
    const can = (module: string, ability: keyof import('@/interface/Interfaces').PermissionAbilities = 'view') => {
        return !!permissions.value[module]?.[ability];
    }
    const logout = async () => {
        try {
            await axios.post(`${baseUrl}api/user/logout`, {}, { withCredentials: true });
        } catch (err) {
            // Session may already be gone (401/419) — clear client state anyway.
        } finally {
            user.value = null;
            permissions.value = {};
            localStorage.setItem("isLoggedout", "true");
        }
    }
    return { user, permissions, getUser, can, logout }
})
