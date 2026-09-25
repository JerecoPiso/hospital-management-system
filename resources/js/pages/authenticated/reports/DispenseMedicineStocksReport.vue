<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Header -->
    <div class="no-print px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-end justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaPills class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Dispense Medicine Stocks Report</h3>
          <p class="text-xs text-slate-400">Dispensed medicines and doctors' fees per patient case</p>
        </div>
      </div>
      <div class="flex flex-wrap items-end gap-2">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-medium text-slate-600">From</label>
          <DatePicker v-model="dateFrom" dateFormat="yy-mm-dd" showIcon showButtonBar placeholder="Any" class="text-sm w-40" />
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-medium text-slate-600">To</label>
          <DatePicker v-model="dateTo" dateFormat="yy-mm-dd" showIcon showButtonBar placeholder="Any" class="text-sm w-40" />
        </div>
        <button
          type="button"
          @click="load"
          :disabled="loading"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md active:scale-95 disabled:opacity-50"
        >
          <BiRefresh size="16" :class="{ 'animate-spin': loading }" />
          Generate
        </button>
        <button type="button" @click="print" class="flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium shadow-sm active:scale-95">
          <BiPrinter size="16" />
          Print
        </button>
      </div>
    </div>

    <!-- Report -->
    <div ref="printArea" class="print-area p-4 md:p-6">
      <div class="hidden print-only mb-3">
        <h2 class="text-lg font-bold">{{ facilityName }}</h2>
        <p class="text-base">Dispense Medicine Stocks Report &bull; {{ periodLabel }}</p>
      </div>

      <div class="overflow-x-auto">
        <table class="report-table w-full min-w-5xl text-base">
          <thead>
            <tr>
              <th class="text-left">Date</th>
              <th class="text-left">Case No.</th>
              <th class="text-left">Patient Name</th>
              <th class="text-left">Medicines</th>
              <th class="text-center">Stocks</th>
              <th class="text-center">Out pcs</th>
              <th class="text-right">Amount</th>
              <th class="text-right">Dispense Balance</th>
              <th class="text-right">Doctors Fee</th>
              <th class="text-right">Sold Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="10" class="text-center py-10 text-slate-400">Loading report...</td>
            </tr>
            <tr v-else-if="!rows.length">
              <td colspan="10" class="text-center py-10 text-slate-400">No dispensed medicines or doctors' fees for this period</td>
            </tr>
            <template v-else>
              <tr v-for="(row, index) in rows" :key="index" :class="{ 'case-start': row.is_first_of_case && index > 0 }">
                <td class="whitespace-nowrap">{{ formatDate(row.date) }}</td>
                <td class="whitespace-nowrap">{{ row.is_first_of_case ? row.case_number || "" : "" }}</td>
                <td class="font-semibold text-slate-800 uppercase">{{ row.is_first_of_case ? row.patient_name : "" }}</td>
                <td>{{ row.medicine || "" }}</td>
                <td class="text-center">
                  <span v-if="row.stocks !== null && row.stock_is_estimate" class="italic text-slate-400" title="Estimated: this dispense was recorded before stock snapshots were saved">~{{ row.stocks }}</span>
                  <template v-else>{{ row.stocks ?? "" }}</template>
                </td>
                <td class="text-center">{{ row.medicine ? row.out_pcs : "" }}</td>
                <td class="text-right"><Peso v-if="row.amount !== null" :value="row.amount" /></td>
                <td class="text-right"><Peso v-if="row.dispense_balance !== null" :value="row.dispense_balance" /></td>
                <td class="text-right"><Peso v-if="row.doctor_fee !== null" :value="row.doctor_fee" /></td>
                <td class="text-right"><Peso :value="row.sold_amount" /></td>
              </tr>
            </template>
          </tbody>
          <tfoot v-if="rows.length && !loading">
            <tr class="total-row">
              <td colspan="5" class="text-right">TOTAL</td>
              <td class="text-center">{{ totals.out_pcs }}</td>
              <td></td>
              <td></td>
              <td class="text-right"><Peso :value="totals.doctor_fee" /></td>
              <td class="text-right"><Peso :value="totals.sold_amount" /></td>
            </tr>
            <tr class="grand-total-row">
              <td colspan="9" class="text-right">GRAND TOTAL (SOLD + DOCTORS FEE)</td>
              <td class="text-right"><Peso :value="totals.grand_total" /></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <p v-if="rows.some((row) => row.stock_is_estimate)" class="mt-2 text-sm italic text-slate-400">~ Estimated stock: dispensed before stock snapshots were recorded.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, onMounted, ref } from "vue";
import { FaPills } from "vue-icons-plus/fa";
import { BiPrinter, BiRefresh } from "vue-icons-plus/bi";
import { useReportStore } from "@/store/Reports";
import { useAppToast } from "@/composables/toast";
import { usePrintElement } from "@/composables/print";

const facilityName = import.meta.env.VITE_FACILITY_NAME;
const toast = useAppToast();
const reportStore = useReportStore();

const rows = computed(() => reportStore.dispenseMedicineStocks.rows);
const totals = computed(() => reportStore.dispenseMedicineStocks.totals);

const now = new Date();
const dateFrom = ref<Date | null>(new Date(now.getFullYear(), now.getMonth(), 1));
const dateTo = ref<Date | null>(new Date(now.getFullYear(), now.getMonth() + 1, 0));
const loading = ref<boolean>(false);

// Accounting-style peso cell: "₱" pinned left, amount right, "-" for zero.
const Peso = defineComponent({
  props: { value: { type: Number, required: true } },
  setup: (props) => () =>
    h("span", { class: "flex justify-between gap-2" }, [
      h("span", "₱"),
      h("span", Number(props.value) ? Number(props.value).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : "-"),
    ]),
});

const toYmd = (date: Date | null) => {
  if (!date) return undefined;
  const pad = (n: number) => String(n).padStart(2, "0");
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
};

const formatDate = (value: string | null) => (value ? new Date(value.replace(" ", "T")).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }) : "");

const periodLabel = computed(() => {
  const from = toYmd(dateFrom.value);
  const to = toYmd(dateTo.value);
  if (!from && !to) return "All dates";
  return `${from ?? "Beginning"} to ${to ?? "Present"}`;
});

const load = async () => {
  loading.value = true;
  try {
    await reportStore.readDispenseMedicineStocks({ date_from: toYmd(dateFrom.value), date_to: toYmd(dateTo.value) });
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to generate report");
  } finally {
    loading.value = false;
  }
};

const { printElement } = usePrintElement();
const printArea = ref<HTMLElement | null>(null);
const print = () => printElement(printArea.value, { title: "Dispense Medicine Stocks Report", orientation: "landscape" });

onMounted(load);
</script>

<style scoped>
.report-table {
  border-collapse: collapse;
}
/* Slate/emerald palette to match the rest of the app's tables. */
.report-table th,
.report-table td {
  border: 1px solid #e2e8f0; /* slate-200 */
  padding: 0.45rem 0.75rem;
  color: #475569; /* slate-600 */
}
.report-table thead th {
  background: #f8fafc; /* slate-50 */
  font-size: 0.875rem;
  font-weight: 600;
  color: #64748b; /* slate-500 */
  white-space: nowrap;
}
.report-table tr.case-start td {
  border-top: 2px solid #cbd5e1; /* slate-300 */
}
.report-table tr.total-row td {
  background: #f8fafc; /* slate-50 */
  font-weight: 600;
  color: #1e293b; /* slate-800 */
}
.report-table tr.grand-total-row td {
  background: #ecfdf5; /* emerald-50 */
  font-weight: 600;
  color: #065f46; /* emerald-800 */
}
</style>

<style>
/* Applied inside the print iframe (see composables/print.ts). */
@media print {
  .print-area {
    padding: 0 !important;
  }
  .print-area .print-only {
    display: block !important;
  }
  .print-area .overflow-x-auto {
    overflow: visible !important;
  }
  .print-area table {
    min-width: 0 !important;
    font-size: 13px;
  }
  .print-area thead {
    display: table-header-group;
  }
  .print-area tr {
    break-inside: avoid;
  }
}
</style>
