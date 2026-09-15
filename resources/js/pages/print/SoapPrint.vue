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

    <div v-if="loading" class="print-state">Loading SOAP note...</div>
    <div v-else-if="!soap?.pid" class="print-state">SOAP note not found.</div>

    <div v-else class="print-paper">
      <PrintFormHeader :patient-case="soap.patientCase ?? soap.patient_case" form-title="SOAP Note" form-subtitle="Subjective, Objective, Assessment & Plan" :document-date="soap.created_at" />

      <!-- <div class="sp-section">
        <span class="sp-label">ICD Diagnosis</span>
        <p class="sp-value">{{ soap.icd ? `${soap.icd.code} — ${soap.icd.name}` : "—" }}</p>
      </div> -->

      <div class="sp-section">
        <span class="sp-label">Subjective</span>
        <p class="sp-value sp-value--block">{{ soap.subjective || "—" }}</p>
      </div>

      <div class="sp-section">
        <span class="sp-label">Objective</span>
        <p class="sp-value sp-value--block">{{ soap.objective || "—" }}</p>
      </div>

      <div class="sp-section">
        <span class="sp-label">Assessment</span>
        <p class="sp-value sp-value--block">{{ soap.assessment || "—" }}</p>
      </div>

      <div class="sp-section">
        <span class="sp-label">Plan</span>
        <p class="sp-value sp-value--block">{{ soap.plan || "—" }}</p>
      </div>

      <div v-if="soap.remarks" class="sp-section">
        <span class="sp-label">Remarks</span>
        <p class="sp-value sp-value--block">{{ soap.remarks }}</p>
      </div>

      <div class="sp-sign">
        <p class="sp-sign-name uppercase">{{ doctorName }}</p>
        <div class="sp-sign-line"></div>
        <p class="sp-sign-lic">Lic. No. {{ doctorLicense || "__________" }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { FiPrinter, FiArrowLeft } from "vue-icons-plus/fi";
import PrintFormHeader from "@/components/print/PrintFormHeader.vue";
import { useSoapStore } from "@/store/patientchart/Soap";
import { useAppToast } from "@/composables/toast";

const route = useRoute();
const router = useRouter();
const toast = useAppToast();
const soapStore = useSoapStore();

const loading = ref(true);
const soap = computed(() => soapStore.soap);

const doctor = computed(() => soap.value.doctor);
const doctorName = computed(() => {
  const d = doctor.value;
  if (!d) return "Attending Physician";
  const name = `${d.firstname ?? ""} ${d.middlename ? d.middlename + " " : ""}${d.lastname ?? ""}`.replace(/\s+/g, " ").trim();
  return name ? `${name}${d.suffix ? ", " + d.suffix : ""}, M.D.` : "Attending Physician";
});
const doctorLicense = computed(() => doctor.value?.license_no || "");

onMounted(async () => {
  const pid = route.params.pid as string;
  try {
    await soapStore.view(pid);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve SOAP note");
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
  max-width: 720px;
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
  max-width: 720px;
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  padding: 28px 32px;
  color: #1e293b;
}
.sp-section {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-top: 16px;
}
.sp-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.sp-value {
  font-size: 13px;
  color: #334155;
  margin: 0;
}
.sp-value--block {
  white-space: pre-wrap;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 8px;
  min-height: 20px;
}
.sp-sign {
  text-align: center;
  min-width: 220px;
  margin: 40px 0 0 auto;
}
.sp-sign-line {
  border-top: 1px solid #1e293b;
  margin-bottom: 4px;
}
.sp-sign-name {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
}
.sp-sign-lic {
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
