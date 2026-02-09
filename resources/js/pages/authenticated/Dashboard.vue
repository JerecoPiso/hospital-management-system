<template>
  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div v-for="stat in stats" :key="stat.id"
      class="bg-white rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-slate-600 text-sm font-medium">{{ stat.label }}</p>
          <p class="text-3xl font-bold text-slate-900 mt-2">{{ stat.value }}</p>
          <p class="text-xs text-emerald-600 font-semibold mt-2">{{ stat.change }}</p>
        </div>
        <div class="w-12 h-12 rounded-lg flex items-center justify-center" :style="{ backgroundColor: stat.bgColor }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: stat.color }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Patient Visits Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-slate-200">
      <h3 class="text-lg font-bold text-slate-900 mb-4">Patient Visits (Last 7 Days)</h3>
      <div class="h-64 flex items-end justify-around gap-2">
        <div v-for="(bar, idx) in chartData" :key="idx"
          class="flex-1 bg-linear-to-t from-emerald-500 to-teal-400 rounded-t-lg hover:shadow-lg transition-shadow"
          :style="{ height: bar + '%' }"></div>
      </div>
      <div class="flex justify-around mt-4 text-xs text-slate-600">
        <span v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="day">{{ day }}</span>
      </div>
    </div>

    <!-- Department Distribution -->
    <div class="bg-white rounded-xl p-6 border border-slate-200">
      <h3 class="text-lg font-bold text-slate-900 mb-4">Departments</h3>
      <div class="space-y-4">
        <div v-for="dept in departments" :key="dept.id" class="flex items-center gap-3">
          <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: dept.color }"></div>
          <div class="flex-1">
            <p class="text-sm font-medium text-slate-900">{{ dept.name }}</p>
            <div class="w-full bg-slate-200 rounded-full h-2 mt-1">
              <div class="h-2 rounded-full" :style="{ width: dept.percentage + '%', backgroundColor: dept.color }">
              </div>
            </div>
          </div>
          <span class="text-sm font-semibold text-slate-900">{{ dept.percentage }}%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-200">
      <h3 class="text-lg font-bold text-slate-900">Recent Users</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Email</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Role</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id"
            class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${user.id}`" :alt="user.name"
                  class="w-8 h-8 rounded-full" />
                <span class="font-medium text-slate-900">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="user.role === 'Doctor'
                ? 'bg-blue-100 text-blue-700'
                : user.role === 'Nurse'
                  ? 'bg-purple-100 text-purple-700'
                  : 'bg-slate-100 text-slate-700'
                ">
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="user.status === 'Active'
                ? 'bg-emerald-100 text-emerald-700'
                : 'bg-slate-100 text-slate-700'
                ">
                {{ user.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <button class="text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup lang="ts">
// ===== Interfaces =====
interface Stat {
  id: number;
  label: string;
  value: string;
  change: string;
  bgColor: string;
  color: string;
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

// ===== Data =====
const stats: Stat[] = [
  {
    id: 1,
    label: 'Total Patients',
    value: '2,543',
    change: '+12% from last month',
    bgColor: '#ecfdf5',
    color: '#10b981',
  },
  {
    id: 2,
    label: 'Appointments',
    value: '156',
    change: '+5% from last week',
    bgColor: '#eff6ff',
    color: '#3b82f6',
  },
  {
    id: 3,
    label: 'Staff Members',
    value: '48',
    change: '+2 new hires',
    bgColor: '#f3e8ff',
    color: '#a855f7',
  },
  {
    id: 4,
    label: 'Revenue',
    value: '$45.2K',
    change: '+8% from last month',
    bgColor: '#fef3c7',
    color: '#f59e0b',
  },
];

const chartData: number[] = [65, 78, 90, 81, 56, 85, 73];

const departments: Department[] = [
  { id: 1, name: 'Cardiology', percentage: 28, color: '#ef4444' },
  { id: 2, name: 'Neurology', percentage: 22, color: '#3b82f6' },
  { id: 3, name: 'Orthopedics', percentage: 25, color: '#10b981' },
  { id: 4, name: 'Pediatrics', percentage: 25, color: '#f59e0b' },
];

const users: User[] = [
  { id: 1, name: 'Dr. Michael Chen', email: 'michael@health.com', role: 'Doctor', status: 'Active' },
  { id: 2, name: 'Nurse Emma Wilson', email: 'emma@health.com', role: 'Nurse', status: 'Active' },
  { id: 3, name: 'Dr. James Brown', email: 'james@health.com', role: 'Doctor', status: 'Active' },
  { id: 4, name: 'Admin Lisa Garcia', email: 'lisa@health.com', role: 'Admin', status: 'Active' },
  { id: 5, name: 'Nurse Robert Taylor', email: 'robert@health.com', role: 'Nurse', status: 'Inactive' },
];

</script>