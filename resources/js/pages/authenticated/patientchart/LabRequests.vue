<template>
  <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
    <GiTestTubes size="40" class="mb-3 opacity-30" />
    <p class="text-sm font-medium">No patient selected</p>
    <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
  </div>

  <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Create Request Dialog -->
    <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <GiTestTubes class="text-white" size="16" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">New Lab Request</h2>
            <p class="text-xs text-slate-400 mt-0.5">Order a laboratory test for this case</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="create" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Lab Test <span class="text-red-400">*</span></label>
            <Select v-model="info.lab_test_pid" :options="labTests" :optionLabel="labTestOptionLabel" optionValue="pid" placeholder="Select lab test" filter fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Priority</label>
            <Select v-model="info.priority" :options="priorityOptions" optionLabel="label" optionValue="value" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Clinical Notes</label>
            <Textarea v-model="info.clinical_notes" rows="3" autoResize fluid placeholder="Optional clinical notes..." class="text-sm" />
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
          <Button type="submit" label="Save Request" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- View Results Dialog (read-only) -->
    <Dialog v-model:visible="resultsModalOpen" modal :style="{ width: '46vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <GiMicroscope class="text-white" size="16" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Lab Results</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ activeRequest?.lab_test?.name || "—" }} &bull; {{ activeRequest?.request_number || "—" }}</p>
          </div>
        </div>
      </template>
      <div class="flex flex-col gap-3 pt-2">
        <div v-if="resultRows.length === 0" class="text-sm text-slate-400 italic py-6 text-center">No results have been recorded for this request yet.</div>
        <div v-for="row in resultRows" :key="row.parameter_pid" class="rounded-lg border border-slate-200 p-3 flex items-center justify-between gap-3 bg-slate-50/50">
          <div>
            <span class="text-sm font-medium text-slate-700">{{ row.parameter_name }}</span>
            <p class="text-xs text-slate-400">{{ row.reference_range || "No reference range" }}{{ row.unit ? ` (${row.unit})` : "" }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-semibold" :class="row.is_abnormal ? 'text-red-600' : 'text-slate-800'">{{ row.result_value || "—" }}</span>
            <Tag v-if="row.is_abnormal" value="Abnormal" severity="danger" />
          </div>
        </div>
        <div class="flex pt-1">
          <Button type="button" label="Close" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" @click="resultsModalOpen = false" />
        </div>
      </div>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <GiTestTubes class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Lab Requests</h3>
          <p class="text-xs text-slate-400">
            <template v-if="patient">{{ patientName }} &bull; Case {{ currentCase?.case_number || "—" }}</template>
            <template v-else>Loading patient...</template>
          </p>
        </div>
      </div>
      <button
        v-if="can('lab-requests', 'create')"
        type="button"
        @click="openCreate"
        :disabled="!patientCasePid"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
      >
        <BsPlusCircle size="16" />
        New Lab Request
      </button>
    </div>

    <!-- Table -->
    <DataTable
      :value="labRequests"
      paginator
      :rows="15"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 60rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <GiTestTubes size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No lab requests found</p>
          <p class="text-xs mt-1">Click "New Lab Request" to order the first test</p>
        </div>
      </template>

      <Column header="Request #" class="w-36">
        <template #body="{ data }"><span class="text-slate-700 text-sm font-mono">{{ data.request_number }}</span></template>
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

      <Column header="Status" class="w-36">
        <template #body="{ data }"><Tag :value="data.status" :severity="statusSeverity(data.status)" /></template>
      </Column>

      <Column header="Ordered By" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ `${data.doctor?.firstname ?? ""} ${data.doctor?.lastname ?? ""}`.trim() || "—" }}</span>
        </template>
      </Column>

      <Column header="Actions" class="w-24">
        <template #body="{ data }">
          <div class="flex items-center gap-1">
            <button type="button" title="View results" @click="openResults(data)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
              <GiMicroscope size="18" />
            </button>
            <button v-if="can('lab-requests', 'delete')" type="button" title="Delete request" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { GiTestTubes, GiMicroscope } from "vue-icons-plus/gi";
import { useLabRequestStore } from "@/store/patientchart/LabRequests";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useLabTestStore } from "@/store/LabTest";
import { LabRequest, LabTest } from "@/interface/Interfaces";
import { useConfirmToast } from "@/composables/confirm";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const route = useRoute();
const labRequestStore = useLabRequestStore();
const patientCaseStore = usePatientCaseStore();
const labTestStore = useLabTestStore();

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const currentCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => currentCase.value?.patient);
const labTests = computed<LabTest[]>(() => labTestStore.tests);
const labRequests = computed<LabRequest[]>(() => labRequestStore.labRequests);

const patientName = computed(() => `${patient.value?.firstname ?? ""} ${patient.value?.lastname ?? ""}`.trim() || "—");
const labTestOptionLabel = (data: LabTest) => `${data.code} — ${data.name}`;

const priorityOptions = [
  { label: "Routine", value: "routine" },
  { label: "Urgent", value: "urgent" },
  { label: "Stat", value: "stat" },
];

const modalOpen = ref<boolean>(false);
const defaultInfo = (): LabRequest => ({ pid: "", patient_case_pid: "", lab_test_pid: "", priority: "routine", clinical_notes: "" });
const info = reactive<LabRequest>(defaultInfo());

watch(modalOpen, (open) => {
  if (!open) Object.assign(info, defaultInfo());
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
    case "in_progress":
    case "sample_collected":
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
    await labRequestStore.read(patientCasePid.value);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve lab requests");
  }
};

onMounted(async () => {
  await Promise.all([loadContext(), labTestStore.read()]);
  await refresh();
});

const openCreate = () => {
  if (!patientCasePid.value) return;
  modalOpen.value = true;
};

const create = async () => {
  try {
    info.patient_case_pid = patientCasePid.value || "";
    await labRequestStore.create(info);
    toast.success("Lab request created successfully");
    modalOpen.value = false;
    await refresh();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to create lab request");
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
    await labRequestStore.view(row.pid);
    const full = labRequestStore.labRequest;
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

const archive = (pid: string) => {
  showConfirm({
    message: "Are you sure you want to delete this lab request?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await labRequestStore.archive(pid);
        toast.success("Lab request deleted successfully");
        await refresh();
      } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to delete lab request");
      }
    },
  });
};
</script>
