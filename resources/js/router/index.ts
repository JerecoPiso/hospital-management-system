import { createRouter, createWebHistory, RouterView } from "vue-router";
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
                { path: "users", name: "Users", component: () => import("../pages/authenticated/Users.vue") },
                { path: "pharmacy", name: "Pharmacy", component: () => import("../pages/authenticated/pharmacy/Pharmacy.vue") },
                {
                    path: "patients",
                    component: RouterView,
                    children: [
                        { path: "registration", name: "PatientRegistration", component: () => import("../pages/authenticated/patients/PatientRegistration.vue") },
                        { path: "out-patients", name: "OutPatients", component: () => import("../pages/authenticated/patients/OutPatients.vue") },
                        { path: "in-patients", name: "InPatients", component: () => import("../pages/authenticated/patients/InPatients.vue") },
                    ],
                },
                {
                    path: "medicines",
                    component: RouterView,
                    children: [
                        { path: "", name: "Medicines", component: () => import("../pages/authenticated/medicines/MedicineItems.vue") },
                        { path: "stocks", name: "MedicineStocks", component: () => import("../pages/authenticated/medicines/MedicineStocks.vue") },
                        { path: "movements", name: "MedicineStockMovements", component: () => import("../pages/authenticated/medicines/MedicineStockMovements.vue") },
                        { path: "distributions", name: "MedicineDistributions", component: () => import("../pages/authenticated/medicines/MedicineDistributions.vue") },
                    ],
                },
                {
                    path: "supplies",
                    component: RouterView,
                    children: [
                        { path: "", name: "Supplies", component: () => import("../pages/authenticated/supplies/SupplyItems.vue") },
                        { path: "stocks", name: "SupplyStocks", component: () => import("../pages/authenticated/supplies/SupplyStocks.vue") },
                        { path: "movements", name: "SupplyMovements", component: () => import("../pages/authenticated/supplies/SupplyMovements.vue") },
                        { path: "distributions", name: "SupplyDistributions", component: () => import("../pages/authenticated/supplies/SupplyDistributions.vue") },
                    ],
                },
                {
                    path: "dietary",
                    component: RouterView,
                    children: [
                        { path: "", name: "Diets", component: () => import("../pages/authenticated/dietary/Diets.vue") },
                        { path: "patients", name: "DietaryList", component: () => import("../pages/authenticated/dietary/DietaryList.vue") },
                    ],
                },
                {
                    path: "settings",
                    component: RouterView,
                    children: [
                        { path: "buildings", name: "Buildings", component: () => import("../pages/authenticated/settings/Buildings.vue") },
                        { path: "floors", name: "Floors", component: () => import("../pages/authenticated/settings/Floors.vue") },
                        { path: "wards", name: "Wards", component: () => import("../pages/authenticated/settings/Wards.vue") },
                        { path: "rooms", name: "Rooms", component: () => import("../pages/authenticated/settings/Rooms.vue") },
                        { path: "beds", name: "Beds", component: () => import("../pages/authenticated/settings/Beds.vue") },
                        { path: "stations", name: "Stations", component: () => import("../pages/authenticated/settings/Stations.vue") },
                        { path: "patient-types", name: "PatientTypes", component: () => import("../pages/authenticated/settings/PatientTypes.vue") },
                        { path: "pertinent-signs-and-symptoms", name: "PertinentSignsAndSymptoms", component: () => import("../pages/authenticated/settings/PertinentSignsAndSymptoms.vue") },
                    ],
                },
            ],
        },
        {
            path: "/patientchart/:patient_case_pid?",
            name: "PatientChart",
            component: () => import("../pages/authenticated/patientchart/PatientChart.vue"),
            meta: { requiresAuth: true },
            children: [
                { path: "", name: "PatientInformation", component: () => import("../pages/authenticated/patientchart/PatientInformation.vue") },
                { path: "doctors-order", name: "DoctorsOrder", component: () => import("../pages/authenticated/patientchart/DoctorsOrder.vue") },
                { path: "nurses-notes", name: "NursesNotes", component: () => import("../pages/authenticated/patientchart/NursesNotes.vue") },
                { path: "therapeutic", name: "Therapeutic", component: () => import("../pages/authenticated/patientchart/Therapeutic.vue") },
                { path: "vital-signs", name: "VitalSigns", component: () => import("../pages/authenticated/patientchart/VitalSigns.vue") },
                { path: "diet", name: "PatientDiet", component: () => import("../pages/authenticated/patientchart/Diet.vue") },
                { path: "patient-forms", name: "PatientForms", component: () => import("../pages/authenticated/patientchart/PatientForms.vue") },
                { path: "patient-forms/physical-examination-form-one", name: "HistoryAndPhysicalExaminationFormOne", component: () => import("../pages/authenticated/patientchart/patientforms/HistoryAndPhysicalExaminationFormOne.vue") },
                { path: "patient-forms/physical-examination-form-two", name: "HistoryAndPhysicalExaminationFormTwo", component: () => import("../pages/authenticated/patientchart/patientforms/HistoryAndPhysicalExaminationFormTwo.vue") },
                { path: "patient-forms/pertinent-signs-and-symptoms", name: "PertinentSignsAndSymptomsForm", component: () => import("../pages/authenticated/patientchart/patientforms/PertinentSignsAndSymptoms.vue") },
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