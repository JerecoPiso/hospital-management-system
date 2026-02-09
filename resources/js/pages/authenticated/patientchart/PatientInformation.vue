<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="mx-auto space-y-6">
      
      <!-- Header with Actions -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Patient Information</h1>
        <div class="space-x-3 flex">
          <button @click="editProfile" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 transition">
            Edit Profile
          </button>
          <button @click="printSummary" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium transition">
            Print Summary
          </button>
        </div>
      </div>

      <!-- Patient Header Card -->
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-linear-to-r from-emerald-500 to-teal-600 h-24 "></div>
        <div class="px-6 pb-6">
          <div class="flex flex-col sm:flex-row gap-6 -mt-16 relative z-10">
            <!-- Patient Avatar -->
            <div class="shrink-0">
              <div class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-linear-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                <span class="text-5xl font-bold text-white">{{ patient.initials }}</span>
              </div>
            </div>
            
            <!-- Patient Basic Info -->
            <div class="flex-1 pt-2">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                  <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider">Full Name</p>
                  <p class="text-lg font-bold text-white mt-1">{{ patient.firstName }} {{ patient.middleInitial }}. {{ patient.lastName }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider">Patient ID</p>
                  <p class="text-lg font-mono text-white mt-1">{{ patient.id }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider">Age</p>
                  <p class="text-lg font-bold text-white mt-1">{{ age }} years</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider">Status</p>
                  <p class="text-lg font-bold text-white mt-1 flex items-center gap-2">
                    <span class="w-2 h-2 bg-white rounded-full"></span>
                    {{ patient.status }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vital Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
          <p class="text-xs font-semibold text-gray-500 uppercase">Blood Pressure</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ vitals.bloodPressure }}</p>
          <p class="text-xs text-green-600 mt-1">Normal</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
          <p class="text-xs font-semibold text-gray-500 uppercase">Heart Rate</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ vitals.heartRate }}</p>
          <p class="text-xs text-green-600 mt-1">bpm</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
          <p class="text-xs font-semibold text-gray-500 uppercase">Temperature</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ vitals.temperature }}</p>
          <p class="text-xs text-green-600 mt-1">Normal</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
          <p class="text-xs font-semibold text-gray-500 uppercase">Blood Type</p>
          <p class="text-2xl font-bold text-red-600 mt-2">{{ patient.bloodType }}</p>
          <p class="text-xs text-gray-600 mt-1">Critical</p>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
          <p class="text-xs font-semibold text-gray-500 uppercase">Weight</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ vitals.weight }} kg</p>
          <p class="text-xs text-gray-600 mt-1">BMI: {{ vitals.bmi }}</p>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column -->
        <div class="space-y-6">
          <!-- Demographics -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
              <h3 class="font-bold text-gray-900">Demographics</h3>
            </div>
            <div class="p-5 space-y-4">
              <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Date of Birth</span>
                <span class="text-sm font-medium text-gray-900">{{ formatDate(patient.dateOfBirth) }}</span>
              </div>
              <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Gender</span>
                <span class="text-sm font-medium text-gray-900">{{ patient.gender }}</span>
              </div>
              <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Contact</span>
                <span class="text-sm font-medium text-gray-900">{{ patient.contact }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Email</span>
                <span class="text-sm font-medium text-gray-900">{{ patient.email }}</span>
              </div>
            </div>
          </div>

          <!-- Emergency Contact -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
              <h3 class="font-bold text-gray-900">Emergency Contact</h3>
            </div>
            <div class="p-5 space-y-4">
              <div v-for="contact in emergencyContacts.filter(c => c.isPrimary)" :key="contact.id">
                <p class="text-xs font-semibold text-gray-500 uppercase">Primary Contact</p>
                <p class="text-sm font-bold text-gray-900 mt-1">{{ contact.name }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ contact.relationship }}</p>
                <p class="text-sm font-mono text-blue-600 mt-2">{{ contact.phone }}</p>
              </div>
              <div class="pt-3 border-t border-gray-100">
                <div v-for="contact in emergencyContacts.filter(c => !c.isPrimary)" :key="contact.id">
                  <p class="text-xs font-semibold text-gray-500 uppercase">Secondary Contact</p>
                  <p class="text-sm font-bold text-gray-900 mt-1">{{ contact.name }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ contact.relationship }}</p>
                  <p class="text-sm font-mono text-blue-600 mt-2">{{ contact.phone }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Middle Column -->
        <div class="space-y-6">
          <!-- Clinical Alerts -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-red-50 px-5 py-3 border-b border-red-200">
              <h3 class="font-bold text-red-900">⚠️ Clinical Alerts</h3>
            </div>
            <div class="p-5 space-y-3">
              <div v-for="alert in alerts" :key="alert.id" :class="`p-3 border-l-4 rounded ${
                alert.severity === 'severe' ? 'bg-red-50 border-red-500' : 
                alert.severity === 'warning' ? 'bg-amber-50 border-amber-500' :
                'bg-orange-50 border-orange-500'
              }`">
                <p :class="`text-xs font-bold uppercase ${
                  alert.severity === 'severe' ? 'text-red-700' : 
                  alert.severity === 'warning' ? 'text-amber-700' :
                  'text-orange-700'
                }`">{{ alert.title }}</p>
                <p :class="`text-sm font-medium mt-1 ${
                  alert.severity === 'severe' ? 'text-red-900' : 
                  alert.severity === 'warning' ? 'text-amber-900' :
                  'text-orange-900'
                }`">{{ alert.message }}</p>
                <p :class="`text-xs mt-1 ${
                  alert.severity === 'severe' ? 'text-red-700' : 
                  alert.severity === 'warning' ? 'text-amber-700' :
                  'text-orange-700'
                }`">{{ alert.description }}</p>
              </div>
            </div>
          </div>

          <!-- Primary Physician -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
              <h3 class="font-bold text-gray-900">Primary Physician</h3>
            </div>
            <div class="p-5">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-linear-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold">
                  {{ primaryPhysician.initials }}
                </div>
                <div class="flex-1">
                  <p class="font-bold text-gray-900">{{ primaryPhysician.name }}, {{ primaryPhysician.title }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ primaryPhysician.specialties.join(' & ') }}</p>
                  <p class="text-sm text-blue-600 mt-2">📞 Ext. {{ primaryPhysician.extension }}</p>
                  <p class="text-sm text-blue-600">📧 {{ primaryPhysician.email }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          <!-- Current Medications -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
              <h3 class="font-bold text-gray-900">Current Medications</h3>
            </div>
            <div class="p-5 space-y-3">
              <div v-for="med in medications" :key="med.id" :class="{
                'pb-3 border-b border-gray-100': med.id !== medications.length
              }">
                <p class="text-sm font-medium text-gray-900">{{ med.name }} {{ med.dose }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ med.frequency }} • {{ med.indication }}</p>
              </div>
            </div>
          </div>

          <!-- Last Visit -->
          <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
              <h3 class="font-bold text-gray-900">Last Visit</h3>
            </div>
            <div class="p-5 space-y-3">
              <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Date</span>
                <span class="text-sm font-medium text-gray-900">{{ formatDate(lastVisit.date) }}</span>
              </div>
              <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                <span class="text-sm text-gray-600">Department</span>
                <span class="text-sm font-medium text-gray-900">{{ lastVisit.department }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Status</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ lastVisit.status }}
                </span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Medical History Section -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <h3 class="font-bold text-gray-900 text-lg">Medical History</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Chronic Conditions -->
            <div>
              <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-blue-600 rounded"></span>
                Chronic Conditions
              </h4>
              <ul class="space-y-2">
                <li v-for="(condition, index) in medicalHistory.chronicConditions" :key="index" class="flex items-start gap-3">
                  <span class="text-blue-600 font-bold mt-0.5">•</span>
                  <span class="text-sm text-gray-700">{{ condition }}</span>
                </li>
              </ul>
            </div>

            <!-- Surgical History -->
            <div>
              <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-1 h-6 bg-green-600 rounded"></span>
                Surgical History
              </h4>
              <div class="space-y-3">
                <div v-for="(surgery, index) in medicalHistory.surgicalHistory" :key="index" :class="{
                  'pb-3 border-b border-gray-100': index !== medicalHistory.surgicalHistory.length - 1
                }">
                  <p class="text-sm font-medium text-gray-900">{{ surgery.procedure }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ formatDate(surgery.date) }} • {{ surgery.specialty }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Patient data
const patient = ref({
  id: 'PT-2024-0089',
  firstName: 'Jonathan',
  lastName: 'Smith',
  middleInitial: 'R',
  dateOfBirth: '1985-05-12',
  gender: 'Male',
  bloodType: 'O Negative',
  contact: '+1 (555) 123-4567',
  email: 'jonathan.smith@email.com',
  status: 'Active',
  initials: 'JS'
});

const vitals = ref({
  bloodPressure: '120/80',
  heartRate: 72,
  temperature: '98.6°F',
  weight: 82,
  bmi: 26.8
});

const allergies = ref([
  {
    id: 1,
    name: 'Penicillin',
    severity: 'severe',
    reaction: 'Can cause Anaphylaxis',
    type: 'Severe Allergy'
  }
]);

const alerts = ref([
  {
    id: 1,
    type: 'allergy',
    severity: 'severe',
    title: 'Severe Allergy',
    message: 'Penicillin',
    description: 'Can cause Anaphylaxis'
  },
  {
    id: 2,
    type: 'fall_risk',
    severity: 'warning',
    title: 'Fall Risk',
    message: 'High Risk',
    description: 'Requires assistance when mobilizing'
  },
  {
    id: 3,
    type: 'drug_interaction',
    severity: 'warning',
    title: 'Drug Interaction',
    message: 'Warfarin + NSAIDs',
    description: 'Monitor INR closely'
  }
]);

const medications = ref([
  {
    id: 1,
    name: 'Metformin',
    dose: '1000mg',
    frequency: 'Twice daily',
    indication: 'Diabetes'
  },
  {
    id: 2,
    name: 'Lisinopril',
    dose: '10mg',
    frequency: 'Once daily',
    indication: 'Hypertension'
  },
  {
    id: 3,
    name: 'Albuterol Inhaler',
    dose: 'Variable',
    frequency: 'As needed',
    indication: 'Asthma'
  },
  {
    id: 4,
    name: 'Vitamin D3',
    dose: '2000 IU',
    frequency: 'Once daily',
    indication: 'Supplement'
  }
]);

const medicalHistory = ref({
  chronicConditions: [
    'Type 2 Diabetes Mellitus',
    'Hypertension (Stage 1)',
    'Asthma (Exercise Induced)',
    'Hyperlipidemia'
  ],
  surgicalHistory: [
    {
      procedure: 'Appendectomy',
      date: '2018-03-10',
      specialty: 'General Surgery'
    },
    {
      procedure: 'ACL Repair (Left Knee)',
      date: '2012-06-15',
      specialty: 'Orthopedic Surgery'
    },
    {
      procedure: 'Wisdom Teeth Extraction',
      date: '2008-09-20',
      specialty: 'Oral Surgery'
    }
  ]
});

const emergencyContacts = ref([
  {
    id: 1,
    name: 'Martha Smith',
    relationship: 'Spouse',
    phone: '+1 (555) 012-3456',
    isPrimary: true
  },
  {
    id: 2,
    name: 'Robert Smith Jr.',
    relationship: 'Son',
    phone: '+1 (555) 098-7654',
    isPrimary: false
  }
]);

const primaryPhysician = ref({
  id: 1,
  name: 'Dr. Sarah Jenkins',
  title: 'MD',
  specialties: ['Cardiology', 'Internal Medicine'],
  extension: '2456',
  email: 's.jenkins@hospital.com',
  initials: 'SJ'
});

const lastVisit = ref({
  date: '2025-01-15',
  department: 'Cardiology',
  status: 'Completed',
  notes: 'Regular checkup completed successfully'
});

// Computed property for age
const age = computed(() => {
  const today = new Date();
  const birthDate = new Date(patient.value.dateOfBirth);
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  
  return age;
});

// Methods
const editProfile = () => {
  console.log('Edit profile clicked');
};

const printSummary = () => {
  window.print();
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const getSeverityColor = (severity) => {
  const colors = {
    severe: 'red',
    warning: 'amber',
    info: 'teal'
  };
  return colors[severity] || 'gray';
};
</script>

<style scoped>
.gradient-header {
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}

@media print {
  .hidden-print {
    display: none;
  }
}
</style>