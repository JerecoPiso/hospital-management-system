import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_APP_URL),
    routes: [
        {
            path: '/',
            name: 'Login',
            component: Login
        }
    ]
})
export default router