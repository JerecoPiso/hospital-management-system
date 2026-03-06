import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/store/patientchart/AuthStore";
const router = createRouter({
    history: createWebHistory(import.meta.env.VITE_APP_URL),
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: () => import("../pages/Login.vue")
        },
        {
            path: "/",
            name: "Authenticated",
            component: () => import("../pages/authenticated/Authenticated.vue"),
            meta: { requiresAuth: true },
            children: [
                { path: "", name: "Dashboard", component: () => import("../pages/authenticated/Dashboard.vue") },
                { path: "patients", name: "Patients", component: () => import("../pages/authenticated/Patients.vue") },
                { path: "medicines", name: "Medicines", component: () => import("../pages/authenticated/Medicines.vue") },
                { path: "users", name: "Users", component: () => import("../pages/authenticated/Users.vue") },
                { path: "supplies", name: "Supplies", component: () => import("../pages/authenticated/Supplies.vue") },
                { path: "settings", name: "Settings", component: () => import("../pages/authenticated/Settings.vue") },
            ],
        },
        {
            path: "/patientchart",
            name: "PatientChart",
            component: () => import("../pages/authenticated/patientchart/PatientChart.vue"),
            meta: { requiresAuth: true },
            children: [
                { path: "", name: "PatientInformation", component: () => import("../pages/authenticated/patientchart/PatientInformation.vue") },
                { path: "doctors-order", name: "DoctorsOrder", component: () => import("../pages/authenticated/patientchart/DoctorsOrder.vue") },
                { path: "nurses-notes", name: "NursesNotes", component: () => import("../pages/authenticated/patientchart/NursesNotes.vue") },
                { path: "therapeutic", name: "Therapeutic", component: () => import("../pages/authenticated/patientchart/Therapeutic.vue") },
                { path: "vital-signs", name: "VitalSigns", component: () => import("../pages/authenticated/patientchart/VitalSigns.vue") },
            ]
        },
    ]
})
// Navigation Guard
router.beforeEach(async (to, from, next) => {
    try {
        // Check session by calling backend
        const auth = useAuthStore();
        const isLoggedOut = localStorage.getItem("isLoggedout");
        if (!auth.user && !isLoggedOut) {
            await auth.getUser();
        }
        if (to.name === 'Login' && auth.user) {
            // If logged in and trying to go to login page → redirect to dashboard
            return next({ name: 'Dashboard' });
        }
        if (to.meta.requiresAuth && !auth.user) {
            // If route requires auth but not logged in → redirect to login
            return next({ name: 'Login' });
        }

        // Otherwise, allow navigation
        next();
    } catch (err) {
        // If API call fails (unauthenticated)
        if (to.meta.requiresAuth) {
            next({ name: 'Login' });
        } else if (to.name === 'Login') {
            next();
        } else {
            next();
        }
    }
});
export default router;