import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
export const useAuthStore = defineStore('auth', () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const user = ref(null)
    const getUser = async () => {
        try {
            const response = await axios.get(`${baseUrl}api/user`, { withCredentials: true });
            user.value = response.data;
        } catch (err) {
            user.value = null;
        }
    }
    const logout = async () => {
        try {
            await axios.post(`${baseUrl}api/user/logout`, {}, { withCredentials: true });
        } catch (err) {
            // Session may already be gone (401/419) — clear client state anyway.
        } finally {
            user.value = null;
            localStorage.setItem("isLoggedout", "true");
        }
    }
    return { user, getUser, logout }
})