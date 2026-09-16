<template>
  <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
    <FiUser size="40" class="mb-3 opacity-30" />
    <p class="text-sm font-medium">No patient selected</p>
    <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
  </div>

  <div v-else-if="!patient" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
    <p class="text-sm font-medium">Loading patient information...</p>
  </div>

  <div v-else class="space-y-6 mb-12">
    <!-- Edit Case Information Modal -->
    <Dialog v-model:visible="caseModal" modal :style="{ width: '42vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiEdit2 class="text-white" size="15" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Edit Case Information</h2>
            <p class="text-xs text-slate-400 mt-0.5">Update admission and diagnosis details for this case</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="updateCase" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Arrival Date &amp; Time <span class="text-red-400">*</span></label>
          <DatePicker v-model="caseInfo.admission_datetime" showTime hourFormat="24" fluid required />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Chief Complaint <span class="text-red-400">*</span></label>
          <Textarea v-model="caseInfo.chief_complaint" rows="2" autoResize fluid required class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Initial Diagnosis</label>
          <Textarea v-model="caseInfo.initial_diagnosis" rows="2" autoResize fluid class="text-sm" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Final Diagnosis</label>
          <Textarea v-model="caseInfo.final_diagnosis" rows="2" autoResize fluid class="text-sm" />
        </div>

        <div class="flex flex-col gap-3 pt-2 border-t border-slate-100">
          <span class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Visual Acuity</span>
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">OD</label>
              <InputText v-model="caseInfo.od" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">PH</label>
              <InputText v-model="caseInfo.ph_right" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">OS</label>
              <InputText v-model="caseInfo.os" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">PH</label>
              <InputText v-model="caseInfo.ph_left" fluid class="text-sm" />
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-3 pt-2 border-t border-slate-100">
          <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-slate-700 uppercase tracking-wide">CC</span>
            <InputText v-model="caseInfo.cc" fluid class="text-sm flex-1" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">OD</label>
              <InputText v-model="caseInfo.cc_od" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">PH</label>
              <InputText v-model="caseInfo.cc_ph_right" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">OS</label>
              <InputText v-model="caseInfo.cc_os" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">PH</label>
              <InputText v-model="caseInfo.cc_ph_left" fluid class="text-sm" />
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-1.5 pt-2 border-t border-slate-100">
          <label class="text-sm font-medium text-slate-700">IOP</label>
          <InputText v-model="caseInfo.iop" fluid class="text-sm" />
        </div>

        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="caseModal = false" />
          <Button type="submit" label="Save Changes" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Patient Information</h1>
        <p class="text-sm text-slate-500 mt-0.5">Patient profile and current case record</p>
      </div>
      <!-- <div class="flex gap-3">
        <button @click="editProfile" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium text-slate-700 transition-colors shadow-sm flex items-center gap-2">
          <FiEdit2 size="15" />
          Edit Profile
        </button>
        <button @click="printSummary" class="px-4 py-2.5 bg-linear-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:brightness-105 text-sm font-medium transition-all shadow-sm flex items-center gap-2">
          <FiPrinter size="15" />
          Print Summary
        </button>
      </div> -->
    </div>

    <!-- Patient Header Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="bg-linear-to-r from-emerald-500 to-teal-600 px-6 py-6">
        <div class="flex flex-col sm:flex-row gap-6 sm:items-center">
          <!-- Patient Avatar -->
          <div class="shrink-0">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl border-4 border-white shadow-lg bg-linear-to-br from-teal-400 to-teal-600 flex items-center justify-center">
              <span class="text-4xl font-bold text-white">{{ initials }}</span>
            </div>
          </div>

          <!-- Patient Basic Info -->
          <div class="flex-1 min-w-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <p class="text-xs font-semibold text-emerald-50 uppercase tracking-wider">Full Name</p>
                <p class="text-lg font-bold text-white mt-1 upp">{{ fullName }}</p>
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
                  {{ patientTypeLabel }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vital Signs -->
    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
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
    <p class="text-xs text-slate-400 -mt-3">Last measured {{ formatDateTime(latestVitalSignsMeasuredAt) }}</p> -->

    <!-- SOAP Note Modal -->
    <Dialog v-model:visible="soapModal" modal :style="{ width: '52vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <BsJournalMedical class="text-white" size="17" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">{{ isSoapUpdate ? "Edit SOAP Note" : "New SOAP Note" }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ isSoapUpdate ? "Update the existing SOAP note" : "Record a subjective, objective, assessment & plan note" }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="saveSoap" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">ICD Diagnosis <span class="text-red-400">*</span></label>
          <Select v-model="soapInfo.icd_pid" :options="icds" optionLabel="name" optionValue="pid" filter required fluid placeholder="Select ICD code" class="text-sm">
            <template #option="{ option }">
              <span class="text-sm"
                ><span class="font-medium text-slate-700">{{ option.code }}</span> — {{ option.name }}</span
              >
            </template>
            <template #value="{ value, placeholder }">
              <span v-if="selectedIcd(value)" class="text-sm">
                <span class="font-medium text-slate-700">{{ selectedIcd(value)?.code }}</span> — {{ selectedIcd(value)?.name }}
              </span>
              <span v-else class="text-slate-400 text-sm">{{ placeholder }}</span>
            </template>
          </Select>
        </div>
        <div class="grid grid-cols-1 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Subjective <span class="text-red-400">*</span></label>
            <Textarea v-model="soapInfo.subjective" rows="3" autoResize fluid required placeholder="Patient-reported symptoms & history..." class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Objective <span class="text-red-400">*</span></label>
            <Textarea v-model="soapInfo.objective" rows="3" autoResize fluid required placeholder="Measurable/observable findings..." class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Assessment <span class="text-red-400">*</span></label>
            <Textarea v-model="soapInfo.assessment" rows="3" autoResize fluid required placeholder="Diagnosis / clinical impression..." class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Plan <span class="text-red-400">*</span></label>
            <Textarea v-model="soapInfo.plan" rows="3" autoResize fluid required placeholder="Treatment / management plan..." class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Remarks <span class="italic">(optional)</span></label>
            <Textarea v-model="soapInfo.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="soapModal = false" />
          <Button type="submit" :label="isSoapUpdate ? 'Update Note' : 'Save Note'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left Column -->
      <div class="space-y-6 h-full">
        <!-- Personal & Contact Information -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-full flex flex-col">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
              <FiUser class="text-white" size="14" />
            </div>
            <h3 class="text-base font-bold text-slate-800">Personal &amp; Contact Information</h3>
          </header>
          <div class="p-4 space-y-2 flex-1 text-sm">
            <div v-for="row in personalInfo" :key="row.label" class="flex justify-between items-start gap-4 pb-2 border-b border-slate-100 last:border-0 last:pb-0">
              <span class="text-slate-500 shrink-0">{{ row.label }}</span>
              <span class="font-medium text-slate-800 text-right">{{ row.value }}</span>
            </div>
          </div>
        </section>
      </div>

      <!-- Middle Column -->
      <div class="space-y-6 h-full">
        <!-- Admission Details & Diagnosis -->
        <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-full flex flex-col">
          <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
                <FiCalendar class="text-white" size="14" />
              </div>
              <h3 class="text-base font-bold text-slate-800">Details &amp; Diagnosis</h3>
            </div>
            <button
              v-if="can('patient-cases', 'update')"
              type="button"
              @click="openEditCase"
              title="Edit case information"
              class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
            >
              <FiEdit2 size="15" />
            </button>
          </header>
          <div class="p-4 space-y-2 flex-1 text-sm">
            <div class="flex justify-between items-start pb-2 border-b border-slate-100">
              <span class="text-slate-500">Case Number</span>
              <span class="font-mono font-medium text-slate-800">{{ patientCase.case_number }}</span>
            </div>
            <div class="flex justify-between items-start pb-2 border-b border-slate-100">
              <span class="text-slate-500">Arrival Date &amp; Time</span>
              <span class="font-medium text-slate-800">{{ formatDateTime(patientCase.admission_datetime) }}</span>
            </div>
            <div class="pb-2 border-b border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Chief Complaint</span>
              <p class="font-medium text-slate-800 mt-0.5">{{ patientCase.chief_complaint }}</p>
            </div>
            <div class="pb-2 border-b border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Initial Diagnosis</span>
              <p class="font-medium text-slate-800 mt-0.5">{{ patientCase.initial_diagnosis }}</p>
            </div>
            <div class="pb-2 border-b border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Final Diagnosis</span>
              <p class="font-medium text-slate-800 mt-0.5">{{ patientCase.final_diagnosis }}</p>
            </div>
            <div>
              <div class="flex items-center gap-2 mb-2">
                <div class="w-5 h-5 rounded-md bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
                  <FiEye class="text-white" size="11" />
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Visual Acuity</span>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div class="rounded-lg bg-linear-to-br from-emerald-50 to-teal-50 border border-emerald-100 px-3 py-1.5">
                  <span class="font-bold text-emerald-600">OD</span> <span class="font-mono font-semibold text-slate-800">{{ patientCase.od || "—" }}</span>
                  <span class="text-slate-400 ml-1.5">PH</span> <span class="font-semibold text-slate-700">{{ patientCase.ph_right || "—" }}</span>
                </div>
                <div class="rounded-lg bg-linear-to-br from-emerald-50 to-teal-50 border border-emerald-100 px-3 py-1.5">
                  <span class="font-bold text-emerald-600">OS</span> <span class="font-mono font-semibold text-slate-800">{{ patientCase.os || "—" }}</span>
                  <span class="text-slate-400 ml-1.5">PH</span> <span class="font-semibold text-slate-700">{{ patientCase.ph_left || "—" }}</span>
                </div>
              </div>

              <div class="mt-1.5 rounded-lg border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-3 py-1 bg-slate-50">
                  <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">With Correction (CC)</span>
                  <span class="font-semibold text-slate-700">{{ patientCase.cc || "—" }}</span>
                </div>
                <div class="grid grid-cols-2 divide-x divide-slate-100">
                  <div class="px-3 py-1.5">
                    <span class="text-slate-400">OD</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_od || "—" }}</span>
                    <span class="text-slate-400 ml-2">PH</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_ph_right || "—" }}</span>
                  </div>
                  <div class="px-3 py-1.5">
                    <span class="text-slate-400">OS</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_os || "—" }}</span>
                    <span class="text-slate-400 ml-2">PH</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_ph_left || "—" }}</span>
                  </div>
                </div>
              </div>

              <div class="mt-1.5 flex items-center justify-between rounded-lg bg-slate-50 border border-slate-200 px-3 py-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">IOP</span>
                <span class="font-bold text-slate-800 font-mono">{{ patientCase.iop || "—" }}</span>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- SOAP Notes -->
    <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <header class="px-5 py-4 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
            <BsJournalMedical class="text-white" size="14" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-800">SOAP Notes</h3>
            <p class="text-sm text-slate-400">Subjective, Objective, Assessment &amp; Plan documentation</p>
          </div>
        </div>
        <button
          v-if="can('soaps', 'create')"
          type="button"
          @click="openCreateSoap"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
        >
          <BsPlusCircle size="16" />
          Add SOAP Note
        </button>
      </header>

      <div v-if="!soaps.length" class="flex flex-col items-center justify-center py-12 text-slate-400">
        <BsJournalMedical size="32" class="mb-3 opacity-30" />
        <p class="text-base font-medium">No SOAP notes recorded</p>
      </div>

      <div v-else class="divide-y divide-slate-100">
        <div v-for="note in soaps" :key="note.pid" class="p-5">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                {{ note.doctor?.firstname?.charAt(0) ?? "?" }}
              </div>
              <span class="text-base font-medium text-slate-700">
                {{ `${note.doctor?.firstname ?? ""} ${note.doctor?.lastname ?? ""}`.trim() || "—" }}
              </span>
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700"> {{ note.icd?.code }} — {{ note.icd?.name }} </span>
            </div>
            <div class="flex items-center gap-1">
              <router-link
                :to="{ name: 'SoapPrint', params: { pid: note.pid } }"
                target="_blank"
                title="Print note"
                class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors duration-150 cursor-pointer"
              >
                <FiPrinter size="18" />
              </router-link>
              <button
                v-if="can('soaps', 'update')"
                type="button"
                title="Edit note"
                @click="editSoap(note.pid)"
                class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
              >
                <BiEdit size="18" />
              </button>
              <button
                v-if="can('soaps', 'delete')"
                type="button"
                title="Delete note"
                @click="deleteSoap(note.pid)"
                class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
              >
                <BiTrash size="18" />
              </button>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <span class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Subjective</span>
              <p class="text-base text-slate-700 mt-1">{{ note.subjective || "—" }}</p>
            </div>
            <div>
              <span class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Objective</span>
              <p class="text-base text-slate-700 mt-1">{{ note.objective || "—" }}</p>
            </div>
            <div>
              <span class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Assessment</span>
              <p class="text-base text-slate-700 mt-1">{{ note.assessment || "—" }}</p>
            </div>
            <div>
              <span class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Plan</span>
              <p class="text-base text-slate-700 mt-1">{{ note.plan || "—" }}</p>
            </div>
            <div v-if="note.remarks" class="sm:col-span-2">
              <span class="text-sm font-semibold text-slate-400 uppercase tracking-wide">Remarks</span>
              <p class="text-base text-slate-700 mt-1">{{ note.remarks }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, markRaw } from "vue";
import { useRoute } from "vue-router";
import { FiUser, FiCalendar, FiEdit2, FiPrinter, FiHeart, FiActivity, FiThermometer, FiDroplet, FiEye } from "vue-icons-plus/fi";
import { FaTextHeight, FaWeight } from "vue-icons-plus/fa";
import { BsJournalMedical, BsPlusCircle } from "vue-icons-plus/bs";
import { BiEdit, BiTrash } from "vue-icons-plus/bi";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useAppToast } from "@/composables/toast";
import { useVitalSignsStore } from "@/store/patientchart/VitalSigns";
import { useSoapStore } from "@/store/patientchart/Soap";
import { useIcdStore } from "@/store/Icd";
import { usePermission } from "@/composables/permission";
import { useConfirmToast } from "@/composables/confirm";
const route = useRoute();
const toast = useAppToast();
const { can } = usePermission();
const { showConfirm } = useConfirmToast();
const patientCaseStore = usePatientCaseStore();
const vitalSignStore = useVitalSignsStore();
const soapStore = useSoapStore();
const icdStore = useIcdStore();

const patientCasePid = computed(() => route.params.patient_case_pid);
const patientCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => patientCase.value?.patient);
const soaps = computed(() => soapStore.soaps);
const icds = computed(() => icdStore.icds);

const patientTypeLabel = computed(() => {
  const type = patientCase.value?.type;
  return type ? `${type.charAt(0).toUpperCase()}${type.slice(1)}` : "—";
});

const fullName = computed(() => {
  const middle = patient.value.middlename ? `${patient.value.middlename.charAt(0)}.` : "";
  const suffix = patient.value.suffix ? ` ${patient.value.suffix}` : "";
  return [patient.value.firstname, middle, `${patient.value.lastname}${suffix}`].filter(Boolean).join(" ");
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
  { label: "Date of Birth", value: formatDate(patient.value.birthdate) },
  { label: "Gender", value: patient.value.gender },
  { label: "Civil Status", value: patient.value.civil_status },
  { label: "Religion", value: patient.value.religion },
  { label: "Birthplace", value: patient.value.birthplace },
  { label: "Contact Number", value: patient.value.contact_number },
  { label: "Email Address", value: patient.value.email_address },
]);

const latestVitalSigns = computed(() => vitalSignStore.latestVitalSigns);
const latestVitalSignsMeasuredAt = ref(null);
const vitalStats = ref([
  {
    label: "Blood Pressure",
    value: `N/A`,
    icon: markRaw(FiHeart),
    bgColor: "#ecfdf5",
    color: "#059669",
  },
  {
    label: "Heart Rate",
    value: `N/A`,
    icon: markRaw(FiActivity),
    bgColor: "#eff6ff",
    color: "#3b82f6",
  },
  {
    label: "Temperature",
    value: `N/A`,
    icon: markRaw(FiThermometer),
    bgColor: "#fdf4ff",
    color: "#a855f7",
  },
  {
    label: "O2 Saturation",
    value: `N/A`,
    icon: markRaw(FiDroplet),
    bgColor: "#fef2f2",
    color: "#dc2626",
  },
  {
    label: "Weight",
    value: `N/A`,
    icon: markRaw(FaWeight),
    bgColor: "#fffbeb",
    color: "#d97706",
  },
  {
    label: "Height",
    value: `N/A`,
    icon: markRaw(FaTextHeight),
    bgColor: "#fffbeb",
    color: "#d97706",
  },
]);
watch(
  () => latestVitalSigns.value,
  () => {
    if (latestVitalSigns.value) {
      if (latestVitalSigns.value?.systolic && latestVitalSigns.value?.diastolic) {
        vitalStats.value[0].value = `${latestVitalSigns.value?.systolic}/${latestVitalSigns.value?.diastolic}`;
      }
      if (latestVitalSigns.value?.heart_rate) {
        vitalStats.value[1].value = `${latestVitalSigns.value?.heart_rate} bpm`;
      }
      if (latestVitalSigns.value?.temperature) {
        vitalStats.value[2].value = `${latestVitalSigns.value?.temperature} °C`;
      }
      if (latestVitalSigns.value?.oxygen_saturation) {
        vitalStats.value[3].value = `${latestVitalSigns.value?.oxygen_saturation} %`;
      }
      if (latestVitalSigns.value?.weight) {
        vitalStats.value[4].value = `${Number(latestVitalSigns.value?.weight)} kg/s`;
      }
      if (latestVitalSigns.value?.height) {
        vitalStats.value[5].value = `${Number(latestVitalSigns.value?.height)} cm`;
      }
      if (latestVitalSigns.value?.measured_at) {
        latestVitalSignsMeasuredAt.value = latestVitalSigns.value?.measured_at;
      }
    }
  }
);
onMounted(async () => {
  // await vitalSignStore.getLatestVitalSigns(route.params.patient_case_pid);
  if (!patientCasePid.value) return;
  try {
    await patientCaseStore.view(patientCasePid.value);
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to load patient information");
  }
  if (can("soaps", "view")) {
    try {
      await soapStore.read(patientCasePid.value);
    } catch (err) {
      toast.error(err.response?.data?.message || "Failed to load SOAP notes");
    }
  }

  if (can("icds", "view")) {
    try {
      await icdStore.read();
    } catch (err) {
      toast.error(err.response?.data?.message || "Failed to load ICD codes");
    }
  }
});

// SOAP Notes
const soapModal = ref(false);
const isSoapUpdate = ref(false);
const defaultSoapInfo = () => ({
  pid: "",
  patient_case_pid: "",
  icd_pid: "",
  subjective: "",
  objective: "",
  assessment: "",
  plan: "",
  remarks: "",
});
const soapInfo = reactive(defaultSoapInfo());
const selectedIcd = (pid) => icds.value.find((i) => i.pid === pid);

watch(soapModal, (open) => {
  if (!open) {
    Object.assign(soapInfo, defaultSoapInfo());
    isSoapUpdate.value = false;
  }
});

const openCreateSoap = () => {
  Object.assign(soapInfo, defaultSoapInfo());
  isSoapUpdate.value = false;
  soapModal.value = true;
};

const saveSoap = async () => {
  try {
    soapInfo.patient_case_pid = patientCasePid.value || "";
    if (isSoapUpdate.value) {
      await soapStore.update(soapInfo);
      toast.success("SOAP note updated successfully");
    } else {
      await soapStore.create(soapInfo);
      toast.success("SOAP note created successfully");
    }
    soapModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to save SOAP note");
  }
};

const editSoap = async (pid) => {
  try {
    await soapStore.view(pid);
    const record = soapStore.soap;
    Object.assign(soapInfo, defaultSoapInfo(), record, {
      patient_case_pid: patientCasePid.value || "",
      icd_pid: record.icd?.pid || record.icd_pid || "",
    });
    isSoapUpdate.value = true;
    soapModal.value = true;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve SOAP note");
  }
};

const deleteSoap = (pid) => {
  showConfirm({
    message: "Are you sure you want to delete this SOAP note?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await soapStore.archive(pid, patientCasePid.value);
        toast.success("SOAP note deleted successfully");
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to delete SOAP note");
      }
    },
    onReject: () => {},
  });
};

// Edit Case Information
const caseModal = ref(false);
const defaultCaseInfo = () => ({
  admission_datetime: null,
  chief_complaint: "",
  initial_diagnosis: "",
  final_diagnosis: "",
  od: "",
  os: "",
  ph_right: "",
  ph_left: "",
  cc: "",
  cc_od: "",
  cc_os: "",
  cc_ph_right: "",
  cc_ph_left: "",
  iop: "",
});
const caseInfo = reactive(defaultCaseInfo());

watch(caseModal, (open) => {
  if (!open) Object.assign(caseInfo, defaultCaseInfo());
});

const openEditCase = () => {
  Object.assign(caseInfo, defaultCaseInfo(), {
    chief_complaint: patientCase.value.chief_complaint || "",
    initial_diagnosis: patientCase.value.initial_diagnosis || "",
    final_diagnosis: patientCase.value.final_diagnosis || "",
    admission_datetime: patientCase.value.admission_datetime ? new Date(patientCase.value.admission_datetime) : null,
    od: patientCase.value.od || "",
    os: patientCase.value.os || "",
    ph_right: patientCase.value.ph_right || "",
    ph_left: patientCase.value.ph_left || "",
    cc: patientCase.value.cc || "",
    cc_od: patientCase.value.cc_od || "",
    cc_os: patientCase.value.cc_os || "",
    cc_ph_right: patientCase.value.cc_ph_right || "",
    cc_ph_left: patientCase.value.cc_ph_left || "",
    iop: patientCase.value.iop || "",
  });
  caseModal.value = true;
};

const updateCase = async () => {
  try {
    await patientCaseStore.update(patientCasePid.value, caseInfo);
    toast.success("Case information updated successfully");
    caseModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to update case information");
  }
};

const printSummary = () => {
  window.print();
};

const formatDate = (dateString) => {
  if (!dateString) return "—";
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatDateTime = (dateString) => {
  if (!dateString) return "—";
  const options = { year: "numeric", month: "short", day: "numeric", hour: "2-digit", minute: "2-digit" };
  return new Date(dateString).toLocaleString(undefined, options);
};
</script>
