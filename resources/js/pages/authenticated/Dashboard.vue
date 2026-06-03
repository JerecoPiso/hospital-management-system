<template>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
        <div v-for="stat in stats" :key="stat.id"
            class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" :style="{ backgroundColor: stat.bgColor }">
                <component :is="stat.icon" :style="{ color: stat.color }" size="22" />
            </div>
            <div class="min-w-0">
                <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">{{ stat.label }}</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ stat.value }}</p>
                <p class="text-xs font-medium mt-1.5 flex items-center gap-1" :class="stat.positive ? 'text-emerald-600' : 'text-red-500'">
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
                    <h3 class="text-sm font-bold text-slate-800">Patient Visits</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Last 7 days</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                    This Week
                </span>
            </div>
            <div class="flex items-end justify-between gap-2 h-48">
                <div v-for="(bar, idx) in chartBars" :key="idx" class="flex-1 flex flex-col items-center gap-1.5 group">
                    <span class="text-xs font-semibold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ bar.value }}
                    </span>
                    <div
                        class="w-full rounded-t-md transition-all duration-300 group-hover:brightness-110 cursor-default"
                        :style="{ height: bar.value + '%', background: bar.value === Math.max(...chartBars.map(b => b.value)) ? 'linear-gradient(to top, #059669, #14b8a6)' : 'linear-gradient(to top, #d1fae5, #99f6e4)' }"
                    ></div>
                    <span class="text-xs text-slate-400">{{ bar.day }}</span>
                </div>
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
            <div class="mb-6">
                <h3 class="text-sm font-bold text-slate-800">Departments</h3>
                <p class="text-xs text-slate-400 mt-0.5">Patient distribution</p>
            </div>
            <div class="space-y-5">
                <div v-for="dept in departments" :key="dept.id">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: dept.color }"></div>
                            <span class="text-sm font-medium text-slate-700">{{ dept.name }}</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800">{{ dept.percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full transition-all duration-500"
                            :style="{ width: dept.percentage + '%', backgroundColor: dept.color }">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Legend total -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between text-xs text-slate-400">
                <span>Total patients</span>
                <span class="font-semibold text-slate-700">2,543</span>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FiUsers class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Recent Users</h3>
                    <p class="text-xs text-slate-400">Latest registered accounts</p>
                </div>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                {{ users.length }} users
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id"
                        class="border-b border-slate-100 hover:bg-slate-50 transition-colors duration-150 last:border-0">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="text-sm font-medium text-slate-800">{{ user.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                :class="user.role === 'Doctor'
                                    ? 'bg-blue-50 text-blue-700 border-blue-100'
                                    : user.role === 'Nurse'
                                        ? 'bg-purple-50 text-purple-700 border-purple-100'
                                        : 'bg-slate-100 text-slate-600 border-slate-200'">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                :class="user.status === 'Active'
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                    : 'bg-slate-100 text-slate-500 border-slate-200'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                    :class="user.status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400'">
                                </span>
                                {{ user.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { FiUsers, FiActivity } from 'vue-icons-plus/fi';
import { GiMedicalPack } from 'vue-icons-plus/gi';
import { MdDashboard } from 'vue-icons-plus/md';

interface Stat {
    id: number;
    label: string;
    value: string;
    change: string;
    positive: boolean;
    bgColor: string;
    color: string;
    icon: any;
}

interface ChartBar {
    day: string;
    value: number;
}

interface Department {
    id: number;
    name: string;
    percentage: number;
    color: string;
}

type UserRole = 'Doctor' | 'Nurse' | 'Admin';
type UserStatus = 'Active' | 'Inactive';

interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    status: UserStatus;
}

const stats: Stat[] = [
    {
        id: 1,
        label: 'Total Patients',
        value: '2,543',
        change: '12% from last month',
        positive: true,
        bgColor: '#ecfdf5',
        color: '#059669',
        icon: GiMedicalPack,
    },
    {
        id: 2,
        label: 'Appointments',
        value: '156',
        change: '5% from last week',
        positive: true,
        bgColor: '#eff6ff',
        color: '#3b82f6',
        icon: FiActivity,
    },
    {
        id: 3,
        label: 'Staff Members',
        value: '48',
        change: '2 new this month',
        positive: true,
        bgColor: '#f3e8ff',
        color: '#a855f7',
        icon: FiUsers,
    },
    {
        id: 4,
        label: 'Revenue',
        value: '₱45.2K',
        change: '8% from last month',
        positive: true,
        bgColor: '#fef3c7',
        color: '#d97706',
        icon: MdDashboard,
    },
];

const chartBars: ChartBar[] = [
    { day: 'Mon', value: 65 },
    { day: 'Tue', value: 78 },
    { day: 'Wed', value: 90 },
    { day: 'Thu', value: 81 },
    { day: 'Fri', value: 56 },
    { day: 'Sat', value: 85 },
    { day: 'Sun', value: 73 },
];

const departments: Department[] = [
    { id: 1, name: 'Cardiology',   percentage: 28, color: '#ef4444' },
    { id: 2, name: 'Neurology',    percentage: 22, color: '#3b82f6' },
    { id: 3, name: 'Orthopedics',  percentage: 25, color: '#10b981' },
    { id: 4, name: 'Pediatrics',   percentage: 25, color: '#f59e0b' },
];

const users: User[] = [
    { id: 1, name: 'Dr. Michael Chen',    email: 'michael@health.com', role: 'Doctor', status: 'Active'   },
    { id: 2, name: 'Nurse Emma Wilson',   email: 'emma@health.com',    role: 'Nurse',  status: 'Active'   },
    { id: 3, name: 'Dr. James Brown',     email: 'james@health.com',   role: 'Doctor', status: 'Active'   },
    { id: 4, name: 'Admin Lisa Garcia',   email: 'lisa@health.com',    role: 'Admin',  status: 'Active'   },
    { id: 5, name: 'Nurse Robert Taylor', email: 'robert@health.com',  role: 'Nurse',  status: 'Inactive' },
];
</script>
