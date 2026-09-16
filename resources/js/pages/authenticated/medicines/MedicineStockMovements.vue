<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <MdSwapVert class="text-white" size="18" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Record Stock Movement</h2>
            <p class="text-xs text-slate-400 mt-0.5">Logs an IN/OUT entry for this medicine (audit trail only)</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="create" class="flex flex-col gap-5 pt-2">
        <div class="grid grid-cols-2 gap-x-4 gap-y-4">
          <div class="col-span-2 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Medicine Stocks<span class="text-red-400">*</span></label>
            <Select
              v-model="info.medicine_stock_pid"
              :options="medicineStocks"
              :optionLabel="(stock) => `${stock.medicine.name} - (${stock.quantity}) Batch: ${stock.batch_number || 0}`"
              optionValue="pid"
              placeholder="Select medicine stock"
              filter
              fluid
              class="text-sm"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Type <span class="text-red-400">*</span></label>
            <Select v-model="info.type" :options="['IN', 'OUT']" placeholder="Select type" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Quantity <span class="text-red-400">*</span></label>
            <InputNumber v-model="info.quantity" :useGrouping="false" placeholder="0" required fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Reference</label>
            <InputText v-model="info.reference" placeholder="e.g. prescription #, delivery #" fluid class="text-sm" />
          </div>
          <div class="col-span-2 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Remarks</label>
            <InputText v-model="info.remarks" placeholder="Optional notes" fluid class="text-sm" />
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
          <Button type="submit" label="Save Movement" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <MdSwapVert class="text-white" size="20" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Medicine Stock Movements</h3>
          <p class="text-xs text-slate-400">Audit trail of medicine IN/OUT entries</p>
        </div>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <div class="relative flex-1 sm:w-64">
          <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
          <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
        </div>
        <!-- <button
          v-if="can('medicine-stock-movements', 'create')"
          type="button"
          @click="modalOpen = true"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0"
        >
          <BsPlusCircle size="16" />
          Record Movement
        </button> -->
      </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-end gap-4 bg-slate-50/50">
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-medium text-slate-500">Type</label>
        <div class="flex items-center gap-1.5 bg-slate-100 rounded-lg p-1">
          <button
            v-for="option in typeOptions"
            :key="option.value"
            type="button"
            @click="typeFilter = option.value"
            class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors duration-150"
            :class="typeFilter === option.value ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
          >
            {{ option.label }}
          </button>
        </div>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-medium text-slate-500">From</label>
        <DatePicker v-model="dateFrom" dateFormat="yy-mm-dd" showIcon showButtonBar placeholder="Any" class="text-sm w-40" />
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-medium text-slate-500">To</label>
        <DatePicker v-model="dateTo" dateFormat="yy-mm-dd" showIcon showButtonBar placeholder="Any" class="text-sm w-40" />
      </div>
      <button v-if="hasFilters" type="button" @click="resetFilters" class="text-xs font-semibold text-slate-500 hover:text-red-500 transition-colors duration-150 px-2 py-2">Clear filters</button>
    </div>

    <DataTable
      :value="medicineStockMovements"
      lazy
      paginator
      :rows="rows"
      :first="first"
      :totalRecords="total"
      :loading="loading"
      @page="onPage"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 55rem"
      :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <MdSwapVert size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No movements recorded</p>
        </div>
      </template>
      <Column header="Medicine">
        <template #body="{ data }"
          ><span class="text-slate-800 text-sm font-medium"> {{ data.medicine_stock?.medicine?.name || "-" }}</span></template
        >
      </Column>
      <Column header="Type" class="w-24">
        <template #body="{ data }">
          <Tag :value="data.type" :severity="data.type === 'IN' ? 'success' : 'warn'" />
        </template>
      </Column>
      <Column field="quantity" header="Quantity" class="w-24" />
      <!-- <Column header="Reference" class="w-32">
        <template #body="{ data }"
          ><span class="text-slate-600 text-sm">{{ data.reference || "—" }}</span></template
        >
      </Column> -->
      <Column header="Remarks">
        <template #body="{ data }"
          ><span class="text-slate-600 text-sm">{{ data.remarks || "—" }}</span></template
        >
      </Column>
      <Column header="Date" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-500 text-xs">{{ formatDate(data.created_at) }}</span></template
        >
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from "vue";
import { BsPlusCircle } from "vue-icons-plus/bs";
import { FiSearch } from "vue-icons-plus/fi";
import { MdSwapVert } from "vue-icons-plus/md";
import { useMedicineStockMovementStore } from "@/store/MedicineStockMovement";
import { useMedicineStockStore } from "@/store/MedicineStock";
// import { useMedicineStore } from "@/store/Medicine";
import { MedicineStockMovement } from "@/interface/Interfaces";
import { useApiTable } from "@/composables/apiTable";
import { useAppToast } from "@/composables/toast";
import { usePermission } from "@/composables/permission";
const { can } = usePermission();
const toast = useAppToast();
const medicineStockMovementStore = useMedicineStockMovementStore();
// const medicineStore = useMedicineStore();
const medicineStockStore = useMedicineStockStore();
const medicineStockMovements = computed<MedicineStockMovement[]>(() => medicineStockMovementStore.medicineStockMovements);
// const medicines = computed(() => medicineStore.medicines);
const modalOpen = ref<boolean>(false);
const defaultInfo = (): MedicineStockMovement => ({ medicine_stock_pid: "", type: "IN", quantity: 0, reference: "", remarks: "" });
const info = reactive<MedicineStockMovement>(defaultInfo());
const medicineStocks = computed(() => medicineStockStore.medicineStocks);
watch(modalOpen, (open) => {
  if (!open) {
    Object.assign(info, defaultInfo());
  }
});

const formatDate = (value?: string) => (value ? new Date(value).toLocaleString() : "—");

const typeOptions: { label: string; value: "ALL" | "IN" | "OUT" }[] = [
  { label: "All", value: "ALL" },
  { label: "In", value: "IN" },
  { label: "Out", value: "OUT" },
];
const typeFilter = ref<"ALL" | "IN" | "OUT">("ALL");
const dateFrom = ref<Date | null>(null);
const dateTo = ref<Date | null>(null);
const hasFilters = computed(() => typeFilter.value !== "ALL" || !!dateFrom.value || !!dateTo.value);

const toDateParam = (date: Date | null) => {
  if (!date) return undefined;
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
};

const resetFilters = () => {
  typeFilter.value = "ALL";
  dateFrom.value = null;
  dateTo.value = null;
};

const { search, rows, first, total, loading, onPage, onSearch, reload, page } = useApiTable(
  async (params) => {
    try {
      await medicineStockMovementStore.read({
        ...params,
        type: typeFilter.value !== "ALL" ? typeFilter.value : undefined,
        date_from: toDateParam(dateFrom.value),
        date_to: toDateParam(dateTo.value),
      });
    } catch (err: any) {
      toast.error(err.response?.data?.message || "Failed to retrieve movements");
    }
  },
  () => medicineStockMovementStore.meta
);

watch([typeFilter, dateFrom, dateTo], () => {
  page.value = 1;
  first.value = 0;
  reload();
});

onMounted(() => {
  //   medicineStore.read();
  medicineStockStore.read();
});

const create = async () => {
  try {
    await medicineStockMovementStore.create(info);
    toast.success("Movement recorded successfully");
    modalOpen.value = false;
    await reload();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to record movement");
  }
};
</script>
