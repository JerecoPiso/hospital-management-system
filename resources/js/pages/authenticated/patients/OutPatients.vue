<template>
  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
        <FaWalking class="text-emerald-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Today's Visits</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ patients.length }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
        <FiClock class="text-amber-500" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Waiting</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ waitingCount }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
        <FiCalendar class="text-teal-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Completed</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ completedCount }}</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaWalking class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Outpatients</h3>
          <p class="text-xs text-slate-400">Patients scheduled for consultation or walk-in visits</p>
        </div>
      </div>
      <div class="relative w-full sm:w-64">
        <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />

        <InputText v-model="search" placeholder="Search . . ." class="w-full text-sm pl-8!" />
      </div>
    </div>

    <!-- Table -->
    <DataTable
      :value="filteredPatients"
      paginator
      :rows="15"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 60rem"
      :pt="{
        table: { class: 'text-sm' },
        thead: { class: 'bg-slate-50' },
        bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' },
      }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <FaWalking size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No outpatients found</p>
          <p class="text-xs mt-1">Scheduled visits will appear here</p>
        </div>
      </template>

      <Column header="Patient">
        <template #body="{ data }">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
              {{ data.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <p class="text-slate-800 text-sm font-medium leading-tight">{{ data.name }}</p>
              <p class="text-slate-400 text-xs mt-0.5">{{ data.patientId }}</p>
            </div>
          </div>
        </template>
      </Column>

      <Column header="Visit Date" class="w-32">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.visitDate }}</span>
        </template>
      </Column>

      <Column header="Time" class="w-24">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.visitTime }}</span>
        </template>
      </Column>

      <Column header="Physician">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.physician }}</span>
        </template>
      </Column>

      <Column header="Reason for Visit">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.reason }}</span>
        </template>
      </Column>

      <Column header="Status" class="w-36">
        <template #body="{ data }">
          <span :class="['inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium border', statusStyles[data.status]]">
            <span class="w-1.5 h-1.5 rounded-full" :class="statusDot[data.status]"></span>
            {{ data.status }}
          </span>
        </template>
      </Column>

      <Column header="Actions" class="w-16">
        <template #body="{ data }">
          <button
            type="button"
            title="View patient chart"
            @click="viewChart(data)"
            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
          >
            <FiEye size="18" />
          </button>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { FaWalking } from "vue-icons-plus/fa";
import { FiClock, FiCalendar, FiSearch, FiEye } from "vue-icons-plus/fi";

const router = useRouter();
const search = ref("");

const patients = ref([
  { patientId: "PT-20114", name: "Carlos Mendoza", visitDate: "2026-07-03", visitTime: "09:00 AM", physician: "Dr. Santos", reason: "Follow-up checkup", status: "Completed" },
  { patientId: "PT-20128", name: "Elena Cruz", visitDate: "2026-07-03", visitTime: "09:30 AM", physician: "Dr. Reyes", reason: "Fever and cough", status: "In Consultation" },
  { patientId: "PT-20135", name: "Miguel Torres", visitDate: "2026-07-03", visitTime: "10:00 AM", physician: "Dr. Bautista", reason: "Annual physical exam", status: "Waiting" },
  { patientId: "PT-20142", name: "Sofia Ramirez", visitDate: "2026-07-03", visitTime: "10:15 AM", physician: "Dr. Cruz", reason: "Skin rash consultation", status: "Waiting" },
  { patientId: "PT-20150", name: "Diego Villanueva", visitDate: "2026-07-03", visitTime: "08:45 AM", physician: "Dr. Santos", reason: "Blood pressure monitoring", status: "Completed" },
]);

const waitingCount = computed(() => patients.value.filter((p) => p.status === "Waiting").length);
const completedCount = computed(() => patients.value.filter((p) => p.status === "Completed").length);

const filteredPatients = computed(() => {
  const query = search.value.trim().toLowerCase();
  if (!query) return patients.value;
  return patients.value.filter((p) => p.name.toLowerCase().includes(query));
});

const statusStyles = {
  Waiting: "bg-amber-50 text-amber-700 border-amber-100",
  "In Consultation": "bg-blue-50 text-blue-700 border-blue-100",
  Completed: "bg-emerald-50 text-emerald-700 border-emerald-100",
};
const statusDot = {
  Waiting: "bg-amber-500",
  "In Consultation": "bg-blue-500",
  Completed: "bg-emerald-500",
};

const viewChart = () => {
  router.push({ name: "PatientChart" });
};
</script>
