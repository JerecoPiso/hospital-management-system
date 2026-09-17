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

    <div v-if="loading" class="print-state">Loading radiology order...</div>
    <div v-else-if="!radiologyOrder?.pid" class="print-state">Radiology order not found.</div>

    <div v-else class="print-paper">
      <PrintFormHeader :patient-case="radiologyOrder.patientCase" form-title="Radiology Order" form-subtitle="Imaging Procedure Order" />

      <div class="ro-section">
        <span class="ro-heading">Order Information</span>
        <div class="ro-grid">
          <div class="ro-field">
            <span class="ro-label">Order No.</span>
            <span class="ro-value">{{ radiologyOrder.order_number || "—" }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Priority</span>
            <span class="ro-value ro-value--caps">{{ radiologyOrder.priority || "—" }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Status</span>
            <span class="ro-value ro-value--caps">{{ radiologyOrder.status || "—" }}</span>
          </div>
          <div class="ro-field ro-field--grow">
            <span class="ro-label">Procedure</span>
            <span class="ro-value">{{ radiologyOrder.procedure?.name || "—" }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Modality</span>
            <span class="ro-value">{{ radiologyOrder.procedure?.modality?.name || "—" }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Ordered By</span>
            <span class="ro-value">{{ doctorName }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Scheduled Date/Time</span>
            <span class="ro-value">{{ formatDateTime(radiologyOrder.scheduled_at) }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Performed Date/Time</span>
            <span class="ro-value">{{ formatDateTime(radiologyOrder.performed_at) }}</span>
          </div>
          <div class="ro-field">
            <span class="ro-label">Technician</span>
            <span class="ro-value">{{ technicianName }}</span>
          </div>
        </div>
      </div>

      <div class="ro-section">
        <span class="ro-heading">Clinical History</span>
        <p class="ro-block">{{ radiologyOrder.clinical_history || "—" }}</p>
      </div>

      <div class="ro-section">
        <span class="ro-heading">Report</span>
        <div v-if="!radiologyOrder.report" class="ro-empty">No report has been recorded for this order yet.</div>
        <template v-else>
          <div class="ro-grid ro-grid--report">
            <div class="ro-field">
              <span class="ro-label">Status</span>
              <span class="ro-value ro-value--caps">{{ radiologyOrder.report.status || "—" }}</span>
            </div>
            <div class="ro-field">
              <span class="ro-label">Radiologist</span>
              <span class="ro-value">{{ radiologistName }}</span>
            </div>
          </div>
          <div class="ro-report-field">
            <span class="ro-label">Findings</span>
            <p class="ro-block">{{ radiologyOrder.report.findings || "—" }}</p>
          </div>
          <div class="ro-report-field">
            <span class="ro-label">Impression</span>
            <p class="ro-block">{{ radiologyOrder.report.impression || "—" }}</p>
          </div>
        </template>
      </div>

      <div class="ro-sign">
        <p class="ro-sign-name uppercase">{{ doctorName }}</p>
        <div class="ro-sign-line"></div>
        <p class="ro-sign-lic">Lic. No. {{ doctorLicense || "__________" }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FiPrinter, FiArrowLeft } from "vue-icons-plus/fi";
import PrintFormHeader from "@/components/print/PrintFormHeader.vue";
import { useRadiologyOrderStore } from "@/store/patientchart/RadiologyOrders";
import { useAppToast } from "@/composables/toast";

const route = useRoute();
const router = useRouter();
const toast = useAppToast();
const radiologyOrderStore = useRadiologyOrderStore();

const loading = ref(true);
const radiologyOrder = computed(() => radiologyOrderStore.radiologyOrder);

const doctor = computed(() => radiologyOrder.value.doctor);
const doctorName = computed(() => {
  const d = doctor.value;
  if (!d) return "Attending Physician";
  const name = `${d.firstname ?? ""} ${d.middlename ? d.middlename + " " : ""}${d.lastname ?? ""}`.replace(/\s+/g, " ").trim();
  return name ? `${name}${d.suffix ? ", " + d.suffix : ""}, M.D.` : "Attending Physician";
});
const doctorLicense = computed(() => doctor.value?.license_no || "");

const technicianName = computed(() => {
  const t = radiologyOrder.value.technician;
  if (!t) return "—";
  return `${t.firstname ?? ""} ${t.lastname ?? ""}`.trim() || "—";
});

const radiologistName = computed(() => {
  const r = radiologyOrder.value.report?.radiologist;
  if (!r) return "—";
  return `${r.firstname ?? ""} ${r.lastname ?? ""}`.trim() || "—";
});

const formatDateTime = (value?: string | null) => (value ? new Date(value).toLocaleString() : "—");

onMounted(async () => {
  const pid = route.params.pid as string;
  try {
    await radiologyOrderStore.view(pid);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve radiology order");
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
.ro-section {
  margin-top: 20px;
}
.ro-heading {
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
.ro-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px 18px;
}
.ro-grid--report {
  margin-bottom: 14px;
}
.ro-field {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.ro-field--grow {
  grid-column: span 2;
}
.ro-report-field {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-top: 10px;
}
.ro-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.ro-value {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 4px;
  min-height: 19px;
  word-break: break-word;
}
.ro-value--caps {
  text-transform: capitalize;
}
.ro-block {
  font-size: 13px;
  color: #334155;
  white-space: pre-wrap;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 8px;
  min-height: 20px;
  margin: 0;
}
.ro-empty {
  font-size: 13px;
  color: #94a3b8;
  font-style: italic;
  padding: 10px 0;
}
.ro-sign {
  text-align: center;
  min-width: 220px;
  margin: 40px 0 0 auto;
}
.ro-sign-line {
  border-top: 1px solid #1e293b;
  margin-bottom: 4px;
}
.ro-sign-name {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
}
.ro-sign-lic {
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
