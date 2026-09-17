<template>
  <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
    <BsPlusCircle size="40" class="mb-3 opacity-30" />
    <p class="text-sm font-medium">No patient selected</p>
    <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
  </div>

  <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <Dialog v-model:visible="modalOpen" modal :style="{ width: '48vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <BsCashCoin class="text-white" size="16" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? "Edit Fee Charge" : "Charge Fees" }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? "Update this fee charge" : "Bill one or more fee schedule items to this patient case" }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
        <div class="grid grid-cols-1 gap-x-4 gap-y-4">
          <div class="col-span-1 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Charge Date <span class="text-red-400">*</span></label>
            <DatePicker v-model="chargeDateModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD HH:mm" fluid class="text-sm" />
          </div>
          <div class="col-span-1 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Remarks</label>
            <Textarea v-model="info.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
          </div>
        </div>

        <div class="flex flex-col gap-3">
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-slate-700">Fees <span class="text-red-400">*</span></label>
            <button type="button" @click="addItem" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50 text-xs font-medium transition-colors duration-150">
              <BsPlusCircle size="14" />
              Add Fee
            </button>
          </div>

          <div v-for="(item, index) in info.items" :key="index" class="rounded-lg border border-slate-200 p-4 flex flex-col gap-3 bg-slate-50/50">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Fee {{ index + 1 }}</span>
              <button
                type="button"
                title="Remove fee"
                @click="removeItem(index)"
                :disabled="info.items.length === 1"
                class="p-1 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:hover:bg-transparent transition-colors duration-150 cursor-pointer"
              >
                <BiTrash size="16" />
              </button>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div class="col-span-2 flex flex-col gap-1.5">
                <label class="text-xs font-medium text-slate-600">Fee Schedule <span class="text-red-400">*</span></label>
                <Select v-model="item.fee_schedule_pid" :options="feeSchedules" :optionLabel="feeScheduleOptionLabel" optionValue="pid" placeholder="Select fee" filter fluid class="text-sm" />
              </div>
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-slate-600">Quantity <span class="text-red-400">*</span></label>
                <InputNumber v-model="item.quantity" :min="0.01" :minFractionDigits="0" :maxFractionDigits="2" fluid class="text-sm" />
              </div>
              <div class="col-span-3 flex items-center justify-between rounded-lg bg-white border border-slate-200 px-3 py-2">
                <span class="text-xs text-slate-500">Unit Fee</span>
                <span class="text-sm font-semibold text-slate-800">₱{{ Number(unitFeeFor(item.fee_schedule_pid)).toFixed(2) }}</span>
                <span class="text-xs text-slate-400">x{{ item.quantity || 0 }} =</span>
                <span class="text-sm font-bold text-emerald-700">₱{{ (Number(unitFeeFor(item.fee_schedule_pid)) * Number(item.quantity || 0)).toFixed(2) }}</span>
              </div>
              <div class="col-span-3 flex flex-col gap-1.5">
                <label class="text-xs font-medium text-slate-600">Remarks</label>
                <Textarea v-model="item.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between rounded-lg bg-emerald-50 border border-emerald-100 px-4 py-3">
            <span class="text-sm font-semibold text-emerald-800">Total</span>
            <span class="text-base font-bold text-emerald-700">₱{{ totalAmount.toFixed(2) }}</span>
          </div>
        </div>

        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
          <Button type="submit" :label="isUpdate ? 'Update Charge' : 'Save Charge'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <BsCashCoin class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Fee Charges</h3>
          <p class="text-xs text-slate-400">
            <template v-if="patient">{{ patientName }} &bull; Case {{ currentCase?.case_number || "—" }}</template>
            <template v-else>Loading patient...</template>
          </p>
        </div>
      </div>
      <button
        v-if="can('fee-charges', 'create')"
        type="button"
        @click="openCreate"
        :disabled="!patientCasePid"
        title="This patient has no case record yet"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
      >
        <BsPlusCircle size="16" />
        Charge Fees
      </button>
    </div>

    <!-- Table -->
    <DataTable
      :value="feeCharges"
      paginator
      :rows="15"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 55rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <BsCashCoin size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No fees charged</p>
          <p class="text-xs mt-1">Click "Charge Fees" to record the first entry</p>
        </div>
      </template>

      <Column header="Date" class="w-40">
        <template #body="{ data }"><span class="text-slate-600 text-sm">{{ formatDateTime(data.charge_date) }}</span></template>
      </Column>

      <Column header="Charged By" class="w-44">
        <template #body="{ data }">
          <span class="text-slate-700 text-sm">{{ `${data.charged_by?.firstname ?? ""} ${data.charged_by?.lastname ?? ""}`.trim() || "—" }}</span>
        </template>
      </Column>

      <Column header="Fees">
        <template #body="{ data }">
          <ul class="space-y-0.5">
            <li v-for="(item, idx) in data.items" :key="idx" class="text-slate-700 text-sm">
              {{ item.fee_schedule?.name || "—" }}
              <span class="text-slate-400 text-xs">x{{ item.quantity }} @ ₱{{ Number(item.unit_fee).toFixed(2) }}</span>
            </li>
          </ul>
        </template>
      </Column>

      <Column header="Total" class="w-32">
        <template #body="{ data }"><span class="text-slate-800 text-sm font-semibold">₱{{ chargeTotal(data).toFixed(2) }}</span></template>
      </Column>

      <Column header="Remarks" class="w-48">
        <template #body="{ data }">
          <p v-if="data.remarks" class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ data.remarks }}</p>
          <span v-else class="text-slate-300 text-sm italic">None</span>
        </template>
      </Column>

      <Column header="Actions" class="w-24">
        <template #body="{ data }">
          <div class="flex items-center gap-1">
            <button
              v-if="can('fee-charges', 'update')"
              type="button"
              title="Edit charge"
              @click="edit(data.pid)"
              class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
            >
              <BiEdit size="18" />
            </button>
            <button
              v-if="can('fee-charges', 'delete')"
              type="button"
              title="Delete charge"
              @click="archive(data.pid)"
              class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
            >
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
import { BsPlusCircle, BsCashCoin } from "vue-icons-plus/bs";
import { BiEdit, BiTrash } from "vue-icons-plus/bi";
import { useFeeChargeStore } from "@/store/patientchart/FeeCharges";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useFeeScheduleStore } from "@/store/FeeSchedule";
import { FeeCharge, FeeChargeItem, FeeSchedule } from "@/interface/Interfaces";
import { useConfirmToast } from "@/composables/confirm";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const route = useRoute();
const feeChargeStore = useFeeChargeStore();
const patientCaseStore = usePatientCaseStore();
const feeScheduleStore = useFeeScheduleStore();

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const currentCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => currentCase.value?.patient);
const feeSchedules = computed<FeeSchedule[]>(() => feeScheduleStore.schedules);
const feeCharges = computed<FeeCharge[]>(() => feeChargeStore.feeCharges);

const patientName = computed(() => `${patient.value?.firstname ?? ""} ${patient.value?.lastname ?? ""}`.trim() || "—");
const feeScheduleOptionLabel = (data: FeeSchedule) => `${data.code} — ${data.name}`;
const unitFeeFor = (pid?: string) => feeSchedules.value.find((f) => f.pid === pid)?.standard_fee ?? 0;

const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const chargeDateModel = ref<Date | null>(null);

const defaultItem = (): FeeChargeItem => ({ fee_schedule_pid: "", quantity: 1, remarks: "" });
const defaultInfo = (): FeeCharge => ({
  patient_case_pid: "",
  charge_date: "",
  remarks: "",
  items: [defaultItem()],
});
const info = reactive<FeeCharge>(defaultInfo());

const totalAmount = computed(() => info.items.reduce((sum, item) => sum + Number(unitFeeFor(item.fee_schedule_pid)) * Number(item.quantity || 0), 0));
const chargeTotal = (charge: FeeCharge) => (charge.items || []).reduce((sum, item) => sum + Number(item.unit_fee || 0) * Number(item.quantity || 0), 0);

watch(modalOpen, (open) => {
  if (!open) {
    Object.assign(info, defaultInfo());
    chargeDateModel.value = null;
    isUpdate.value = false;
  }
});
watch(chargeDateModel, (val) => {
  info.charge_date = val ? val.toISOString() : "";
});

const formatDateTime = (value?: string) => (value ? new Date(value).toLocaleString() : "—");

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
    await feeChargeStore.read(patientCasePid.value);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve fee charges");
  }
};

onMounted(async () => {
  await Promise.all([loadContext(), feeScheduleStore.read()]);
  await refresh();
});

const addItem = () => info.items.unshift(defaultItem());
const removeItem = (index: number) => {
  if (info.items.length > 1) info.items.splice(index, 1);
};

const openCreate = () => {
  if (!patientCasePid.value) return;
  chargeDateModel.value = new Date();
  modalOpen.value = true;
};

const create = async () => {
  try {
    info.patient_case_pid = patientCasePid.value || "";
    await feeChargeStore.create(info);
    toast.success("Fees charged successfully");
    modalOpen.value = false;
    await refresh();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to charge fees");
  }
};

const edit = async (pid: string) => {
  try {
    await feeChargeStore.view(pid);
    const full = feeChargeStore.feeCharge;
    Object.assign(info, {
      pid: full.pid,
      patient_case_pid: patientCasePid.value || "",
      charge_date: full.charge_date,
      remarks: full.remarks || "",
      items: (full.items || []).map((item) => ({
        fee_schedule_pid: item.fee_schedule?.pid || "",
        quantity: item.quantity,
        remarks: item.remarks || "",
      })),
    });
    chargeDateModel.value = full.charge_date ? new Date(full.charge_date) : null;
    isUpdate.value = true;
    modalOpen.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve fee charge");
  }
};

const update = async () => {
  try {
    await feeChargeStore.update(info);
    toast.success("Fee charge updated successfully");
    modalOpen.value = false;
    await refresh();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update fee charge");
  }
};

const archive = (pid: string) => {
  showConfirm({
    message: "Are you sure you want to delete this fee charge?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await feeChargeStore.archive(pid);
        toast.success("Fee charge deleted successfully");
        await refresh();
      } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to delete fee charge");
      }
    },
  });
};
</script>
