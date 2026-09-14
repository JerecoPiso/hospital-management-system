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
                { path: "users", name: "Users", component: () => import("../pages/authenticated/Users.vue"), meta: { module: "users" } },
                { path: "pharmacy", name: "Pharmacy", component: () => import("../pages/authenticated/pharmacy/Pharmacy.vue"), meta: { module: "prescriptions" } },
                {
                    path: "patients",
                    component: RouterView,
                    children: [
                        { path: "registration", name: "PatientRegistration", component: () => import("../pages/authenticated/patients/PatientRegistration.vue"), meta: { module: "patient" } },
                        { path: "out-patients", name: "OutPatients", component: () => import("../pages/authenticated/patients/OutPatients.vue"), meta: { module: "patient" } },
                        { path: "in-patients", name: "InPatients", component: () => import("../pages/authenticated/patients/InPatients.vue"), meta: { module: "patient" } },
                    ],
                },
                {
                    path: "medicines",
                    component: RouterView,
                    children: [
                        { path: "", name: "Medicines", component: () => import("../pages/authenticated/medicines/MedicineItems.vue"), meta: { module: "medicine" } },
                        { path: "stocks", name: "MedicineStocks", component: () => import("../pages/authenticated/medicines/MedicineStocks.vue"), meta: { module: "medicine-stocks" } },
                        { path: "movements", name: "MedicineStockMovements", component: () => import("../pages/authenticated/medicines/MedicineStockMovements.vue"), meta: { module: "medicine-stock-movements" } },
                        { path: "distributions", name: "MedicineDistributions", component: () => import("../pages/authenticated/medicines/MedicineDistributions.vue"), meta: { module: "medicine-distributions" } },
                    ],
                },
                {
                    path: "supplies",
                    component: RouterView,
                    children: [
                        { path: "", name: "Supplies", component: () => import("../pages/authenticated/supplies/SupplyItems.vue"), meta: { module: "supplies" } },
                        { path: "stocks", name: "SupplyStocks", component: () => import("../pages/authenticated/supplies/SupplyStocks.vue"), meta: { module: "supply-stocks" } },
                        { path: "movements", name: "SupplyMovements", component: () => import("../pages/authenticated/supplies/SupplyMovements.vue"), meta: { module: "supply-movements" } },
                        { path: "distributions", name: "SupplyDistributions", component: () => import("../pages/authenticated/supplies/SupplyDistributions.vue"), meta: { module: "supply-distributions" } },
                    ],
                },
                {
                    path: "dietary",
                    component: RouterView,
                    children: [
                        { path: "", name: "Diets", component: () => import("../pages/authenticated/dietary/Diets.vue"), meta: { module: "diets" } },
                        { path: "patients", name: "DietaryList", component: () => import("../pages/authenticated/dietary/DietaryList.vue"), meta: { module: "patient-case-diets" } },
                    ],
                },
                {
                    path: "settings",
                    component: RouterView,
                    children: [
                        { path: "buildings", name: "Buildings", component: () => import("../pages/authenticated/settings/Buildings.vue"), meta: { module: "buildings" } },
                        { path: "floors", name: "Floors", component: () => import("../pages/authenticated/settings/Floors.vue"), meta: { module: "floors" } },
                        { path: "wards", name: "Wards", component: () => import("../pages/authenticated/settings/Wards.vue"), meta: { module: "wards" } },
                        { path: "rooms", name: "Rooms", component: () => import("../pages/authenticated/settings/Rooms.vue"), meta: { module: "rooms" } },
                        { path: "beds", name: "Beds", component: () => import("../pages/authenticated/settings/Beds.vue"), meta: { module: "beds" } },
                        { path: "stations", name: "Stations", component: () => import("../pages/authenticated/settings/Stations.vue"), meta: { module: "stations" } },
                        { path: "patient-types", name: "PatientTypes", component: () => import("../pages/authenticated/settings/PatientTypes.vue"), meta: { module: "patient-types" } },
                        { path: "pertinent-signs-and-symptoms", name: "PertinentSignsAndSymptoms", component: () => import("../pages/authenticated/settings/PertinentSignsAndSymptoms.vue"), meta: { module: "pertinent-signs-and-symptoms-lists" } },
                        { path: "icds", name: "Icds", component: () => import("../pages/authenticated/settings/Icds.vue"), meta: { module: "icds" } },
                        { path: "roles", name: "Roles", component: () => import("../pages/authenticated/settings/Roles.vue"), meta: { module: "roles" } },
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
                { path: "doctors-order", name: "DoctorsOrder", component: () => import("../pages/authenticated/patientchart/DoctorsOrder.vue"), meta: { module: "doctors-order" } },
                { path: "nurses-notes", name: "NursesNotes", component: () => import("../pages/authenticated/patientchart/NursesNotes.vue"), meta: { module: "nurses-notes" } },
                { path: "therapeutic", name: "Therapeutic", component: () => import("../pages/authenticated/patientchart/Therapeutic.vue"), meta: { module: "prescriptions" } },
                { path: "supply-charges", name: "SupplyCharges", component: () => import("../pages/authenticated/patientchart/SupplyCharges.vue"), meta: { module: "supply-charges" } },
                { path: "vital-signs", name: "VitalSigns", component: () => import("../pages/authenticated/patientchart/VitalSigns.vue"), meta: { module: "vital-signs" } },
                { path: "diet", name: "PatientDiet", component: () => import("../pages/authenticated/patientchart/Diet.vue"), meta: { module: "patient-case-diets" } },
                { path: "patient-forms", name: "PatientForms", component: () => import("../pages/authenticated/patientchart/PatientForms.vue") },
                { path: "patient-forms/physical-examination-form-one", name: "HistoryAndPhysicalExaminationFormOne", component: () => import("../pages/authenticated/patientchart/patientforms/HistoryAndPhysicalExaminationFormOne.vue"), meta: { module: "history-and-physical-examination-form-one" } },
                { path: "patient-forms/physical-examination-form-two", name: "HistoryAndPhysicalExaminationFormTwo", component: () => import("../pages/authenticated/patientchart/patientforms/HistoryAndPhysicalExaminationFormTwo.vue"), meta: { module: "history-and-physical-examination-form-two" } },
                { path: "patient-forms/pertinent-signs-and-symptoms", name: "PertinentSignsAndSymptomsForm", component: () => import("../pages/authenticated/patientchart/patientforms/PertinentSignsAndSymptoms.vue"), meta: { module: "pertinent-signs-and-symptoms" } },
                { path: "patient-forms/soap", name: "SoapForm", component: () => import("../pages/authenticated/patientchart/patientforms/Soap.vue"), meta: { module: "soaps" } },
            ]
        },
        {
            path: "/forbidden",
            name: "Forbidden",
            component: () => import("../pages/Forbidden.vue"),
            meta: { requiresAuth: true },
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

        const module = to.meta.module as string | undefined;
        if (module && to.name !== 'Forbidden' && !auth.can(module, 'view')) {
            return next({ name: 'Forbidden' });
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