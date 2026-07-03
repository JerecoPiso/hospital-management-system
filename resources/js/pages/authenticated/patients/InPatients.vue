<template>
  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
        <FaBed class="text-emerald-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Total Admitted</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ patients.length }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
        <FiActivity class="text-red-500" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Critical Cases</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ criticalCount }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
        <BiBed class="text-teal-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Stable Patients</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ stableCount }}</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaBed class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Inpatients</h3>
          <p class="text-xs text-slate-400">Patients currently admitted to the hospital</p>
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
          <FaBed size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No inpatients found</p>
          <p class="text-xs mt-1">Admitted patients will appear here</p>
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

      <Column header="Room / Bed" class="w-32">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.room }}</span>
        </template>
      </Column>

      <Column header="Ward" class="w-32">
        <template #body="{ data }">
          <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
            {{ data.ward }}
          </span>
        </template>
      </Column>

      <Column header="Attending Physician">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.physician }}</span>
        </template>
      </Column>

      <Column header="Admission Date" class="w-36">
        <template #body="{ data }">
          <span class="text-slate-500 text-sm">{{ data.admissionDate }}</span>
        </template>
      </Column>

      <Column header="Diagnosis">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.diagnosis }}</span>
        </template>
      </Column>

      <Column header="Status" class="w-32">
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
import { FaBed } from "vue-icons-plus/fa";
import { BiBed } from "vue-icons-plus/bi";
import { FiActivity, FiSearch, FiEye } from "vue-icons-plus/fi";

const router = useRouter();
const search = ref("");

const patients = ref([
  {
    patientId: "PT-10231",
    name: "Maria Santos",
    room: "204-A",
    ward: "Medical Ward",
    physician: "Dr. Reyes",
    admissionDate: "2026-06-28",
    diagnosis: "Community-acquired pneumonia",
    status: "Critical",
  },
  {
    patientId: "PT-10245",
    name: "Juan Dela Cruz",
    room: "210-B",
    ward: "Surgical Ward",
    physician: "Dr. Bautista",
    admissionDate: "2026-06-30",
    diagnosis: "Post-appendectomy recovery",
    status: "Stable",
  },
  { patientId: "PT-10267", name: "Ana Lim", room: "112-A", ward: "Pediatric Ward", physician: "Dr. Cruz", admissionDate: "2026-07-01", diagnosis: "Dengue fever", status: "Stable" },
  { patientId: "PT-10289", name: "Roberto Garcia", room: "ICU-3", ward: "ICU", physician: "Dr. Reyes", admissionDate: "2026-07-02", diagnosis: "Acute myocardial infarction", status: "Critical" },
  {
    patientId: "PT-10302",
    name: "Liza Fernandez",
    room: "305-C",
    ward: "Medical Ward",
    physician: "Dr. Santos",
    admissionDate: "2026-06-25",
    diagnosis: "Type 2 diabetes management",
    status: "Stable",
  },
]);

const criticalCount = computed(() => patients.value.filter((p) => p.status === "Critical").length);
const stableCount = computed(() => patients.value.filter((p) => p.status === "Stable").length);

const filteredPatients = computed(() => {
  const query = search.value.trim().toLowerCase();
  if (!query) return patients.value;
  return patients.value.filter((p) => p.name.toLowerCase().includes(query) || p.room.toLowerCase().includes(query));
});

const statusStyles = {
  Critical: "bg-red-50 text-red-700 border-red-100",
  Stable: "bg-emerald-50 text-emerald-700 border-emerald-100",
};
const statusDot = {
  Critical: "bg-red-500",
  Stable: "bg-emerald-500",
};

const viewChart = () => {
  router.push({ name: "PatientChart" });
};
</script>
