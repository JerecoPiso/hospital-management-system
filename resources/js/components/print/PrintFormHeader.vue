<template>
  <div class="pf-header">
    <!-- Hospital Letterhead -->
    <div class="pf-letterhead">
      <div class="flex w-full justify-center align-middle">
        <img :src="`${appURL}storage/images/patterson.png`" alt="Logo" class="print:h-26 h-26 print:w-auto w-auto rounded-md" />
      </div>
      <!-- <p class="pf-clinic-name">{{ hospitalName }}</p> -->
      <p class="pf-clinic-line">{{ hospitalAddress }}</p>
      <p class="pf-clinic-line">{{ hospitalContact }}</p>
    </div>

    <!-- Form Title -->
    <div class="pf-title-bar">
      <div>
        <h2 class="pf-title">{{ formTitle }}</h2>
        <p v-if="formSubtitle" class="pf-subtitle">{{ formSubtitle }}</p>
      </div>
      <div class="pf-doc-date">
        <span class="pf-label">Date</span>
        <span class="pf-value">{{ formattedDate }}</span>
      </div>
    </div>

    <!-- Patient Info Bar -->
    <div class="pf-patient">
      <div class="pf-field pf-field--grow">
        <span class="pf-label">Patient Name</span>
        <span class="pf-value uppercase">{{ patientName }}</span>
      </div>
      <div class="pf-field">
        <span class="pf-label">Age / Sex</span>
        <span class="pf-value">{{ ageSex }}</span>
      </div>
        <div class="pf-field">
        <span class="pf-label">Admission/Visit Date</span>
        <span class="pf-value">{{ admissionDate }}</span>
      </div>
      <!-- <div class="pf-field">
        <span class="pf-label">Case No.</span>
        <span class="pf-value">{{ caseNumber }}</span>
      </div> -->
    </div>
    <div class="pf-patient pf-patient--sub">
      <!-- <div class="pf-field">
        <span class="pf-label">Room / Bed</span>
        <span class="pf-value">{{ roomBed }}</span>
      </div> -->
      <!-- <div class="pf-field">
        <span class="pf-label">Admission Date</span>
        <span class="pf-value">{{ admissionDate }}</span>
      </div> -->
      <!-- <div class="pf-field">
        <span class="pf-label">Patient Type</span>
        <span class="pf-value pf-value--caps">{{ patientCase?.type || "—" }}</span>
      </div> -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { PatientCase } from "@/interface/Interfaces";
const appURL = import.meta.env.VITE_APP_URL;

const props = withDefaults(
  defineProps<{
    patientCase?: PatientCase | null;
    formTitle: string;
    formSubtitle?: string;
    documentDate?: string | Date | null;
    hospitalName?: string;
    hospitalAddress?: string;
    hospitalContact?: string;
  }>(),
  {
    patientCase: null,
    formSubtitle: "",
    documentDate: null,
    hospitalName: import.meta.env.VITE_FACILITY_NAME,
    hospitalAddress: import.meta.env.VITE_ADDRESS,
    hospitalContact: import.meta.env.VITE_CONTACT,
  }
);

const patient = computed(() => props.patientCase?.patient);

const patientName = computed(() => {
  const p = patient.value;
  if (!p) return "—";
  return `${p.firstname ?? ""} ${p.middlename ? p.middlename + " " : ""}${p.lastname ?? ""}`.replace(/\s+/g, " ").trim() || "—";
});

const ageSex = computed(() => {
  const p = patient.value;
  const parts: string[] = [];
  if (p?.birthdate) {
    const dob = new Date(p.birthdate);
    if (!isNaN(dob.getTime())) {
      const now = new Date();
      let age = now.getFullYear() - dob.getFullYear();
      const m = now.getMonth() - dob.getMonth();
      if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
      parts.push(`${age}y`);
    }
  }
  if (p?.gender) parts.push(p.gender);
  return parts.join(" / ") || "—";
});

const caseNumber = computed(() => props.patientCase?.case_number || "—");

const admissionDate = computed(() => {
  const value = props.patientCase?.admission_datetime;
  return value ? new Date(value).toLocaleString() : "—";
});

const roomBed = computed(() => {
  const bed = props.patientCase?.bed;
  if (bed) {
    const room = bed.room?.room_number ? `Room ${bed.room.room_number}` : null;
    const bedNo = bed.bed_number ? `Bed ${bed.bed_number}` : null;
    return [room, bedNo].filter(Boolean).join(" / ") || "—";
  }
  return props.patientCase?.station?.name || "—";
});

const formattedDate = computed(() => {
  const value = props.documentDate ? new Date(props.documentDate) : new Date();
  return isNaN(value.getTime()) ? "—" : value.toLocaleString();
});
</script>

<style scoped>
.pf-header {
  font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
}
.pf-letterhead {
  text-align: center;
  border-bottom: 2px solid #0f766e;
  padding-bottom: 12px;
}
.pf-clinic-name {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 0.5px;
}
.pf-clinic-line {
  margin: 2px 0 0;
  font-size: 12px;
  color: #64748b;
}
.pf-title-bar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-top: 16px;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 10px;
}
.pf-title {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #0f172a;
}
.pf-subtitle {
  margin: 2px 0 0;
  font-size: 12px;
  color: #64748b;
}
.pf-doc-date {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  min-width: 140px;
}
.pf-patient {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  margin-top: 14px;
}
.pf-patient--sub {
  margin-top: 8px;
}
.pf-field {
  display: flex;
  flex-direction: column;
  min-width: 120px;
}
.pf-field--grow {
  flex: 1 1 200px;
}
.pf-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #94a3b8;
}
.pf-value {
  font-size: 14px;
  font-weight: 600;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 3px;
  min-height: 20px;
}
.pf-value--caps {
  text-transform: capitalize;
}
</style>
