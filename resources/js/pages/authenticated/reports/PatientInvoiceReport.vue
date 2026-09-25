<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col xl:flex-row xl:items-end justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FaFileInvoiceDollar class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Patient Invoice Report</h3>
          <p class="text-xs text-slate-400">Cash in per patient case, grouped into PF, fees, medicines and supplies</p>
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
        <div class="flex flex-col gap-1">
          <label class="text-xs font-medium text-slate-600">Case Type</label>
          <Select v-model="caseType" :options="caseTypeOptions" optionLabel="label" optionValue="value" class="text-sm w-36" />
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-medium text-slate-600">Status</label>
          <Select v-model="status" :options="statusOptions" optionLabel="label" optionValue="value" class="text-sm w-40" />
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
        <p class="text-base">Patient Invoice Report (Cash In) &bull; {{ periodLabel }}</p>
      </div>

      <div class="flex flex-col 2xl:flex-row gap-6 items-start">
        <div class="overflow-x-auto w-full">
          <table class="report-table w-full min-w-3xl text-base">
            <thead>
              <tr>
                <th class="text-left">Cash In</th>
                <th class="text-left">Patient Name</th>
                <th class="text-center">Patient Type</th>
                <th class="text-left">Particulars</th>
                <th class="text-right w-44">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="5" class="text-center py-10 text-slate-400">Loading report...</td>
              </tr>
              <tr v-else-if="!cases.length">
                <td colspan="5" class="text-center py-10 text-slate-400">No invoices for this period</td>
              </tr>
              <template v-else>
                <tr v-for="(row, index) in lines" :key="index" :class="rowClass[row.kind]">
                  <template v-if="row.kind === 'case'">
                    <td class="whitespace-nowrap">
                      <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-sm font-semibold">{{ row.case.case_type_label }}</span>
                    </td>
                    <td class="font-semibold text-slate-800 uppercase">
                      {{ row.case.patient_name }}
                      <span class="block text-xs font-normal normal-case text-slate-400">{{ row.case.case_number }} &bull; {{ row.case.invoice_numbers.join(", ") }}</span>
                    </td>
                    <td class="text-center font-medium text-slate-700 uppercase" :title="row.case.patient_type_name || ''">{{ row.case.patient_type || "—" }}</td>
                    <td class="font-medium text-slate-700">PF</td>
                    <td><Peso :value="row.amount" /></td>
                  </template>
                  <template v-else>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td :class="{ 'pl-6': row.kind === 'item' }">
                      {{ row.text }}
                      <span v-if="row.kind === 'item' && row.quantity > 1" class="text-sm text-slate-400">x{{ row.quantity }}</span>
                    </td>
                    <td><Peso v-if="row.amount !== null" :value="row.amount" /></td>
                  </template>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Summary -->
        <div v-if="cases.length && !loading" class="summary w-full 2xl:w-80 shrink-0 flex flex-col gap-3">
          <div v-for="type in totals.by_type" :key="type.label" class="rounded-md border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">{{ type.label }} Cash In</p>
            <p class="mt-1 text-xl font-bold text-slate-800"><Peso :value="type.total" /></p>
          </div>
          <table class="report-table w-full text-base">
            <tbody>
              <tr v-for="section in totals.by_section" :key="section.label">
                <td>{{ section.label }}</td>
                <td class="w-40"><Peso :value="section.total" /></td>
              </tr>
              <tr v-if="totals.discount">
                <td>LESS: DISCOUNT</td>
                <td><Peso :value="-totals.discount" /></td>
              </tr>
              <tr v-if="totals.tax">
                <td>ADD: TAX</td>
                <td><Peso :value="totals.tax" /></td>
              </tr>
              <tr class="font-semibold bg-emerald-50 text-emerald-800">
                <td>GRAND TOTAL</td>
                <td><Peso :value="totals.grand_total" /></td>
              </tr>
              <tr>
                <td>COLLECTED</td>
                <td><Peso :value="totals.paid" /></td>
              </tr>
              <tr :class="{ 'text-red-500 font-medium': totals.balance > 0 }">
                <td>BALANCE</td>
                <td><Peso :value="totals.balance" /></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, onMounted, ref } from "vue";
import { FaFileInvoiceDollar } from "vue-icons-plus/fa";
import { BiPrinter, BiRefresh } from "vue-icons-plus/bi";
import { useReportStore } from "@/store/Reports";
import { useAppToast } from "@/composables/toast";
import { usePrintElement } from "@/composables/print";
import { PatientInvoiceReportCase } from "@/interface/Interfaces";

type Line =
  | { kind: "case"; case: PatientInvoiceReportCase; amount: number }
  | { kind: "section" | "item" | "adjust" | "total"; text: string; quantity: number; amount: number | null };

const facilityName = import.meta.env.VITE_FACILITY_NAME;
const toast = useAppToast();
const reportStore = useReportStore();

const cases = computed(() => reportStore.patientInvoice.cases);
const totals = computed(() => reportStore.patientInvoice.totals);

const now = new Date();
const dateFrom = ref<Date | null>(new Date(now.getFullYear(), now.getMonth(), 1));
const dateTo = ref<Date | null>(new Date(now.getFullYear(), now.getMonth() + 1, 0));
const caseType = ref<string>("");
const status = ref<string>("");
const loading = ref<boolean>(false);

const caseTypeOptions = [
  { label: "All", value: "" },
  { label: "OPD", value: "outpatient" },
  { label: "IPD", value: "inpatient" },
];
const statusOptions = [
  { label: "All", value: "" },
  { label: "Paid", value: "paid" },
  { label: "Partially Paid", value: "partially_paid" },
  { label: "Unpaid", value: "unpaid" },
];

const rowClass: Record<Line["kind"], string> = {
  case: "case-row",
  section: "section-row",
  item: "item-row",
  adjust: "item-row italic",
  total: "total-row",
};

// Flattens each case into spreadsheet rows: the case header carries the summed
// PF (like the source sheet), then each remaining section gets a label row
// followed by its items, then any discount/tax and the case total.
const lines = computed<Line[]>(() =>
  cases.value.flatMap((c) => {
    const pf = c.sections.find((s) => s.category === "Professional Fee");
    const rows: Line[] = [{ kind: "case", case: c, amount: pf?.subtotal ?? 0 }];

    c.sections
      .filter((s) => s.category !== "Professional Fee")
      .forEach((section) => {
        rows.push({ kind: "section", text: section.label, quantity: 0, amount: null });
        section.items.forEach((item) => rows.push({ kind: "item", text: item.description, quantity: item.quantity, amount: item.amount }));
      });

    if (c.discount) rows.push({ kind: "adjust", text: "Less: Discount", quantity: 0, amount: -c.discount });
    if (c.tax) rows.push({ kind: "adjust", text: "Add: Tax", quantity: 0, amount: c.tax });
    rows.push({ kind: "total", text: c.balance > 0 ? `TOTAL (balance ₱${formatAmount(c.balance)})` : "TOTAL", quantity: 0, amount: c.total });

    return rows;
  })
);

const formatAmount = (value: number) => Number(value).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

// Accounting-style peso cell: "₱" pinned left, amount right, "-" for zero.
const Peso = defineComponent({
  props: { value: { type: Number, required: true } },
  setup: (props) => () =>
    h("span", { class: "flex justify-between gap-2" }, [h("span", "₱"), h("span", Number(props.value) ? formatAmount(props.value) : "-")]),
});

const toYmd = (date: Date | null) => {
  if (!date) return undefined;
  const pad = (n: number) => String(n).padStart(2, "0");
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
};

const periodLabel = computed(() => {
  const from = toYmd(dateFrom.value);
  const to = toYmd(dateTo.value);
  if (!from && !to) return "All dates";
  return `${from ?? "Beginning"} to ${to ?? "Present"}`;
});

const load = async () => {
  loading.value = true;
  try {
    await reportStore.readPatientInvoice({
      date_from: toYmd(dateFrom.value),
      date_to: toYmd(dateTo.value),
      case_type: caseType.value || undefined,
      status: status.value || undefined,
    });
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to generate report");
  } finally {
    loading.value = false;
  }
};

const { printElement } = usePrintElement();
const printArea = ref<HTMLElement | null>(null);
const print = () => printElement(printArea.value, { title: "Patient Invoice Report", orientation: "portrait" });

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
.case-row td {
  background: #f8fafc; /* slate-50 */
  border-top: 2px solid #cbd5e1; /* slate-300 */
}
.section-row td {
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: #047857; /* emerald-700 */
}
.total-row td {
  font-weight: 600;
  color: #1e293b; /* slate-800 */
}
</style>

<style>
/* Applied inside the print iframe (see composables/print.ts). */
@media print {
  .print-area {
    padding: 0 !important;
  }
  .print-area thead {
    display: table-header-group;
  }
  .print-area tr {
    break-inside: avoid;
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
  .print-area .summary {
    width: 18rem !important;
    break-inside: avoid;
  }
}
</style>
