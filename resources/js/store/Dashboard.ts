import { defineStore } from "pinia";
import { ref } from "vue";
import { DashboardSummary } from "@/interface/Interfaces";
import axios from "axios";

export const useDashboardStore = defineStore("dashboard", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const loading = ref(false);
    const summary = ref<DashboardSummary>({
        stats: {
            total_patients: 0,
            total_patients_change: 0,
            total_admissions: 0,
            total_admissions_change: 0,
            beds_total: 0,
            beds_occupied: 0,
            bed_occupancy_rate: 0,
            low_stock_count: 0,
        },
        weekly_admissions: [],
        patient_type_distribution: [],
        recent_admissions: [],
        recent_users: [],
    });

    const read = async () => {
        loading.value = true;
        try {
            const response = await axios.get(`${baseUrl}api/dashboard/summary`);
            summary.value = response.data.data;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        summary,
        read,
    };
});
