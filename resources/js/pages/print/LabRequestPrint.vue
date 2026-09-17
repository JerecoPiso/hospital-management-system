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

    <div v-if="loading" class="print-state">Loading lab request...</div>
    <div v-else-if="!labRequest?.pid" class="print-state">Lab request not found.</div>

    <div v-else class="print-paper">
      <PrintFormHeader :patient-case="labRequest.patient_case" form-title="Laboratory Request" form-subtitle="Laboratory Test Order" />

      <div class="lr-section">
        <span class="lr-heading">Request Information</span>
        <div class="lr-grid">
          <div class="lr-field">
            <span class="lr-label">Request No.</span>
            <span class="lr-value">{{ labRequest.request_number || "—" }}</span>
          </div>
          <div class="lr-field">
            <span class="lr-label">Priority</span>
            <span class="lr-value lr-value--caps">{{ labRequest.priority || "—" }}</span>
          </div>
          <div class="lr-field">
            <span class="lr-label">Status</span>
            <span class="lr-value lr-value--caps">{{ labRequest.status || "—" }}</span>
          </div>
          <div class="lr-field lr-field--grow">
            <span class="lr-label">Lab Test</span>
            <span class="lr-value">{{ labRequest.lab_test?.name || "—" }}</span>
          </div>
          <div class="lr-field">
            <span class="lr-label">Category</span>
            <span class="lr-value">{{ labRequest.lab_test?.category?.name || "—" }}</span>
          </div>
          <div class="lr-field">
            <span class="lr-label">Ordered By</span>
            <span class="lr-value">{{ doctorName }}</span>
          </div>
        </div>
      </div>

      <div class="lr-section">
        <span class="lr-heading">Clinical Notes</span>
        <p class="lr-block">{{ labRequest.clinical_notes || "—" }}</p>
      </div>

      <div class="lr-section">
        <span class="lr-heading">Results</span>
        <div v-if="resultRows.length === 0" class="lr-empty">No results have been recorded for this request yet.</div>
        <table v-else class="lr-table">
          <thead>
            <tr>
              <th>Parameter</th>
              <th>Result</th>
              <th>Reference Range</th>
              <th>Unit</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in resultRows" :key="row.parameter_pid">
              <td>{{ row.parameter_name }}</td>
              <td :class="{ 'lr-abnormal': row.is_abnormal }">{{ row.result_value || "—" }}<span v-if="row.is_abnormal"> *</span></td>
              <td>{{ row.reference_range || "—" }}</td>
              <td>{{ row.unit || "—" }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="lr-sign">
        <p class="lr-sign-name uppercase">{{ doctorName }}</p>
        <div class="lr-sign-line"></div>
        <p class="lr-sign-lic">Lic. No. {{ doctorLicense || "__________" }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FiPrinter, FiArrowLeft } from "vue-icons-plus/fi";
import PrintFormHeader from "@/components/print/PrintFormHeader.vue";
import { useLabRequestStore } from "@/store/patientchart/LabRequests";
import { useAppToast } from "@/composables/toast";

const route = useRoute();
const router = useRouter();
const toast = useAppToast();
const labRequestStore = useLabRequestStore();

const loading = ref(true);
const labRequest = computed(() => labRequestStore.labRequest);

const doctor = computed(() => labRequest.value.doctor);
const doctorName = computed(() => {
  const d = doctor.value;
  if (!d) return "Attending Physician";
  const name = `${d.firstname ?? ""} ${d.middlename ? d.middlename + " " : ""}${d.lastname ?? ""}`.replace(/\s+/g, " ").trim();
  return name ? `${name}${d.suffix ? ", " + d.suffix : ""}, M.D.` : "Attending Physician";
});
const doctorLicense = computed(() => doctor.value?.license_no || "");

interface ResultRow {
  parameter_pid: string;
  parameter_name: string;
  unit?: string | null;
  reference_range?: string | null;
  result_value: string;
  is_abnormal: boolean;
}

const resultRows = computed<ResultRow[]>(() => {
  const parameters = labRequest.value.lab_test?.parameters || [];
  return parameters.map((param: any) => {
    const existing = (labRequest.value.results || []).find((r: any) => r.parameter?.pid === param.pid);
    return {
      parameter_pid: param.pid,
      parameter_name: param.parameter_name,
      unit: param.unit,
      reference_range: param.reference_range,
      result_value: existing?.result_value || "",
      is_abnormal: existing?.is_abnormal || false,
    };
  });
});

onMounted(async () => {
  const pid = route.params.pid as string;
  try {
    await labRequestStore.view(pid);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve lab request");
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
.lr-section {
  margin-top: 20px;
}
.lr-heading {
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
.lr-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px 18px;
}
.lr-field {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.lr-field--grow {
  grid-column: span 2;
}
.lr-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.lr-value {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 4px;
  min-height: 19px;
  word-break: break-word;
}
.lr-value--caps {
  text-transform: capitalize;
}
.lr-block {
  font-size: 13px;
  color: #334155;
  white-space: pre-wrap;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 8px;
  min-height: 20px;
  margin: 0;
}
.lr-empty {
  font-size: 13px;
  color: #94a3b8;
  font-style: italic;
  padding: 10px 0;
}
.lr-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
}
.lr-table th,
.lr-table td {
  border: 1px solid #e2e8f0;
  padding: 6px 10px;
  text-align: left;
}
.lr-table th {
  background: #f8fafc;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
}
.lr-abnormal {
  color: #dc2626;
  font-weight: 700;
}
.lr-sign {
  text-align: center;
  min-width: 220px;
  margin: 40px 0 0 auto;
}
.lr-sign-line {
  border-top: 1px solid #1e293b;
  margin-bottom: 4px;
}
.lr-sign-name {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
}
.lr-sign-lic {
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
