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
    <Dialog v-model:visible="caseModal" modal :style="{ width: '60vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
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
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Arrival Date &amp; Time <span class="text-red-400">*</span></label>
            <DatePicker v-model="caseInfo.admission_datetime" showTime hourFormat="24" fluid required />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Chief Complaint <span class="text-red-400">*</span></label>
            <Textarea v-model="caseInfo.chief_complaint" rows="1" autoResize fluid required class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Initial Diagnosis</label>
            <Textarea v-model="caseInfo.initial_diagnosis" rows="1" autoResize fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Final Diagnosis</label>
            <Textarea v-model="caseInfo.final_diagnosis" rows="1" autoResize fluid class="text-sm" />
          </div>
        </div>

        <div class="flex flex-col gap-3 pt-2 border-t border-slate-100">
          <span class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Visual Acuity</span>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="col-span-1">
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
            <div class="col-span-1">
              <div class="flex flex-col gap-3 pt-2">
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
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
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
          <div class="flex justify-between text-slate-500">
            <span>Subtotal</span><span>₱{{ Number(viewingInvoice?.subtotal || 0).toFixed(2) }}</span>
          </div>
          <div v-if="Number(viewingInvoice?.discount_amount || 0) > 0" class="flex justify-between text-slate-500">
            <span>Discount</span><span>-₱{{ Number(viewingInvoice?.discount_amount || 0).toFixed(2) }}</span>
          </div>
          <div v-if="Number(viewingInvoice?.tax_amount || 0) > 0" class="flex justify-between text-slate-500">
            <span>Tax</span><span>₱{{ Number(viewingInvoice?.tax_amount || 0).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-slate-800 font-bold pt-1.5 border-t border-slate-200">
            <span>Total</span><span>₱{{ Number(viewingInvoice?.total_amount || 0).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-emerald-600">
            <span>Paid</span><span>₱{{ Number(viewingInvoice?.paid_amount || 0).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-red-500 font-semibold">
            <span>Balance</span><span>₱{{ Number(viewingInvoice?.balance || 0).toFixed(2) }}</span>
          </div>
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
          <div class="p-4 space-y-6 flex-1 text-sm">
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
                <div class="flex justify-between rounded-lg bg-linear-to-br from-emerald-50 to-teal-50 border border-emerald-100 px-3 py-1.5">
                  <span class="font-bold text-emerald-600">OD</span> <span class="font-mono font-semibold text-slate-800">{{ patientCase.od || "—" }}</span>
                  <span class="font-bold text-emerald-600 ml-5">PH</span> <span class="font-semibold text-slate-700">{{ patientCase.ph_right || "—" }}</span>
                </div>
                <div class="flex justify-between rounded-lg bg-linear-to-br from-emerald-50 to-teal-50 border border-emerald-100 px-3 py-1.5">
                  <span class="font-bold text-emerald-600">OS</span> <span class="font-mono font-semibold text-slate-800">{{ patientCase.os || "—" }}</span>
                  <span class="font-bold text-emerald-600 ml-5">PH</span> <span class="font-semibold text-slate-700">{{ patientCase.ph_left || "—" }}</span>
                </div>
              </div>

              <div class="mt-1.5 rounded-lg border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-3 py-1 bg-slate-50">
                  <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">With Correction (CC)</span>
                  <span class="font-semibold text-slate-700">{{ patientCase.cc || "—" }}</span>
                </div>
                <div class="grid grid-cols-2 divide-x divide-slate-100">
                  <div class="flex justify-between px-3 py-1.5">
                    <span class="font-bold text-emerald-600">OD</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_od || "—" }}</span>
                    <span class="font-bold text-emerald-600 ml-5">PH</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_ph_right || "—" }}</span>
                  </div>
                  <div class="flex justify-between px-3 py-1.5">
                    <span class="font-bold text-emerald-600">OS</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_os || "—" }}</span>
                    <span class="font-bold text-emerald-600 ml-5">PH</span> <span class="font-semibold text-slate-800">{{ patientCase.cc_ph_left || "—" }}</span>
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
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700 uppercase tracking-wide">Subjective</span>
              <p class="text-base text-slate-700 mt-1 ml-0.5">{{ note.subjective || "—" }}</p>
            </div>
            <div>
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700 uppercase tracking-wide">Objective</span>
              <p class="text-base text-slate-700 mt-1 ml-0.5">{{ note.objective || "—" }}</p>
            </div>
            <div>
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700 uppercase tracking-wide">Assessment</span>
              <p class="text-base text-slate-700 mt-1 ml-0.5">{{ note.assessment || "—" }}</p>
            </div>
            <div>
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700 uppercase tracking-wide">Plan</span>
              <p class="text-base text-slate-700 mt-1 ml-0.5">{{ note.plan || "—" }}</p>
            </div>
            <div v-if="note.remarks" class="sm:col-span-2">
              <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-medium text-emerald-700 uppercase tracking-wide">Remarks</span>
              <p class="text-base text-slate-700 mt-1 ml-0.5">{{ note.remarks }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Bed Assignment Dialog -->
      <Dialog v-model:visible="bedModal" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
              <FaBed class="text-white" size="15" />
            </div>
            <div>
              <h2 class="text-base font-semibold text-slate-800">{{ isBedUpdate ? "Edit Bed Assignment" : "Assign Bed" }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ isBedUpdate ? "Update this bed assignment" : "Assign a bed to this patient case" }}</p>
            </div>
          </div>
        </template>
        <form @submit.prevent="isBedUpdate ? updateBedAssignment() : createBedAssignment()" class="flex flex-col gap-5 pt-2">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Bed <span class="text-red-400">*</span></label>
            <Select v-model="bedForm.bed_pid" :options="beds" :optionLabel="bedOptionLabel" optionValue="pid" placeholder="Select bed" filter required fluid class="text-sm" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">Started At <span class="text-red-400">*</span></label>
              <DatePicker v-model="bedStartedAtModel" showTime hourFormat="24" dateFormat="yy-mm-dd" fluid class="text-sm" />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">Ended At</label>
              <DatePicker v-model="bedEndedAtModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="Leave empty if active" showButtonBar fluid class="text-sm" />
            </div>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Remarks</label>
            <Textarea v-model="bedForm.remarks" rows="3" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
          </div>
          <div class="flex gap-2 pt-1">
            <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="bedModal = false" />
            <Button type="submit" :label="isBedUpdate ? 'Update Assignment' : 'Assign Bed'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
          </div>
        </form>
      </Dialog>

      <!-- Discharge Dialog -->
      <Dialog v-model:visible="dischargeModal" modal :style="{ width: '34vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
              <FiLogOut class="text-white" size="15" />
            </div>
            <div>
              <h2 class="text-base font-semibold text-slate-800">{{ isDischargeUpdate ? "Edit Discharge" : "Discharge Patient" }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ isDischargeUpdate ? "Update discharge details" : "Record discharge details for this case" }}</p>
            </div>
          </div>
        </template>
        <form @submit.prevent="isDischargeUpdate ? updateDischarge() : createDischarge()" class="flex flex-col gap-5 pt-2">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Discharge Date/Time <span class="text-red-400">*</span></label>
            <DatePicker v-model="dischargeDatetimeModel" showTime hourFormat="24" dateFormat="yy-mm-dd" required fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Disposition <span class="text-red-400">*</span></label>
            <Select v-model="dischargeForm.disposition" :options="dispositionOptions" placeholder="Select disposition" required fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Condition <span class="text-red-400">*</span></label>
            <Select v-model="dischargeForm.discharge_condition" :options="conditionOptions" placeholder="Select condition" required fluid class="text-sm" />
          </div>
          <div class="flex gap-2 pt-1">
            <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="dischargeModal = false" />
            <Button type="submit" :label="isDischargeUpdate ? 'Update Discharge' : 'Save Discharge'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
          </div>
        </form>
      </Dialog>

      <div class="grid col-san-1 lg:grid-cols-3 gap-5">
        <div class="col-span-2">
          <!-- Bed Assignment -->
          <div v-if="can('patient-case-beds', 'view')" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                  <FaBed class="text-white" size="18" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-slate-800">Bed Assignment</h3>
                  <p class="text-xs text-slate-400">{{ activeBedAssignment ? `Currently in ${bedLabel(activeBedAssignment.bed)}` : "No active bed assignment" }}</p>
                </div>
              </div>
              <button
                v-if="can('patient-case-beds', 'create')"
                type="button"
                @click="openCreateBed"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
              >
                <BsPlusCircle size="16" />
                Assign Bed
              </button>
            </div>

            <div v-if="bedAssignmentsLoading" class="p-8 text-center text-sm text-slate-400">Loading bed assignments...</div>
            <div v-else-if="!patientCaseBeds.length" class="flex flex-col items-center justify-center py-12 text-slate-400">
              <FaBed size="32" class="mb-3 opacity-30" />
              <p class="text-sm font-medium">No bed assignment recorded</p>
              <p class="text-xs mt-1">Click "Assign Bed" to assign one</p>
            </div>
            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Bed</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Started</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Ended</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Assigned By</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in patientCaseBeds" :key="row.pid" class="border-b border-slate-100 hover:bg-slate-50 transition-colors duration-150 last:border-0">
                    <td class="px-6 py-4 text-sm text-slate-700">{{ bedLabel(row.bed) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-slate-600">₱{{ Number(row.price || 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ formatDateTime(row.started_at) }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span v-if="!row.ended_at" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"
                        >Active</span
                      >
                      <span v-else class="text-slate-500">{{ formatDateTime(row.ended_at) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ userName(row.assigned_by) || "—" }}</td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex items-center justify-end gap-1">
                        <button
                          v-if="can('patient-case-beds', 'update') && !row.ended_at"
                          type="button"
                          title="End assignment"
                          @click="endBedAssignment(row)"
                          class="p-1.5 rounded-md text-amber-600 hover:bg-amber-50 hover:text-amber-700 transition-colors duration-150 cursor-pointer"
                        >
                          <FiLogOut size="16" />
                        </button>
                        <button
                          v-if="can('patient-case-beds', 'update')"
                          type="button"
                          title="Edit"
                          @click="editBedAssignment(row.pid)"
                          class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                          <BiEdit size="18" />
                        </button>
                        <button
                          v-if="can('patient-case-beds', 'delete')"
                          type="button"
                          title="Delete"
                          @click="archiveBedAssignment(row.pid)"
                          class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
                        >
                          <BiTrash size="18" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-span-1">
          <!-- Discharge -->
          <div v-if="can('patient-case-discharges', 'view')" class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                  <FiLogOut class="text-white" size="18" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-slate-800">Discharge</h3>
                  <p class="text-xs text-slate-400">{{ discharge ? "This case has been discharged" : "Discharge details for this case" }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  v-if="can('patient-case-discharges', 'create') && !discharge"
                  type="button"
                  @click="openCreateDischarge"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
                >
                  <BsPlusCircle size="16" />
                  Discharge Patient
                </button>
                <button
                  v-if="can('patient-case-discharges', 'update') && discharge"
                  type="button"
                  @click="editDischarge"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium shadow-sm transition-colors"
                >
                  <BiEdit size="15" />
                  Edit
                </button>
                <button
                  v-if="can('patient-case-discharges', 'delete') && discharge"
                  type="button"
                  @click="archiveDischarge"
                  class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-slate-200 hover:bg-red-50 text-red-500 text-sm font-medium shadow-sm transition-colors"
                >
                  <BiTrash size="15" />
                  Delete
                </button>
              </div>
            </div>

            <div v-if="dischargeLoading" class="p-8 text-center text-sm text-slate-400">Loading discharge information...</div>
            <div v-else-if="!discharge" class="flex flex-col items-center justify-center py-12 text-slate-400">
              <FiLogOut size="32" class="mb-3 opacity-30" />
              <p class="text-sm font-medium">Patient has not been discharged</p>
            </div>
            <div v-else class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Discharge Date/Time</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ formatDateTime(discharge.discharge_datetime) }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Disposition</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ discharge.disposition }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Condition</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ discharge.discharge_condition || "—" }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Discharged By</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ userName(discharge.discharged_by) || "—" }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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
import { FiUser, FiUsers, FiCalendar, FiEdit2, FiPrinter, FiMapPin, FiHeart, FiActivity, FiThermometer, FiDroplet, FiFileText, FiCreditCard, FiEye, FiLogOut } from "vue-icons-plus/fi";
import { FaTextHeight, FaWeight } from "vue-icons-plus/fa";
import { BsJournalMedical, BsPlusCircle } from "vue-icons-plus/bs";
import { BiEdit, BiTrash } from "vue-icons-plus/bi";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { useInvoiceStore } from "@/store/patientchart/Invoices";
import { usePatientCaseBedStore } from "@/store/patientchart/PatientCaseBed";
import { usePatientCaseDischargeStore } from "@/store/patientchart/PatientCaseDischarge";
import { useBedStore } from "@/store/Bed";
import { useAppToast } from "@/composables/toast";
import { useConfirmToast } from "@/composables/confirm";
import { useVitalSignsStore } from "@/store/patientchart/VitalSigns";
import { useSoapStore } from "@/store/patientchart/Soap";
import { useIcdStore } from "@/store/Icd";
import { usePermission } from "@/composables/permission";
const route = useRoute();
const toast = useAppToast();
const { can } = usePermission();
const { showConfirm } = useConfirmToast();

const patientCaseStore = usePatientCaseStore();
const invoiceStore = useInvoiceStore();
const vitalSignStore = useVitalSignsStore();
const soapStore = useSoapStore();
const icdStore = useIcdStore();
const patientCaseBedStore = usePatientCaseBedStore();
const patientCaseDischargeStore = usePatientCaseDischargeStore();
const bedStore = useBedStore();

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
  await loadInvoices();
  await Promise.all([loadBeds(), loadBedAssignments(), loadDischarge()]);
};

const userName = (user) => {
  if (!user) return "";
  return `${user.firstname ?? ""} ${user.lastname ?? ""}`.trim();
};

const formatForApi = (date) => {
  const pad = (n) => String(n).padStart(2, "0");
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
};

// Bed Assignment
const patientCaseBeds = computed(() => patientCaseBedStore.patientCaseBeds);
const beds = computed(() => bedStore.beds);
const activeBedAssignment = computed(() => patientCaseBeds.value.find((b) => !b.ended_at));
const bedAssignmentsLoading = ref(false);
const bedModal = ref(false);
const isBedUpdate = ref(false);
const defaultBedForm = () => ({ pid: "", patient_case_pid: "", bed_pid: "", started_at: "", ended_at: null, remarks: "" });
const bedForm = reactive(defaultBedForm());
const bedStartedAtModel = ref(new Date());
const bedEndedAtModel = ref(null);

watch(bedModal, (open) => {
  if (!open) {
    Object.assign(bedForm, defaultBedForm());
    isBedUpdate.value = false;
    bedStartedAtModel.value = new Date();
    bedEndedAtModel.value = null;
  }
});

const bedOptionLabel = (data) => `${data.room?.ward?.name ? data.room.ward.name + " — " : ""}${data.room?.room_number || ""} / Bed ${data.bed_number}`;
const bedLabel = (bed) => (bed ? `${bed.room?.ward?.name ? bed.room.ward.name + " — " : ""}${bed.room?.room_number || ""} / Bed ${bed.bed_number}` : "—");

const loadBedAssignments = async () => {
  if (!patientCasePid.value || !can("patient-case-beds", "view")) return;
  bedAssignmentsLoading.value = true;
  try {
    await patientCaseBedStore.read(patientCasePid.value);
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve bed assignments");
  } finally {
    bedAssignmentsLoading.value = false;
  }
};

const loadBeds = async () => {
  if (!can("patient-case-beds", "create") && !can("patient-case-beds", "update")) return;
  try {
    await bedStore.read();
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve beds");
  }
};

const openCreateBed = () => {
  Object.assign(bedForm, defaultBedForm());
  isBedUpdate.value = false;
  bedStartedAtModel.value = new Date();
  bedEndedAtModel.value = null;
  bedModal.value = true;
};

const createBedAssignment = async () => {
  try {
    bedForm.patient_case_pid = patientCasePid.value;
    bedForm.started_at = formatForApi(bedStartedAtModel.value);
    bedForm.ended_at = bedEndedAtModel.value ? formatForApi(bedEndedAtModel.value) : null;
    await patientCaseBedStore.create(bedForm);
    toast.success("Bed assigned successfully");
    bedModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to assign bed");
  }
};

const editBedAssignment = async (pid) => {
  try {
    await patientCaseBedStore.view(pid);
    const current = patientCaseBedStore.patientCaseBed;
    Object.assign(bedForm, {
      pid: current.pid,
      patient_case_pid: patientCasePid.value,
      bed_pid: current.bed?.pid || "",
      remarks: current.remarks || "",
    });
    bedStartedAtModel.value = current.started_at ? new Date(current.started_at) : new Date();
    bedEndedAtModel.value = current.ended_at ? new Date(current.ended_at) : null;
    isBedUpdate.value = true;
    bedModal.value = true;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve bed assignment");
  }
};

const updateBedAssignment = async () => {
  try {
    bedForm.patient_case_pid = patientCasePid.value;
    bedForm.started_at = formatForApi(bedStartedAtModel.value);
    bedForm.ended_at = bedEndedAtModel.value ? formatForApi(bedEndedAtModel.value) : null;
    await patientCaseBedStore.update(bedForm);
    toast.success("Bed assignment updated successfully");
    bedModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to update bed assignment");
  }
};

const endBedAssignment = (row) => {
  showConfirm({
    message: "End this bed assignment now?",
    header: "End Assignment",
    onAccept: async () => {
      try {
        await patientCaseBedStore.update({
          pid: row.pid,
          patient_case_pid: patientCasePid.value,
          bed_pid: row.bed?.pid,
          started_at: row.started_at,
          ended_at: formatForApi(new Date()),
          remarks: row.remarks,
        });
        toast.success("Bed assignment ended");
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to end bed assignment");
      }
    },
  });
};

const archiveBedAssignment = (pid) => {
  showConfirm({
    message: "Are you sure you want to delete this bed assignment record?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await patientCaseBedStore.archive(pid, patientCasePid.value);
        toast.success("Bed assignment deleted");
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to delete bed assignment");
      }
    },
  });
};

// Discharge
const patientCaseDischarges = computed(() => patientCaseDischargeStore.patientCaseDischarges);
const discharge = computed(() => patientCaseDischarges.value?.[0] || null);
const dischargeLoading = ref(false);
const dischargeModal = ref(false);
const isDischargeUpdate = ref(false);
const defaultDischargeForm = () => ({ pid: "", patient_case_pid: "", discharge_datetime: "", disposition: "", discharge_condition: "" });
const dischargeForm = reactive(defaultDischargeForm());
const dischargeDatetimeModel = ref(new Date());
const dispositionOptions = ["Home", "Transferred", "DAMA", "Absconded", "Death", "Other"];
const conditionOptions = ["Improved", "Stable", "Recovered", "Unchanged", "Other"];

watch(dischargeModal, (open) => {
  if (!open) {
    Object.assign(dischargeForm, defaultDischargeForm());
    isDischargeUpdate.value = false;
    dischargeDatetimeModel.value = new Date();
  }
});

const loadDischarge = async () => {
  if (!patientCasePid.value || !can("patient-case-discharges", "view")) return;
  dischargeLoading.value = true;
  try {
    await patientCaseDischargeStore.read(patientCasePid.value);
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to retrieve discharge information");
  } finally {
    dischargeLoading.value = false;
  }
};

const openCreateDischarge = () => {
  Object.assign(dischargeForm, defaultDischargeForm());
  isDischargeUpdate.value = false;
  dischargeDatetimeModel.value = new Date();
  dischargeModal.value = true;
};

const createDischarge = async () => {
  try {
    dischargeForm.patient_case_pid = patientCasePid.value;
    dischargeForm.discharge_datetime = formatForApi(dischargeDatetimeModel.value);
    await patientCaseDischargeStore.create(dischargeForm);
    toast.success("Patient discharged successfully");
    dischargeModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to discharge patient");
  }
};

const editDischarge = () => {
  if (!discharge.value) return;
  Object.assign(dischargeForm, {
    pid: discharge.value.pid,
    patient_case_pid: patientCasePid.value,
    disposition: discharge.value.disposition || "",
    discharge_condition: discharge.value.discharge_condition || "",
  });
  dischargeDatetimeModel.value = discharge.value.discharge_datetime ? new Date(discharge.value.discharge_datetime) : new Date();
  isDischargeUpdate.value = true;
  dischargeModal.value = true;
};

const updateDischarge = async () => {
  try {
    dischargeForm.patient_case_pid = patientCasePid.value;
    dischargeForm.discharge_datetime = formatForApi(dischargeDatetimeModel.value);
    await patientCaseDischargeStore.update(dischargeForm);
    toast.success("Discharge updated successfully");
    dischargeModal.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || "Failed to update discharge");
  }
};

const archiveDischarge = () => {
  if (!discharge.value) return;
  showConfirm({
    message: "Are you sure you want to delete this discharge record?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await patientCaseDischargeStore.archive(discharge.value.pid, patientCasePid.value);
        toast.success("Discharge record deleted");
      } catch (err) {
        toast.error(err.response?.data?.message || "Failed to delete discharge record");
      }
    },
  });
};

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
