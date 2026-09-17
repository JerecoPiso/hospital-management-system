<template>
  <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
    <FaXRay size="40" class="mb-3 opacity-30" />
    <p class="text-sm font-medium">No patient selected</p>
    <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
  </div>

  <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Create Order Dialog -->
    <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FaXRay class="text-white" size="15" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">New Radiology Order</h2>
            <p class="text-xs text-slate-400 mt-0.5">Order an imaging procedure for this case</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="create" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Procedure <span class="text-red-400">*</span></label>
            <Select v-model="info.procedure_pid" :options="procedures" :optionLabel="procedureOptionLabel" optionValue="pid" placeholder="Select procedure" filter fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Priority</label>
            <Select v-model="info.priority" :options="priorityOptions" optionLabel="label" optionValue="value" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Scheduled Date/Time</label>
            <DatePicker v-model="scheduledAtModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD HH:mm" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Clinical History</label>
            <Textarea v-model="info.clinical_history" rows="3" autoResize fluid placeholder="Optional clinical history..." class="text-sm" />
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
          <Button type="submit" label="Save Order" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

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
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaXRay class="text-white" size="17" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Radiology Orders</h3>
          <p class="text-xs text-slate-400">
            <template v-if="patient">{{ patientName }} &bull; Case {{ currentCase?.case_number || "—" }}</template>
            <template v-else>Loading patient...</template>
          </p>
        </div>
      </div>
      <button
        v-if="can('radiology-orders', 'create')"
        type="button"
        @click="openCreate"
        :disabled="!patientCasePid"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
      >
        <BsPlusCircle size="16" />
        New Radiology Order
      </button>
    </div>

    <!-- Table -->
    <DataTable
      :value="radiologyOrders"
      paginator
      :rows="15"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 68rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <FaXRay size="36" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No radiology orders found</p>
          <p class="text-xs mt-1">Click "New Radiology Order" to order the first procedure</p>
        </div>
      </template>

      <Column header="Order #" class="w-36">
        <template #body="{ data }"><span class="text-slate-700 text-sm font-mono">{{ data.order_number }}</span></template>
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

      <Column header="Ordered By" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ `${data.doctor?.firstname ?? ""} ${data.doctor?.lastname ?? ""}`.trim() || "—" }}</span>
        </template>
      </Column>

      <Column header="Report" class="w-28">
        <template #body="{ data }">
          <Tag v-if="data.report" :value="data.report.status" :severity="data.report.status === 'finalized' ? 'success' : 'warn'" />
          <span v-else class="text-slate-300 text-sm italic">None</span>
        </template>
      </Column>

      <Column header="Actions" class="w-24">
        <template #body="{ data }">
          <div class="flex items-center gap-1">
            <button v-if="can('radiology-orders', 'update')" type="button" title="Enter report" @click="openReport(data)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
              <MdMedicalServices size="18" />
            </button>
            <button v-if="can('radiology-orders', 'delete')" type="button" title="Delete order" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
              <BiTrash size="18" />
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useRoute } from "vue-router";
import { BsPlusCircle } from "vue-icons-plus/bs";
import { BiTrash } from "vue-icons-plus/bi";
import { FaXRay } from "vue-icons-plus/fa";
import { MdMedicalServices } from "vue-icons-plus/md";
import { useRadiologyOrderStore } from "@/store/patientchart/RadiologyOrders";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useRadiologyProcedureStore } from "@/store/RadiologyProcedure";
import { RadiologyOrder, RadiologyProcedure } from "@/interface/Interfaces";
import { useConfirmToast } from "@/composables/confirm";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const route = useRoute();
const radiologyOrderStore = useRadiologyOrderStore();
const patientCaseStore = usePatientCaseStore();
const radiologyProcedureStore = useRadiologyProcedureStore();

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const currentCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => currentCase.value?.patient);
const procedures = computed<RadiologyProcedure[]>(() => radiologyProcedureStore.procedures);
const radiologyOrders = computed<RadiologyOrder[]>(() => radiologyOrderStore.radiologyOrders);

const patientName = computed(() => `${patient.value?.firstname ?? ""} ${patient.value?.lastname ?? ""}`.trim() || "—");
const procedureOptionLabel = (data: RadiologyProcedure) => `${data.code} — ${data.name}`;

const priorityOptions = [
  { label: "Routine", value: "routine" },
  { label: "Urgent", value: "urgent" },
  { label: "Stat", value: "stat" },
];
const statusOptions = [
  { label: "Ordered", value: "ordered" },
  { label: "Scheduled", value: "scheduled" },
  { label: "Completed", value: "completed" },
  { label: "Cancelled", value: "cancelled" },
];
const reportStatusOptions = [
  { label: "Draft", value: "draft" },
  { label: "Finalized", value: "finalized" },
  { label: "Amended", value: "amended" },
];

const modalOpen = ref<boolean>(false);
const scheduledAtModel = ref<Date | null>(null);
const defaultInfo = (): RadiologyOrder => ({ pid: "", patient_case_pid: "", procedure_pid: "", priority: "routine", clinical_history: "", scheduled_at: null });
const info = reactive<RadiologyOrder>(defaultInfo());

watch(modalOpen, (open) => {
  if (!open) {
    Object.assign(info, defaultInfo());
    scheduledAtModel.value = null;
  }
});
watch(scheduledAtModel, (val) => {
  info.scheduled_at = val ? val.toISOString() : null;
});

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

const loadContext = async () => {
  if (!patientCasePid.value) return;
  try {
    await patientCaseStore.view(patientCasePid.value);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to load patient");
  }
};

const refresh = async () => {
  if (!patientCasePid.value) return;
  try {
    await radiologyOrderStore.read(patientCasePid.value);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve radiology orders");
  }
};

onMounted(async () => {
  await Promise.all([loadContext(), radiologyProcedureStore.read()]);
  await refresh();
});

const openCreate = () => {
  if (!patientCasePid.value) return;
  modalOpen.value = true;
};

const create = async () => {
  try {
    info.patient_case_pid = patientCasePid.value || "";
    await radiologyOrderStore.create(info);
    toast.success("Radiology order created successfully");
    modalOpen.value = false;
    await refresh();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to create radiology order");
  }
};

const setStatus = async (row: RadiologyOrder, status: string) => {
  if (!row.pid) return;
  try {
    await radiologyOrderStore.updateStatus(row.pid, { status });
    toast.success("Status updated successfully");
    await refresh();
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
    await radiologyOrderStore.view(row.pid);
    const full = radiologyOrderStore.radiologyOrder;
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
    await radiologyOrderStore.saveReport(activeOrder.value.pid, reportInfo);
    toast.success("Report saved successfully");
    reportModalOpen.value = false;
    await refresh();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to save report");
  }
};

const archive = (pid: string) => {
  showConfirm({
    message: "Are you sure you want to delete this radiology order?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await radiologyOrderStore.archive(pid);
        toast.success("Radiology order deleted successfully");
        await refresh();
      } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to delete radiology order");
      }
    },
  });
};
</script>
