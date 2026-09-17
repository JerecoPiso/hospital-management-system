<template>
  <div class="print-page">
    <!-- Toolbar (hidden when printing) -->
    <div class="print-toolbar">
      <button type="button" class="pt-btn pt-btn--ghost" @click="goBack">
        <FiArrowLeft size="16" />
        Back
      </button>
      <button type="button" class="pt-btn pt-btn--primary" @click="print">
        <FiPrinter size="16" />
        Print
      </button>
    </div>

    <div v-if="loading" class="print-state">Loading invoice...</div>
    <div v-else-if="!invoice?.pid" class="print-state">Invoice not found.</div>

    <div v-else class="print-paper">
      <PrintFormHeader :patient-case="invoice.patient_case" form-title="Billing Invoice" form-subtitle="Statement of Account" :document-date="invoice.created_at" />

      <div class="iv-section">
        <span class="iv-heading">Invoice Information</span>
        <div class="iv-grid">
          <div class="iv-field">
            <span class="iv-label">Invoice No.</span>
            <span class="iv-value">{{ invoice.invoice_number || "—" }}</span>
          </div>
          <div class="iv-field">
            <span class="iv-label">Status</span>
            <span class="iv-value iv-value--caps">{{ (invoice.status || "—").replace("_", " ") }}</span>
          </div>
          <div class="iv-field">
            <span class="iv-label">Prepared By</span>
            <span class="iv-value">{{ preparedByName }}</span>
          </div>
        </div>
      </div>

      <div class="iv-section">
        <span class="iv-heading">Charges</span>
        <div v-if="!invoice.items?.length" class="iv-empty">No items recorded for this invoice.</div>
        <table v-else class="iv-table">
          <thead>
            <tr>
              <th>Description</th>
              <th class="iv-num">Qty</th>
              <th class="iv-num">Unit Price</th>
              <th class="iv-num">Subtotal</th>
            </tr>
          </thead>
          <tbody v-for="group in itemsByCategory" :key="group.category">
            <tr class="iv-category-row">
              <td colspan="4">{{ group.category }}</td>
            </tr>
            <tr v-for="item in group.items" :key="item.pid">
              <td>{{ item.description }}</td>
              <td class="iv-num">{{ item.quantity }}</td>
              <td class="iv-num">₱{{ Number(item.unit_price || 0).toFixed(2) }}</td>
              <td class="iv-num">₱{{ Number(item.subtotal || 0).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="iv-totals">
        <div class="iv-totals-row"><span>Subtotal</span><span>₱{{ Number(invoice.subtotal || 0).toFixed(2) }}</span></div>
        <div v-if="Number(invoice.discount_amount || 0) > 0" class="iv-totals-row"><span>Discount</span><span>-₱{{ Number(invoice.discount_amount || 0).toFixed(2) }}</span></div>
        <div v-if="Number(invoice.tax_amount || 0) > 0" class="iv-totals-row"><span>Tax</span><span>₱{{ Number(invoice.tax_amount || 0).toFixed(2) }}</span></div>
        <div class="iv-totals-row iv-totals-row--total"><span>Total</span><span>₱{{ Number(invoice.total_amount || 0).toFixed(2) }}</span></div>
        <div class="iv-totals-row"><span>Amount Paid</span><span>₱{{ Number(invoice.paid_amount || 0).toFixed(2) }}</span></div>
        <div class="iv-totals-row iv-totals-row--balance"><span>Balance Due</span><span>₱{{ Number(invoice.balance || 0).toFixed(2) }}</span></div>
      </div>

      <div v-if="invoice.payments?.length" class="iv-section">
        <span class="iv-heading">Payment History</span>
        <table class="iv-table">
          <thead>
            <tr>
              <th>Receipt No.</th>
              <th>Date</th>
              <th>Method</th>
              <th>Reference</th>
              <th class="iv-num">Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="payment in invoice.payments" :key="payment.pid">
              <td>{{ payment.receipt_number || "—" }}</td>
              <td>{{ formatDate(payment.paid_at) }}</td>
              <td class="iv-value--caps">{{ (payment.payment_method || "—").replace("_", " ") }}</td>
              <td>{{ payment.reference_number || "—" }}</td>
              <td class="iv-num">₱{{ Number(payment.amount_paid || 0).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="iv-sign">
        <p class="iv-sign-name uppercase">{{ preparedByName }}</p>
        <div class="iv-sign-line"></div>
        <p class="iv-sign-lic">Billing / Cashier</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FiPrinter, FiArrowLeft } from "vue-icons-plus/fi";
import PrintFormHeader from "@/components/print/PrintFormHeader.vue";
import { useInvoiceStore } from "@/store/patientchart/Invoices";
import { useAppToast } from "@/composables/toast";

const route = useRoute();
const router = useRouter();
const toast = useAppToast();
const invoiceStore = useInvoiceStore();

const loading = ref(true);
const invoice = computed(() => invoiceStore.invoice);

const preparedByName = computed(() => {
  const u = invoice.value.created_by;
  if (!u) return "—";
  return `${u.firstname ?? ""} ${u.lastname ?? ""}`.trim() || "—";
});

const itemsByCategory = computed(() => {
  const items = invoice.value.items || [];
  const groups: { category: string; items: typeof items }[] = [];
  items.forEach((item) => {
    const category = item.category || "Other";
    let group = groups.find((g) => g.category === category);
    if (!group) {
      group = { category, items: [] };
      groups.push(group);
    }
    group.items.push(item);
  });
  return groups;
});

const formatDate = (value?: string | null) => (value ? new Date(value).toLocaleString() : "—");

onMounted(async () => {
  const pid = route.params.pid as string;
  try {
    await invoiceStore.view(pid);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve invoice");
  } finally {
    loading.value = false;
  }
});

const print = () => window.print();
const goBack = () => {
  if (window.history.length > 1) router.back();
  else window.close();
};
</script>

<style scoped>
.print-page {
  min-height: 100vh;
  background: #f1f5f9;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
.print-toolbar {
  width: 100%;
  max-width: 820px;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
.pt-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid transparent;
  cursor: pointer;
}
.pt-btn--ghost {
  background: #fff;
  border-color: #cbd5e1;
  color: #334155;
}
.pt-btn--ghost:hover {
  background: #f8fafc;
}
.pt-btn--primary {
  background: linear-gradient(to right, #10b981, #0d9488);
  color: #fff;
}
.pt-btn--primary:hover {
  opacity: 0.92;
}
.print-state {
  color: #94a3b8;
  font-style: italic;
  padding: 60px 0;
}
.print-paper {
  width: 100%;
  max-width: 820px;
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  padding: 28px 32px;
  color: #1e293b;
}
.iv-section {
  margin-top: 20px;
}
.iv-heading {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #0f766e;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 6px;
  margin-bottom: 12px;
}
.iv-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px 18px;
}
.iv-field {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.iv-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.iv-value {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 4px;
  min-height: 19px;
  word-break: break-word;
}
.iv-value--caps {
  text-transform: capitalize;
}
.iv-empty {
  font-size: 13px;
  color: #94a3b8;
  font-style: italic;
  padding: 10px 0;
}
.iv-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
}
.iv-table th,
.iv-table td {
  border: 1px solid #e2e8f0;
  padding: 6px 10px;
  text-align: left;
}
.iv-table th {
  background: #f8fafc;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
}
.iv-num {
  text-align: right;
}
.iv-category-row td {
  background: #ecfdf5;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #0f766e;
}
.iv-totals {
  margin-top: 16px;
  margin-left: auto;
  width: 260px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 13px;
}
.iv-totals-row {
  display: flex;
  justify-content: space-between;
  color: #64748b;
}
.iv-totals-row--total {
  font-weight: 700;
  color: #0f172a;
  border-top: 1px solid #e2e8f0;
  padding-top: 4px;
  margin-top: 2px;
}
.iv-totals-row--balance {
  font-weight: 700;
  color: #dc2626;
}
.iv-sign {
  text-align: center;
  min-width: 220px;
  margin: 40px 0 0 auto;
}
.iv-sign-line {
  border-top: 1px solid #1e293b;
  margin-bottom: 4px;
}
.iv-sign-name {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
}
.iv-sign-lic {
  margin: 2px 0 0;
  font-size: 11px;
  color: #64748b;
}

@media print {
  .print-toolbar {
    display: none !important;
  }
  .print-page {
    background: #fff;
    padding: 0;
    display: block;
  }
  .print-paper {
    border: none;
    border-radius: 0;
    max-width: none;
    margin: 0;
    padding: 0;
  }
}
</style>
