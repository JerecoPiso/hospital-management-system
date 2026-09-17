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

    <!-- Payment Dialog -->
    <Dialog v-model:visible="paymentModalOpen" modal :style="{ width: '32vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiCreditCard class="text-white" size="15" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Record Payment</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ activeInvoice?.invoice_number || "—" }} &bull; Balance ₱{{ Number(activeInvoice?.balance || 0).toFixed(2) }}</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="submitPayment" class="flex flex-col gap-5 pt-2">
        <div class="flex flex-col gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Amount <span class="text-red-400">*</span></label>
            <InputNumber v-model="paymentForm.amount_paid" mode="currency" currency="PHP" locale="en-PH" :min="0.01" :max="Number(activeInvoice?.balance || 0)" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Payment Method</label>
            <Select v-model="paymentForm.payment_method" :options="paymentMethodOptions" optionLabel="label" optionValue="value" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Reference Number</label>
            <InputText v-model="paymentForm.reference_number" placeholder="Optional reference / OR number" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Date/Time Paid</label>
            <DatePicker v-model="paymentDateModel" showTime hourFormat="24" dateFormat="yy-mm-dd" fluid class="text-sm" />
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="paymentModalOpen = false" />
          <Button type="submit" label="Save Payment" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Invoice Items Dialog -->
    <Dialog v-model:visible="itemsModalOpen" modal :style="{ width: '40vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiFileText class="text-white" size="15" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Invoice Items</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ viewingInvoice?.invoice_number || "—" }} &bull; {{ formatDate(viewingInvoice?.created_at) }}</p>
          </div>
        </div>
      </template>
      <div class="flex flex-col gap-4 pt-2">
        <div v-if="!viewingInvoice?.items?.length" class="text-sm text-slate-400 italic py-6 text-center">No items recorded for this invoice.</div>
        <div v-else class="border border-slate-200 rounded-lg overflow-hidden">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Description</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Qty</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Unit Price</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Subtotal</th>
              </tr>
            </thead>
            <tbody v-for="group in viewingItemsByCategory" :key="group.category">
              <tr class="bg-emerald-50/60">
                <td colspan="4" class="px-4 py-1.5 text-xs font-bold text-emerald-700 uppercase tracking-wide">{{ group.category }}</td>
              </tr>
              <tr v-for="item in group.items" :key="item.pid" class="border-b border-slate-100 last:border-0">
                <td class="px-4 py-2.5 text-slate-700">{{ item.description }}</td>
                <td class="px-4 py-2.5 text-right text-slate-600">{{ item.quantity }}</td>
                <td class="px-4 py-2.5 text-right text-slate-600">₱{{ Number(item.unit_price || 0).toFixed(2) }}</td>
                <td class="px-4 py-2.5 text-right text-slate-800 font-medium">₱{{ Number(item.subtotal || 0).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex flex-col gap-1.5 rounded-lg bg-slate-50 border border-slate-200 p-4 text-sm">
          <div class="flex justify-between text-slate-500"><span>Subtotal</span><span>₱{{ Number(viewingInvoice?.subtotal || 0).toFixed(2) }}</span></div>
          <div v-if="Number(viewingInvoice?.discount_amount || 0) > 0" class="flex justify-between text-slate-500"><span>Discount</span><span>-₱{{ Number(viewingInvoice?.discount_amount || 0).toFixed(2) }}</span></div>
          <div v-if="Number(viewingInvoice?.tax_amount || 0) > 0" class="flex justify-between text-slate-500"><span>Tax</span><span>₱{{ Number(viewingInvoice?.tax_amount || 0).toFixed(2) }}</span></div>
          <div class="flex justify-between text-slate-800 font-bold pt-1.5 border-t border-slate-200"><span>Total</span><span>₱{{ Number(viewingInvoice?.total_amount || 0).toFixed(2) }}</span></div>
          <div class="flex justify-between text-emerald-600"><span>Paid</span><span>₱{{ Number(viewingInvoice?.paid_amount || 0).toFixed(2) }}</span></div>
          <div class="flex justify-between text-red-500 font-semibold"><span>Balance</span><span>₱{{ Number(viewingInvoice?.balance || 0).toFixed(2) }}</span></div>
        </div>

        <div class="flex gap-2 pt-1">
          <Button type="button" label="Close" severity="secondary" outlined fluid @click="itemsModalOpen = false" />
          <router-link v-if="viewingInvoice?.pid" :to="{ name: 'InvoicePrint', params: { pid: viewingInvoice.pid } }" target="_blank" class="w-full">
            <Button type="button" label="Print" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0">
              <template #icon><FiPrinter size="15" class="mr-2" /></template>
            </Button>
          </router-link>
        </div>
      </div>
    </Dialog>

    <!-- Vital Signs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
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
    <p class="text-xs text-slate-400 -mt-3">Last measured {{ formatDateTime(latestVitalSignsMeasuredAt) }}</p>

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

    <!-- Billing & Invoices -->
    <div v-if="can('invoices', 'view')" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
      <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
            <FiFileText class="text-white" size="18" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-800">Billing &amp; Invoices</h3>
            <p class="text-xs text-slate-400">Charges recorded for this case</p>
          </div>
        </div>
        <button
          v-if="can('invoices', 'create')"
          type="button"
          @click="generateInvoice"
          :disabled="generating"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <BsPlusCircle size="16" />
          {{ generating ? "Generating..." : "Generate Invoice" }}
        </button>
      </div>

      <div v-if="invoicesLoading" class="p-8 text-center text-sm text-slate-400">Loading invoices...</div>
      <div v-else-if="!invoices.length" class="flex flex-col items-center justify-center py-12 text-slate-400">
        <FiFileText size="32" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No invoices yet</p>
        <p class="text-xs mt-1">Click "Generate Invoice" to bill the charges recorded for this case</p>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Invoice #</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Paid</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Balance</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inv in invoices" :key="inv.pid" class="border-b border-slate-100 hover:bg-slate-50 transition-colors duration-150 last:border-0">
              <td class="px-6 py-4 text-sm font-mono text-slate-700">{{ inv.invoice_number }}</td>
              <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(inv.created_at) }}</td>
              <td class="px-6 py-4 text-sm text-right text-slate-800 font-medium">₱{{ Number(inv.total_amount || 0).toFixed(2) }}</td>
              <td class="px-6 py-4 text-sm text-right text-emerald-600">₱{{ Number(inv.paid_amount || 0).toFixed(2) }}</td>
              <td class="px-6 py-4 text-sm text-right text-red-500 font-medium">₱{{ Number(inv.balance || 0).toFixed(2) }}</td>
              <td class="px-6 py-4">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="invoiceStatusClass(inv.status)">{{ invoiceStatusLabel(inv.status) }}</span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    type="button"
                    title="View items"
                    @click="openItems(inv)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold transition-colors duration-150 cursor-pointer"
                  >
                    <FiEye size="14" />
                    Items
                  </button>
                  <router-link
                    :to="{ name: 'InvoicePrint', params: { pid: inv.pid } }"
                    target="_blank"
                    title="Print invoice"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold transition-colors duration-150 cursor-pointer"
                  >
                    <FiPrinter size="14" />
                    Print
                  </router-link>
                  <button
                    v-if="can('invoices', 'update') && Number(inv.balance || 0) > 0"
                    type="button"
                    @click="openPayment(inv)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors duration-150 cursor-pointer"
                  >
                    <FiCreditCard size="14" />
                    Pay
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, markRaw } from "vue";
import { useRoute } from "vue-router";
import { FiUser, FiUsers, FiCalendar, FiEdit2, FiPrinter, FiMapPin, FiHeart, FiActivity, FiThermometer, FiDroplet, FiFileText, FiCreditCard, FiEye } from "vue-icons-plus/fi";
import { FaTextHeight, FaWeight } from "vue-icons-plus/fa";
import { BsPlusCircle } from "vue-icons-plus/bs";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useInvoiceStore } from "@/store/patientchart/Invoices";
import { useAppToast } from "@/composables/toast";
import { useConfirmToast } from "@/composables/confirm";
import { usePermission } from "@/composables/permission";
import { useVitalSignsStore } from "@/store/patientchart/VitalSigns";
const route = useRoute();
const toast = useAppToast();
const { showConfirm } = useConfirmToast();
const { can } = usePermission();
const patientCaseStore = usePatientCaseStore();
const invoiceStore = useInvoiceStore();
const vitalSignStore = useVitalSignsStore();

const patientCasePid = computed(() => route.params.patient_case_pid);
const patientCase = computed(() => patientCaseStore.patientCase);
const patient = computed(() => patientCase.value?.patient);

const patientTypeLabel = computed(() => {
  const type = patientCase.value?.type;
  return type ? `${type.charAt(0).toUpperCase()}${type.slice(1)}` : "—";
});

// Static placeholder data shaped like the bed/room/ward/station hierarchy.
const assignment = ref({
  ward: "Medical Ward",
  station: "Station A",
  room: "305A",
  bed: "Bed 2",
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

const familyInfo = computed(() => [
  { label: "Occupation", value: patient.value.occupation },
  { label: "Spouse Name", value: patient.value.spouse_name || "—" },
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
  await vitalSignStore.getLatestVitalSigns(route.params.patient_case_pid);
  if (!patientCasePid.value) return;
  try {
    await patientCaseStore.view(patientCasePid.value);
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to load patient information");
  }
  await loadInvoices();
});

// Billing & Invoices
const invoices = computed(() => invoiceStore.invoices);
const invoicesLoading = ref(false);
const generating = ref(false);

const loadInvoices = async () => {
  if (!patientCasePid.value || !can("invoices", "view")) return;
  invoicesLoading.value = true;
  try {
    await invoiceStore.read(patientCasePid.value);
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve invoices");
  } finally {
    invoicesLoading.value = false;
  }
};

const invoiceStatusLabel = (status) => (status ? status.replace("_", " ").replace(/\b\w/g, (c) => c.toUpperCase()) : "—");

const invoiceStatusClass = (status) => {
  switch (status) {
    case "paid":
      return "bg-emerald-50 text-emerald-700 border border-emerald-100";
    case "partially_paid":
      return "bg-amber-50 text-amber-700 border border-amber-100";
    case "cancelled":
      return "bg-red-50 text-red-600 border border-red-100";
    default:
      return "bg-slate-100 text-slate-600 border border-slate-200";
  }
};

const generateInvoice = () => {
  if (!patientCasePid.value) return;
  showConfirm({
    message: "Generate a new invoice from the charges recorded for this case?",
    header: "Generate Invoice",
    onAccept: async () => {
      generating.value = true;
      try {
        await invoiceStore.generate(patientCasePid.value);
        toast.success("Invoice generated successfully");
        await loadInvoices();
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to generate invoice");
      } finally {
        generating.value = false;
      }
    },
  });
};

const paymentModalOpen = ref(false);
const activeInvoice = ref(null);
const paymentDateModel = ref(new Date());
const defaultPaymentForm = () => ({ amount_paid: 0, payment_method: "cash", reference_number: "" });
const paymentForm = reactive(defaultPaymentForm());
const paymentMethodOptions = [
  { label: "Cash", value: "cash" },
  { label: "Card", value: "card" },
  { label: "Insurance", value: "insurance" },
  { label: "Online Banking", value: "online_banking" },
  { label: "E-Wallet", value: "e_wallet" },
];

watch(paymentModalOpen, (open) => {
  if (!open) {
    Object.assign(paymentForm, defaultPaymentForm());
    activeInvoice.value = null;
    paymentDateModel.value = new Date();
  }
});

const itemsModalOpen = ref(false);
const viewingInvoice = ref(null);

const viewingItemsByCategory = computed(() => {
  const items = viewingInvoice.value?.items || [];
  const groups = [];
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

const openItems = (inv) => {
  viewingInvoice.value = inv;
  itemsModalOpen.value = true;
};

const openPayment = (inv) => {
  activeInvoice.value = inv;
  paymentForm.amount_paid = Number(inv.balance || 0);
  paymentDateModel.value = new Date();
  paymentModalOpen.value = true;
};

const submitPayment = async () => {
  if (!activeInvoice.value?.pid) return;
  try {
    await invoiceStore.pay(activeInvoice.value.pid, {
      amount_paid: paymentForm.amount_paid,
      payment_method: paymentForm.payment_method,
      reference_number: paymentForm.reference_number || null,
      paid_at: paymentDateModel.value ? paymentDateModel.value.toISOString() : null,
    });
    toast.success("Payment recorded successfully");
    paymentModalOpen.value = false;
    await loadInvoices();
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to record payment");
  }
};

// Methods
const editProfile = () => {
  console.log("Edit profile clicked");
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
