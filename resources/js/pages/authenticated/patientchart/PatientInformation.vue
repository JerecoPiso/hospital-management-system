<template>
  <div class="space-y-6 mb-12">

    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Patient Information</h1>
        <p class="text-sm text-slate-500 mt-0.5">Patient profile and current case record</p>
      </div>
      <div class="flex gap-3">
        <button @click="editProfile" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700 transition-colors shadow-sm flex items-center gap-2">
          <FiEdit2 size="15" />
          Edit Profile
        </button>
        <button @click="printSummary" class="px-4 py-2.5 bg-linear-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:brightness-105 text-sm font-medium transition-all shadow-sm flex items-center gap-2">
          <FiPrinter size="15" />
          Print Summary
        </button>
      </div>
    </div>

    <!-- Patient Header Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="bg-linear-to-r from-emerald-500 to-teal-600 h-24"></div>
      <div class="px-6 pb-6">
        <div class="flex flex-col sm:flex-row gap-6 -mt-16 relative z-10">
          <!-- Patient Avatar -->
          <div class="shrink-0">
            <div class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-linear-to-br from-teal-400 to-teal-600 flex items-center justify-center">
              <span class="text-5xl font-bold text-white">{{ initials }}</span>
            </div>
          </div>

          <!-- Patient Basic Info -->
          <div class="flex-1 pt-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <p class="text-xs font-semibold text-emerald-50 uppercase tracking-wider">Full Name</p>
                <p class="text-lg font-bold text-white mt-1">{{ fullName }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-emerald-50 uppercase tracking-wider">Medical Record No.</p>
                <p class="text-lg font-mono text-white mt-1">{{ patient.medical_record_number }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-emerald-50 uppercase tracking-wider">Age / Gender</p>
                <p class="text-lg font-bold text-white mt-1">{{ age }} yrs &bull; {{ patient.gender }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-emerald-50 uppercase tracking-wider">Patient Type</p>
                <span class="inline-flex items-center gap-1.5 mt-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">
                  <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                  {{ patientCase.patient_type }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vital Signs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div v-for="stat in vitalStats" :key="stat.label" class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-start gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" :style="{ backgroundColor: stat.bgColor }">
          <component :is="stat.icon" :style="{ color: stat.color }" size="18" />
        </div>
        <div class="min-w-0">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ stat.label }}</p>
          <p class="text-xl font-bold text-slate-900 mt-1">{{ stat.value }}</p>
        </div>
      </div>
    </div>
    <p class="text-xs text-slate-400 -mt-3">Last measured {{ formatDateTime(vitals.measured_at) }}</p>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Left Column -->
      <div class="space-y-6">
        <!-- Personal & Contact Information -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
              <FiUser class="text-white" size="14" />
            </div>
            <h3 class="text-sm font-bold text-slate-800">Personal &amp; Contact Information</h3>
          </header>
          <div class="p-5 space-y-3.5">
            <div v-for="row in personalInfo" :key="row.label" class="flex justify-between items-start gap-4 pb-3.5 border-b border-slate-100 last:border-0 last:pb-0">
              <span class="text-sm text-slate-500 shrink-0">{{ row.label }}</span>
              <span class="text-sm font-medium text-slate-800 text-right">{{ row.value }}</span>
            </div>
          </div>
        </section>
      </div>

      <!-- Middle Column -->
      <div class="space-y-6">
        <!-- Admission Details & Diagnosis -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
              <FiCalendar class="text-white" size="14" />
            </div>
            <h3 class="text-sm font-bold text-slate-800">Admission Details &amp; Diagnosis</h3>
          </header>
          <div class="p-5 space-y-3.5">
            <div class="flex justify-between items-start pb-3.5 border-b border-slate-100">
              <span class="text-sm text-slate-500">Case Number</span>
              <span class="text-sm font-mono font-medium text-slate-800">{{ patientCase.case_number }}</span>
            </div>
            <div class="flex justify-between items-start pb-3.5 border-b border-slate-100">
              <span class="text-sm text-slate-500">Admission Date</span>
              <span class="text-sm font-medium text-slate-800">{{ formatDateTime(patientCase.admission_datetime) }}</span>
            </div>
            <div class="pb-3.5 border-b border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Chief Complaint</span>
              <p class="text-sm font-medium text-slate-800 mt-1">{{ patientCase.chief_complaint }}</p>
            </div>
            <div class="pb-3.5 border-b border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Initial Diagnosis</span>
              <p class="text-sm font-medium text-slate-800 mt-1">{{ patientCase.initial_diagnosis }}</p>
            </div>
            <div>
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Final Diagnosis</span>
              <p class="text-sm font-medium text-slate-800 mt-1">{{ patientCase.final_diagnosis }}</p>
            </div>
          </div>
        </section>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        <!-- Family & Background -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
              <FiUsers class="text-white" size="14" />
            </div>
            <h3 class="text-sm font-bold text-slate-800">Family &amp; Background</h3>
          </header>
          <div class="p-5 space-y-3.5">
            <div v-for="row in familyInfo" :key="row.label" class="flex justify-between items-start gap-4 pb-3.5 border-b border-slate-100 last:border-0 last:pb-0">
              <span class="text-sm text-slate-500 shrink-0">{{ row.label }}</span>
              <span class="text-sm font-medium text-slate-800 text-right">{{ row.value }}</span>
            </div>
          </div>
        </section>

        <!-- Ward Assignment -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
              <FiMapPin class="text-white" size="14" />
            </div>
            <h3 class="text-sm font-bold text-slate-800">Ward Assignment</h3>
          </header>
          <div class="p-5 space-y-3.5">
            <div class="flex justify-between items-start pb-3.5 border-b border-slate-100">
              <span class="text-sm text-slate-500">Ward</span>
              <span class="text-sm font-medium text-slate-800">{{ assignment.ward }}</span>
            </div>
            <div class="flex justify-between items-start pb-3.5 border-b border-slate-100">
              <span class="text-sm text-slate-500">Station</span>
              <span class="text-sm font-medium text-slate-800">{{ assignment.station }}</span>
            </div>
            <div class="flex justify-between items-start pb-3.5 border-b border-slate-100">
              <span class="text-sm text-slate-500">Room</span>
              <span class="text-sm font-medium text-slate-800">{{ assignment.room }}</span>
            </div>
            <div class="flex justify-between items-start">
              <span class="text-sm text-slate-500">Bed</span>
              <span class="text-sm font-medium text-slate-800">{{ assignment.bed }}</span>
            </div>
          </div>
        </section>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { FiUser, FiUsers, FiCalendar, FiEdit2, FiPrinter, FiMapPin, FiHeart, FiActivity, FiThermometer, FiDroplet } from 'vue-icons-plus/fi';
import { FaWeight } from 'vue-icons-plus/fa';

// Static placeholder data shaped exactly like the `patients` table columns.
const patient = ref({
  medical_record_number: 'MRN-2GX8QK3F',
  firstname: 'Jonathan',
  lastname: 'Smith',
  middlename: 'Reyes',
  suffix: null,
  birthdate: '1985-05-12',
  gender: 'Male',
  civil_status: 'Married',
  contact_number: '+63 917 123 4567',
  email_address: 'jonathan.smith@email.com',
  religion: 'Roman Catholic',
  birthplace: 'Tacloban City, Leyte',
  occupation: 'Civil Engineer',
  spouse_name: 'Martha Smith'
});

// Static placeholder data shaped like the `patient_cases` table columns.
const patientCase = ref({
  case_number: 'CASE-8X8L1LKJ',
  patient_type: 'In-Patient',
  admission_datetime: '2026-08-04 07:37:00',
  chief_complaint: 'Persistent chest pain and shortness of breath',
  initial_diagnosis: 'Suspected Acute Coronary Syndrome',
  final_diagnosis: 'Stable Angina Pectoris'
});

// Static placeholder data shaped like the `vital_signs` table columns.
const vitals = ref({
  temperature: 36.8,
  heart_rate: 78,
  respiratory_rate: 18,
  systolic: 120,
  diastolic: 80,
  oxygen_saturation: 98,
  weight: 82,
  height: 172,
  bmi: 27.7,
  measured_at: '2026-08-07 06:15:00'
});

// Static placeholder data shaped like the bed/room/ward/station hierarchy.
const assignment = ref({
  ward: 'Medical Ward',
  station: 'Station A',
  room: '305A',
  bed: 'Bed 2'
});

const fullName = computed(() => {
  const middle = patient.value.middlename ? `${patient.value.middlename.charAt(0)}.` : '';
  const suffix = patient.value.suffix ? ` ${patient.value.suffix}` : '';
  return [patient.value.firstname, middle, `${patient.value.lastname}${suffix}`].filter(Boolean).join(' ');
});

const initials = computed(() => `${patient.value.firstname.charAt(0)}${patient.value.lastname.charAt(0)}`.toUpperCase());

const age = computed(() => {
  const today = new Date();
  const birthDate = new Date(patient.value.birthdate);
  let years = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();

  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    years--;
  }

  return years;
});

const personalInfo = computed(() => [
  { label: 'Date of Birth', value: formatDate(patient.value.birthdate) },
  { label: 'Gender', value: patient.value.gender },
  { label: 'Civil Status', value: patient.value.civil_status },
  { label: 'Religion', value: patient.value.religion },
  { label: 'Birthplace', value: patient.value.birthplace },
  { label: 'Contact Number', value: patient.value.contact_number },
  { label: 'Email Address', value: patient.value.email_address }
]);

const familyInfo = computed(() => [
  { label: 'Occupation', value: patient.value.occupation },
  { label: 'Spouse Name', value: patient.value.spouse_name || '—' }
]);

const vitalStats = computed(() => [
  {
    label: 'Blood Pressure',
    value: `${vitals.value.systolic}/${vitals.value.diastolic}`,
    icon: FiHeart,
    bgColor: '#ecfdf5',
    color: '#059669'
  },
  {
    label: 'Heart Rate',
    value: `${vitals.value.heart_rate} bpm`,
    icon: FiActivity,
    bgColor: '#eff6ff',
    color: '#3b82f6'
  },
  {
    label: 'Temperature',
    value: `${vitals.value.temperature}°C`,
    icon: FiThermometer,
    bgColor: '#fdf4ff',
    color: '#a855f7'
  },
  {
    label: 'O2 Saturation',
    value: `${vitals.value.oxygen_saturation}%`,
    icon: FiDroplet,
    bgColor: '#fef2f2',
    color: '#dc2626'
  },
  {
    label: 'Weight / BMI',
    value: `${vitals.value.weight}kg (${vitals.value.bmi})`,
    icon: FaWeight,
    bgColor: '#fffbeb',
    color: '#d97706'
  }
]);

// Methods
const editProfile = () => {
  console.log('Edit profile clicked');
};

const printSummary = () => {
  window.print();
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatDateTime = (dateString) => {
  if (!dateString) return '—';
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleString(undefined, options);
};
</script>
