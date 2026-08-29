<template>
  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
        <FaBed class="text-emerald-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Total Admitted</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ total }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
        <FiActivity class="text-red-500" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Admitted Today</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ admittedTodayCount }}</p>
      </div>
    </div>
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
        <BiBed class="text-teal-600" size="20" />
      </div>
      <div>
        <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Admitted This Week</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ admittedThisWeekCount }}</p>
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

        <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
      </div>
    </div>

    <!-- Table -->
    <DataTable
      :value="inpatients"
      lazy
      paginator
      :rows="rows"
      :first="first"
      :totalRecords="total"
      :loading="loading"
      @page="onPage"
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
              {{ data.firstname?.charAt(0)?.toUpperCase() ?? "?" }}
            </div>
            <div>
              <p class="text-slate-800 text-sm font-medium leading-tight">{{ `${data.firstname ?? ""} ${data.lastname ?? ""}`.trim() || "—" }}</p>
              <p class="text-slate-400 text-xs mt-0.5">{{ data.medical_record_number || "—" }}</p>
            </div>
          </div>
        </template>
      </Column>
      <Column header="Case Number" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.patient_cases?.[data.patient_cases.length - 1]?.case_number || "—" }}</span>
        </template>
      </Column>
      <Column header="Station & Bed">
        <template #body="{ data }">
          <div class="flex flex-col gap-2.5">
            <p v-if="data.patient_cases[data.patient_cases.length - 1].station?.name">Station: {{ data.patient_cases[data.patient_cases.length - 1].station?.name }}</p>
            <p v-if="data.patient_cases[data.patient_cases.length - 1].bed?.bed_number">Bed: {{ data.patient_cases[data.patient_cases.length - 1].bed?.bed_number }}</p>
          </div>
        </template>
      </Column>
      <Column header="Patient Type">
        <template #body="{ data }">
          {{ data.patient_cases[data.patient_cases.length - 1].patient_type?.name }}
        </template>
      </Column>
      <Column header="Admission Date" class="w-44">
        <template #body="{ data }">
          <span class="text-slate-500 text-sm">{{ formatDate(data.patient_cases?.[data.patient_cases.length - 1]?.admission_datetime) }}</span>
        </template>
      </Column>

      <Column header="Chief Complaint">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.patient_cases?.[data.patient_cases.length - 1]?.chief_complaint || "—" }}</span>
        </template>
      </Column>
      <Column header="Initial Diagnosis">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.patient_cases?.[data.patient_cases.length - 1]?.initial_diagnosis || "—" }}</span>
        </template>
      </Column>

      <Column header="Final Diagnosis">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{
            data.patient_cases?.[data.patient_cases.length - 1]?.final_diagnosis || data.patient_cases?.[data.patient_cases.length - 1]?.final_diagnosis || "—"
          }}</span>
        </template>
      </Column>

      <Column header="Actions" class="w-16">
        <template #body="{ data }">
          <button
            type="button"
            title="View patient chart"
            :disabled="!data.patient_cases?.length"
            @click="viewChart(data.patient_cases[data.patient_cases.length - 1].pid)"
            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <FiEye size="18" />
          </button>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRouter } from "vue-router";
import { FaBed } from "vue-icons-plus/fa";
import { BiBed } from "vue-icons-plus/bi";
import { FiActivity, FiSearch, FiEye } from "vue-icons-plus/fi";
import { usePatientStore } from "@/store/patients/PatientRegistration";
import { PatientRegistration } from "@/interface/Interfaces";
import { useApiTable } from "@/composables/apiTable";
import { useAppToast } from "@/composables/toast";

const router = useRouter();
const toast = useAppToast();
const patientStore = usePatientStore();

const inpatients = computed<PatientRegistration[]>(() => patientStore.patients);

const { search, rows, first, total, loading, onPage, onSearch } = useApiTable(
  async (params) => {
    try {
      await patientStore.read({ ...params, type: "inpatient" });
    } catch (err: any) {
      toast.error(err.response?.data?.message || "Failed to retrieve inpatients");
    }
  },
  () => patientStore.meta
);

const formatDate = (value?: string) => (value ? new Date(value).toLocaleString() : "—");

const isSameDay = (value: string | undefined, reference: Date) => {
  if (!value) return false;
  const date = new Date(value);
  return date.toDateString() === reference.toDateString();
};

const admittedTodayCount = computed(() => {
  const today = new Date();
  return inpatients.value.filter((p) => isSameDay(p.patient_cases?.[0]?.admission_datetime, today)).length;
});

const admittedThisWeekCount = computed(() => {
  const now = Date.now();
  const weekMs = 7 * 24 * 60 * 60 * 1000;
  return inpatients.value.filter((p) => {
    const admittedAt = p.patient_cases?.[0]?.admission_datetime;
    if (!admittedAt) return false;
    return now - new Date(admittedAt).getTime() <= weekMs;
  }).length;
});

const viewChart = (patientCasePid: string) => {
  router.push({ name: "PatientInformation", params: { patient_case_pid: patientCasePid } });
};
</script>
