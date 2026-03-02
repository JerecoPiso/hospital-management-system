import { createRouter, createWebHistory } from "vue-router";
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
            // meta: { requiresAuth: true },
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
export default router;