<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Enter Results Dialog -->
    <Dialog v-model:visible="resultsModalOpen" modal :style="{ width: '48vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <GiMicroscope class="text-white" size="16" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Enter Results</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ activeRequest?.lab_test?.name || "—" }} &bull; {{ activeRequest?.request_number || "—" }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="submitResults" class="flex flex-col gap-4 pt-2">
        <div v-if="resultRows.length === 0" class="text-sm text-slate-400 italic py-6 text-center">This test has no defined parameters yet.</div>
        <div v-for="row in resultRows" :key="row.parameter_pid" class="rounded-lg border border-slate-200 p-3 grid grid-cols-12 gap-3 items-end bg-slate-50/50">
          <div class="col-span-4">
            <span class="text-sm font-medium text-slate-700">{{ row.parameter_name }}</span>
            <p class="text-xs text-slate-400">{{ row.reference_range || "No reference range" }}{{ row.unit ? ` (${row.unit})` : "" }}</p>
          </div>
          <div class="col-span-5 flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-600">Result</label>
            <InputText v-model="row.result_value" fluid class="text-sm" />
          </div>
          <div class="col-span-3 flex items-center gap-2 pb-1.5">
            <input :id="`abn-${row.parameter_pid}`" type="checkbox" v-model="row.is_abnormal" class="w-4 h-4 rounded border-slate-300 text-red-500 focus:ring-red-400" />
            <label :for="`abn-${row.parameter_pid}`" class="text-xs font-medium text-slate-600">Abnormal</label>
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="resultsModalOpen = false" />
          <Button type="submit" label="Save Results" fluid :disabled="resultRows.length === 0" class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <GiTestTubes class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Lab Requests</h3>
          <p class="text-xs text-slate-400">Process pending laboratory test requests and record results</p>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
        <Select v-model="statusFilter" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="All statuses" class="text-sm w-full sm:w-48" @change="reload" />
        <div class="relative w-full sm:w-64">
          <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
          <InputText v-model="search" @input="onSearch" placeholder="Search patient / test . . ." class="w-full text-sm pl-8!" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <DataTable
      :value="labRequests"
      lazy
      paginator
      :rows="rows"
      :first="first"
      :totalRecords="total"
      :loading="loading"
      @page="onPage"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 70rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <GiTestTubes size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No lab requests found</p>
        </div>
      </template>

      <Column header="Request #" class="w-36">
        <template #body="{ data }"><span class="text-slate-700 text-sm font-mono">{{ data.request_number }}</span></template>
      </Column>

      <Column header="Patient">
        <template #body="{ data }">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
              {{ patientName(data).charAt(0).toUpperCase() || "?" }}
            </div>
            <div>
              <p class="text-slate-800 text-sm font-medium leading-tight">{{ patientName(data) || "—" }}</p>
              <p class="text-slate-400 text-xs mt-0.5">{{ data.patient_case?.case_number || "—" }}</p>
            </div>
          </div>
        </template>
      </Column>

      <Column header="Test">
        <template #body="{ data }">
          <span class="text-slate-800 text-sm font-medium">{{ data.lab_test?.name || "—" }}</span>
          <p class="text-xs text-slate-400">{{ data.lab_test?.category?.name || "—" }}</p>
        </template>
      </Column>

      <Column header="Priority" class="w-28">
        <template #body="{ data }"><Tag :value="data.priority" :severity="prioritySeverity(data.priority)" /></template>
      </Column>

      <Column header="Status" class="w-44">
        <template #body="{ data }">
          <Select
            v-if="can('lab-requests', 'update')"
            :modelValue="data.status"
            @update:modelValue="(val) => setStatus(data, val)"
            :options="statusOptions"
            optionLabel="label"
            optionValue="value"
            class="text-sm w-full"
          />
          <Tag v-else :value="data.status" :severity="statusSeverity(data.status)" />
        </template>
      </Column>

      <Column header="Ordered By" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ `${data.doctor?.firstname ?? ""} ${data.doctor?.lastname ?? ""}`.trim() || "—" }}</span>
        </template>
      </Column>

      <Column header="Actions" class="w-20">
        <template #body="{ data }">
          <button v-if="can('lab-requests', 'update')" type="button" title="Enter results" @click="openResults(data)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
            <GiMicroscope size="18" />
          </button>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { FiSearch } from "vue-icons-plus/fi";
import { GiTestTubes, GiMicroscope } from "vue-icons-plus/gi";
import { useLabRequestWorklistStore } from "@/store/LabRequestWorklist";
import { LabRequest } from "@/interface/Interfaces";
import { useApiTable } from "@/composables/apiTable";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const toast = useAppToast();
const { can } = usePermission();
const labRequestWorklistStore = useLabRequestWorklistStore();

const statusFilter = ref<string | null>(null);
const statusFilterOptions = [
  { label: "All statuses", value: null },
  { label: "Pending", value: "pending" },
  { label: "Sample Collected", value: "sample_collected" },
  { label: "In Progress", value: "in_progress" },
  { label: "Completed", value: "completed" },
  { label: "Cancelled", value: "cancelled" },
];
const statusOptions = statusFilterOptions.filter((o) => o.value !== null) as { label: string; value: string }[];

const labRequests = computed<LabRequest[]>(() => labRequestWorklistStore.labRequests);

const patientName = (row: LabRequest) => {
  const p = row.patient_case?.patient;
  if (!p) return "";
  return `${p.firstname ?? ""} ${p.lastname ?? ""}`.trim();
};

const prioritySeverity = (priority?: string) => {
  switch (priority) {
    case "stat":
      return "danger";
    case "urgent":
      return "warn";
    default:
      return "info";
  }
};

const statusSeverity = (status?: string) => {
  switch (status) {
    case "completed":
      return "success";
    case "cancelled":
      return "danger";
    case "in_progress":
    case "sample_collected":
      return "warn";
    default:
      return "secondary";
  }
};

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
  async (params) => {
    try {
      await labRequestWorklistStore.read({ ...params, status: statusFilter.value || undefined });
    } catch (err: any) {
      toast.error(err.response?.data?.message || "Failed to retrieve lab requests");
    }
  },
  () => labRequestWorklistStore.meta
);

const setStatus = async (row: LabRequest, status: string) => {
  if (!row.pid) return;
  try {
    await labRequestWorklistStore.updateStatus(row.pid, status);
    toast.success("Status updated successfully");
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update status");
  }
};

interface ResultRow {
  parameter_pid: string;
  parameter_name: string;
  unit?: string | null;
  reference_range?: string | null;
  result_value: string;
  is_abnormal: boolean;
}

const resultsModalOpen = ref<boolean>(false);
const activeRequest = ref<LabRequest | null>(null);
const resultRows = ref<ResultRow[]>([]);

const openResults = async (row: LabRequest) => {
  if (!row.pid) return;
  try {
    await labRequestWorklistStore.view(row.pid);
    const full = labRequestWorklistStore.labRequest as LabRequest;
    activeRequest.value = full;

    const parameters = full.lab_test?.parameters || [];
    resultRows.value = parameters.map((param: any) => {
      const existing = (full.results || []).find((r: any) => r.parameter?.pid === param.pid);
      return {
        parameter_pid: param.pid,
        parameter_name: param.parameter_name,
        unit: param.unit,
        reference_range: param.reference_range,
        result_value: existing?.result_value || "",
        is_abnormal: existing?.is_abnormal || false,
      };
    });

    resultsModalOpen.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve lab request");
  }
};

const submitResults = async () => {
  if (!activeRequest.value?.pid) return;
  try {
    await labRequestWorklistStore.saveResults(
      activeRequest.value.pid,
      resultRows.value.map((row) => ({ parameter_pid: row.parameter_pid, result_value: row.result_value, is_abnormal: row.is_abnormal }))
    );
    toast.success("Results saved successfully");
    resultsModalOpen.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to save results");
  }
};
</script>
