<template>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
        <div v-for="stat in statCards" :key="stat.label"
            class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" :style="{ backgroundColor: stat.bgColor }">
                <component :is="stat.icon" :style="{ color: stat.color }" size="22" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">{{ stat.label }}</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5">
                    <span v-if="loading" class="inline-block h-6 w-16 bg-slate-100 rounded animate-pulse"></span>
                    <span v-else>{{ stat.value }}</span>
                </p>
                <p v-if="!loading" class="text-xs font-medium mt-1.5 flex items-center gap-1" :class="stat.positive ? 'text-emerald-600' : 'text-red-500'">
                    <svg v-if="stat.positive" class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                        <path d="M6 2l4 5H2l4-5z"/>
                    </svg>
                    <svg v-else class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                        <path d="M6 10L2 5h8l-4 5z"/>
                    </svg>
                    {{ stat.change }}
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-7">
        <!-- Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Patient Admissions</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Last 7 days</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                    This Week
                </span>
            </div>
            <div v-if="loading" class="flex items-end justify-between gap-2 h-48">
                <div v-for="n in 7" :key="n" class="flex-1 bg-slate-100 rounded-t-md animate-pulse" :style="{ height: (30 + n * 5) + '%' }"></div>
            </div>
            <div v-else-if="weeklyAdmissions.every(bar => bar.count === 0)" class="h-48 flex flex-col items-center justify-center text-slate-400 gap-2">
                <FiActivity size="28" />
                <p class="text-sm">No admissions recorded this week</p>
            </div>
            <div v-else class="flex items-end justify-between gap-2 h-48">
                <div v-for="bar in weeklyAdmissions" :key="bar.date" class="flex-1 flex flex-col items-center gap-1.5 group">
                    <span class="text-xs font-semibold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ bar.count }}
                    </span>
                    <div
                        class="w-full rounded-t-md transition-all duration-300 group-hover:brightness-110 cursor-default"
                        :style="{
                            height: Math.max((bar.count / maxWeeklyAdmission) * 100, 4) + '%',
                            background: bar.count === maxWeeklyAdmission && maxWeeklyAdmission > 0
                                ? 'linear-gradient(to top, #059669, #14b8a6)'
                                : 'linear-gradient(to top, #d1fae5, #99f6e4)'
                        }"
                    ></div>
                    <span class="text-xs text-slate-400">{{ bar.day }}</span>
                </div>
            </div>
        </div>

        <!-- Patient Type Distribution -->
        <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
            <div class="mb-6">
                <h3 class="text-sm font-bold text-slate-800">Patient Types</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribution by case type</p>
            </div>
            <div v-if="loading" class="space-y-5">
                <div v-for="n in 4" :key="n" class="space-y-1.5">
                    <div class="h-4 bg-slate-100 rounded animate-pulse"></div>
                    <div class="h-1.5 bg-slate-100 rounded-full animate-pulse"></div>
                </div>
            </div>
            <div v-else-if="!patientTypeDistribution.length" class="py-8 flex flex-col items-center justify-center text-slate-400 gap-2">
                <BiCategoryAlt size="28" />
                <p class="text-sm">No patient cases yet</p>
            </div>
            <div v-else class="space-y-5">
                <div v-for="(type, idx) in patientTypeDistribution" :key="type.name">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: distributionColor(idx) }"></div>
                            <span class="text-sm font-medium text-slate-700">{{ type.name }}</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800">{{ type.percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full transition-all duration-500"
                            :style="{ width: type.percentage + '%', backgroundColor: distributionColor(idx) }">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Legend total -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between text-xs text-slate-400">
                <span>Total patient cases</span>
                <span class="font-semibold text-slate-700">{{ totalPatientCases }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Admissions -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <Fa6BedPulse class="text-white" size="16" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Recent Admissions</h3>
                    <p class="text-xs text-slate-400">Latest patient cases</p>
                </div>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                {{ recentAdmissions.length }} cases
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Case No.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Chief Complaint</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Admitted</th>
                    </tr>
                </thead>
                <tbody v-if="loading">
                    <tr v-for="n in 5" :key="n" class="border-b border-slate-100 last:border-0">
                        <td class="px-6 py-4" colspan="4">
                            <div class="h-4 bg-slate-100 rounded animate-pulse w-full"></div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="!recentAdmissions.length">
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-sm">
                            No patient admissions recorded yet
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="admission in recentAdmissions" :key="admission.pid"
                        class="border-b border-slate-100 hover:bg-slate-50 transition-colors duration-150 last:border-0">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                    {{ admission.patient_name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ admission.patient_name }}</p>
                                    <p v-if="admission.medical_record_number" class="text-xs text-slate-400">{{ admission.medical_record_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ admission.case_number }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ admission.chief_complaint }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(admission.admission_datetime) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { FiActivity } from 'vue-icons-plus/fi';
import { GiMedicalPack } from 'vue-icons-plus/gi';
import { FaBed } from 'vue-icons-plus/fa';
import { Fa6BedPulse } from 'vue-icons-plus/fa6';
import { BiCategoryAlt } from 'vue-icons-plus/bi';
import { MdWarning } from 'vue-icons-plus/md';
import { useDashboardStore } from '@/store/Dashboard';
import { storeToRefs } from 'pinia';

const dashboardStore = useDashboardStore();
const { loading, summary } = storeToRefs(dashboardStore);

onMounted(() => {
    dashboardStore.read();
});

const weeklyAdmissions = computed(() => summary.value.weekly_admissions);
const patientTypeDistribution = computed(() => summary.value.patient_type_distribution);
const recentAdmissions = computed(() => summary.value.recent_admissions);

const maxWeeklyAdmission = computed(() => {
    const max = Math.max(...weeklyAdmissions.value.map(bar => bar.count), 0);
    return max > 0 ? max : 1;
});

const totalPatientCases = computed(() =>
    patientTypeDistribution.value.reduce((sum, type) => sum + type.count, 0)
);

const distributionPalette = ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#a855f7', '#14b8a6'];
const distributionColor = (idx: number) => distributionPalette[idx % distributionPalette.length];

const formatDate = (value: string) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const changeLabel = (change: number, suffix: string) => {
    const sign = change > 0 ? '+' : '';
    return `${sign}${change}% ${suffix}`;
};

const statCards = computed(() => {
    const stats = summary.value.stats;
    return [
        {
            label: 'Total Patients',
            value: stats.total_patients.toLocaleString(),
            change: changeLabel(stats.total_patients_change, 'from last month'),
            positive: stats.total_patients_change >= 0,
            bgColor: '#ecfdf5',
            color: '#059669',
            icon: GiMedicalPack,
        },
        {
            label: 'Admissions This Month',
            value: stats.total_admissions.toLocaleString(),
            change: changeLabel(stats.total_admissions_change, 'from last month'),
            positive: stats.total_admissions_change >= 0,
            bgColor: '#eff6ff',
            color: '#3b82f6',
            icon: FiActivity,
        },
        {
            label: 'Bed Occupancy',
            value: `${stats.bed_occupancy_rate}%`,
            change: `${stats.beds_occupied} of ${stats.beds_total} beds occupied`,
            positive: stats.bed_occupancy_rate < 90,
            bgColor: '#f3e8ff',
            color: '#a855f7',
            icon: FaBed,
        },
        {
            label: 'Low Stock Alerts',
            value: stats.low_stock_count.toLocaleString(),
            change: stats.low_stock_count > 0 ? 'items at or below reorder level' : 'all stocks healthy',
            positive: stats.low_stock_count === 0,
            bgColor: '#fef3c7',
            color: '#d97706',
            icon: MdWarning,
        },
    ];
});
</script>
