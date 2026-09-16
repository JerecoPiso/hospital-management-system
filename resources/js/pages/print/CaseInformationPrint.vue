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

    <div v-if="loading" class="print-state">Loading case information...</div>
    <div v-else-if="!patientCase?.pid" class="print-state">Case not found.</div>

    <div v-else class="print-paper">
      <PrintFormHeader :patient-case="patientCase" form-title="Case Information Form" form-subtitle="Personal & Case Information" :document-date="patientCase.admission_datetime" />

      <div class="ci-section">
        <span class="ci-heading">Personal Information</span>
        <div class="ci-grid ci-grid--4">
          <div class="ci-field ci-field--grow">
            <span class="ci-label">Full Name</span>
            <span class="ci-value uppercase">{{ fullName }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Medical Record No.</span>
            <span class="ci-value">{{ patient?.medical_record_number || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Birthdate</span>
            <span class="ci-value">{{ formatDate(patient?.birthdate) }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Gender</span>
            <span class="ci-value">{{ patient?.gender || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Civil Status</span>
            <span class="ci-value">{{ patient?.civil_status || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Contact Number</span>
            <span class="ci-value">{{ patient?.contact_number || "—" }}</span>
          </div>
          <div class="ci-field ci-field--grow">
            <span class="ci-label">Email Address</span>
            <span class="ci-value">{{ patient?.email_address || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Religion</span>
            <span class="ci-value">{{ patient?.religion || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Occupation</span>
            <span class="ci-value">{{ patient?.occupation || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Spouse Name</span>
            <span class="ci-value">{{ patient?.spouse_name || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Birthplace</span>
            <span class="ci-value">{{ patient?.birthplace || "—" }}</span>
          </div>
          <div class="ci-field ci-field--grow ci-field--full">
            <span class="ci-label">Address</span>
            <span class="ci-value">{{ address }}</span>
          </div>
        </div>
      </div>

      <div class="ci-section">
        <span class="ci-heading">Case Information</span>
        <div class="ci-grid ci-grid--3">
          <div class="ci-field">
            <span class="ci-label">Case Number</span>
            <span class="ci-value">{{ patientCase.case_number || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Type</span>
            <span class="ci-value ci-value--caps">{{ patientCase.type || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Patient Type</span>
            <span class="ci-value">{{ patientCase.patient_type?.name || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Admission/Arrival Date/Time</span>
            <span class="ci-value">{{ formatDateTime(patientCase.admission_datetime) }}</span>
          </div>
          <template v-if="patientCase.type === 'inpatient'">
            <div class="ci-field">
              <span class="ci-label">Station</span>
              <span class="ci-value">{{ patientCase.station?.name || "—" }}</span>
            </div>
            <div class="ci-field">
              <span class="ci-label">Bed</span>
              <span class="ci-value">{{ patientCase.bed?.bed_number || "—" }}</span>
            </div>
          </template>
          <div class="ci-field ci-field--full">
            <span class="ci-label">Chief Complaint</span>
            <span class="ci-value">{{ patientCase.chief_complaint || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Initial Diagnosis</span>
            <span class="ci-value">{{ patientCase.initial_diagnosis || "—" }}</span>
          </div>
          <div class="ci-field">
            <span class="ci-label">Final Diagnosis</span>
            <span class="ci-value">{{ patientCase.final_diagnosis || "—" }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FiPrinter, FiArrowLeft } from "vue-icons-plus/fi";
import PrintFormHeader from "@/components/print/PrintFormHeader.vue";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useAppToast } from "@/composables/toast";

const route = useRoute();
const router = useRouter();
const toast = useAppToast();
const patientCaseStore = usePatientCaseStore();

const loading = ref(true);
const patientCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => patientCase.value?.patient);

const fullName = computed(() => {
  const p = patient.value;
  if (!p) return "—";
  return `${p.firstname ?? ""} ${p.middlename ? p.middlename + " " : ""}${p.lastname ?? ""}${p.suffix ? " " + p.suffix : ""}`.replace(/\s+/g, " ").trim() || "—";
});

const address = computed(() => {
  const p = patient.value;
  if (!p) return "—";
  return [p.barangay, p.municipality, p.province, p.region].filter(Boolean).join(", ") || "—";
});

const formatDate = (value?: string) => (value ? new Date(value).toLocaleDateString() : "—");
const formatDateTime = (value?: string) => (value ? new Date(value).toLocaleString() : "—");

onMounted(async () => {
  const pid = route.params.pid as string;
  try {
    await patientCaseStore.view(pid);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve case information");
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
.ci-section {
  margin-top: 20px;
}
.ci-heading {
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
.ci-grid {
  display: grid;
  gap: 14px 18px;
}
.ci-grid--4 {
  grid-template-columns: repeat(4, minmax(0, 1fr));
}
.ci-grid--3 {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.ci-field {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.ci-field--grow {
  grid-column: span 2;
}
.ci-field--full {
  grid-column: 1 / -1;
}
.ci-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.ci-value {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 4px;
  min-height: 19px;
  word-break: break-word;
}
.ci-value--caps {
  text-transform: capitalize;
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
