<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Enter Report Dialog -->
    <Dialog v-model:visible="reportModalOpen" modal :style="{ width: '46vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <MdMedicalServices class="text-white" size="16" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Radiology Report</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ activeOrder?.procedure?.name || "—" }} &bull; {{ activeOrder?.order_number || "—" }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="submitReport" class="flex flex-col gap-4 pt-2">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Findings <span class="text-red-400">*</span></label>
          <Textarea v-model="reportInfo.findings" rows="5" autoResize fluid required placeholder="Describe the imaging findings..." class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Impression <span class="text-red-400">*</span></label>
          <Textarea v-model="reportInfo.impression" rows="3" autoResize fluid required placeholder="Overall impression..." class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Status</label>
          <Select v-model="reportInfo.status" :options="reportStatusOptions" optionLabel="label" optionValue="value" fluid class="text-sm" />
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="reportModalOpen = false" />
          <Button type="submit" label="Save Report" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaXRay class="text-white" size="17" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Radiology Orders</h3>
          <p class="text-xs text-slate-400">Process pending imaging orders and record reports</p>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
        <Select v-model="statusFilter" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="All statuses" class="text-sm w-full sm:w-48" @change="reload" />
        <Select v-model="statusPriority" :options="statusPriorityOptions" optionLabel="label" optionValue="value" placeholder="All" class="text-sm w-full sm:w-48" @change="reload" />

        <div class="relative w-full sm:w-64">
          <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
          <InputText v-model="search" @input="onSearch" placeholder="Search patient / procedure . . ." class="w-full text-sm pl-8!" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <DataTable
      :value="radiologyOrders"
      lazy
      paginator
      :rows="rows"
      :first="first"
      :totalRecords="total"
      :loading="loading"
      @page="onPage"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 72rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <FaXRay size="36" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No radiology orders found</p>
        </div>
      </template>

      <Column header="Order #" class="w-36">
        <template #body="{ data }"
          ><span class="text-slate-700 text-sm font-mono">{{ data.order_number }}</span></template
        >
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

      <Column header="Procedure">
        <template #body="{ data }">
          <span class="text-slate-800 text-sm font-medium">{{ data.procedure?.name || "—" }}</span>
          <p class="text-xs text-slate-400">{{ data.procedure?.modality?.name || "—" }}</p>
        </template>
      </Column>

      <Column header="Priority" class="w-28">
        <template #body="{ data }"><Tag :value="data.priority" :severity="prioritySeverity(data.priority)" /></template>
      </Column>

      <Column header="Status" class="w-44">
        <template #body="{ data }">
          <Select
            v-if="can('radiology-orders', 'update')"
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

      <Column header="Report" class="w-28">
        <template #body="{ data }">
          <Tag v-if="data.report" :value="data.report.status" :severity="data.report.status === 'finalized' ? 'success' : 'warn'" />
          <span v-else class="text-slate-300 text-sm italic">None</span>
        </template>
      </Column>

      <Column header="Actions" class="w-20">
        <template #body="{ data }">
          <div class="flex gap-1">
            <router-link
              v-if="can('radiology-procedures', 'view')"
              :to="{ name: 'RadiologyOrderPrint', params: { pid: data.pid } }"
              target="_blank"
              title="Print order"
              class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors duration-150 cursor-pointer"
            >
              <FiPrinter size="18" />
            </router-link>
            <button
              v-if="can('radiology-orders', 'update')"
              type="button"
              title="Enter report"
              @click="openReport(data)"
              class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
            >
              <MdMedicalServices size="18" />
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { FiSearch, FiPrinter } from "vue-icons-plus/fi";
import { FaXRay } from "vue-icons-plus/fa";
import { MdMedicalServices } from "vue-icons-plus/md";
import { useRadiologyOrderWorklistStore } from "@/store/RadiologyOrderWorklist";
import { RadiologyOrder } from "@/interface/Interfaces";
import { useApiTable } from "@/composables/apiTable";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const toast = useAppToast();
const { can } = usePermission();
const radiologyOrderWorklistStore = useRadiologyOrderWorklistStore();

const statusFilter = ref<string | null>(null);
const statusPriority = ref<string | null>(null);

const statusFilterOptions = [
  { label: "All statuses", value: null },
  { label: "Ordered", value: "ordered" },
  { label: "Scheduled", value: "scheduled" },
  { label: "Completed", value: "completed" },
  { label: "Cancelled", value: "cancelled" },
];
const statusPriorityOptions = [
  { label: "All", value: null },
  { label: "Routine", value: "routine" },
  { label: "Urgent", value: "urgent" },
  { label: "Stat", value: "stat" },
];
const statusOptions = statusFilterOptions.filter((o) => o.value !== null) as { label: string; value: string }[];
const reportStatusOptions = [
  { label: "Draft", value: "draft" },
  { label: "Finalized", value: "finalized" },
  { label: "Amended", value: "amended" },
];

const radiologyOrders = computed<RadiologyOrder[]>(() => radiologyOrderWorklistStore.radiologyOrders);

const patientName = (row: RadiologyOrder) => {
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
    case "scheduled":
      return "warn";
    default:
      return "secondary";
  }
};

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
  async (params) => {
    try {
      await radiologyOrderWorklistStore.read({ ...params, status: statusFilter.value || undefined, priority: statusPriority.value || undefined });
    } catch (err: any) {
      toast.error(err.response?.data?.message || "Failed to retrieve radiology orders");
    }
  },
  () => radiologyOrderWorklistStore.meta
);

const setStatus = async (row: RadiologyOrder, status: string) => {
  if (!row.pid) return;
  try {
    await radiologyOrderWorklistStore.updateStatus(row.pid, { status });
    toast.success("Status updated successfully");
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update status");
  }
};

const reportModalOpen = ref<boolean>(false);
const activeOrder = ref<RadiologyOrder | null>(null);
const defaultReportInfo = () => ({ findings: "", impression: "", status: "draft" });
const reportInfo = reactive(defaultReportInfo());

watch(reportModalOpen, (open) => {
  if (!open) Object.assign(reportInfo, defaultReportInfo());
});

const openReport = async (row: RadiologyOrder) => {
  if (!row.pid) return;
  try {
    await radiologyOrderWorklistStore.view(row.pid);
    const full = radiologyOrderWorklistStore.radiologyOrder as RadiologyOrder;
    activeOrder.value = full;
    Object.assign(reportInfo, {
      findings: full.report?.findings || "",
      impression: full.report?.impression || "",
      status: full.report?.status || "draft",
    });
    reportModalOpen.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve radiology order");
  }
};

const submitReport = async () => {
  if (!activeOrder.value?.pid) return;
  try {
    await radiologyOrderWorklistStore.saveReport(activeOrder.value.pid, reportInfo);
    toast.success("Report saved successfully");
    reportModalOpen.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to save report");
  }
};
</script>
