<template>
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <Dialog v-model:visible="patientModal" modal :style="{ width: '56vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiUserPlus class="text-white" size="17" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? "Edit Registration" : "New Patient Registration" }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Patient information and case details</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Patient Information</p>
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">First Name <span class="text-red-400">*</span></label>
            <InputText v-model="patientInfo.firstname" fluid required class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Last Name <span class="text-red-400">*</span></label>
            <InputText v-model="patientInfo.lastname" fluid required class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Middle Name</label>
            <InputText v-model="patientInfo.middlename" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Suffix</label>
            <InputText v-model="patientInfo.suffix" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Birthdate <span class="text-red-400">*</span></label>
            <DatePicker v-model="birthdateModel" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Gender</label>
            <Select v-model="patientInfo.gender" :options="genders" placeholder="Select gender" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Civil Status</label>
            <InputText v-model="patientInfo.civil_status" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Contact Number</label>
            <InputText v-model="patientInfo.contact_number" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Email Address</label>
            <InputText v-model="patientInfo.email_address" type="email" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Religion</label>
            <InputText v-model="patientInfo.religion" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Birthplace</label>
            <InputText v-model="patientInfo.birthplace" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Occupation</label>
            <InputText v-model="patientInfo.occupation" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Spouse Name</label>
            <InputText v-model="patientInfo.spouse_name" fluid class="text-sm" />
          </div>
        </div>

        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide pt-2">Case Information</p>
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Patient Type <span class="text-red-400">*</span></label>
            <Select v-model="patientInfo.type" :options="patientTypeOptions" optionLabel="label" optionValue="value" placeholder="Select patient type" required fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Admission Date/Time <span class="text-red-400">*</span></label>
            <DatePicker v-model="admissionDatetimeModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD HH:mm" fluid class="text-sm" />
          </div>
          <div class="col-span-2 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Chief Complaint <span class="text-red-400">*</span></label>
            <InputText v-model="patientInfo.chief_complaint" fluid required class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Initial Diagnosis</label>
            <InputText v-model="patientInfo.initial_diagnosis" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Final Diagnosis</label>
            <InputText v-model="patientInfo.final_diagnosis" fluid class="text-sm" />
          </div>
        </div>

        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="patientModal = false" />
          <Button type="submit" :label="isUpdate ? 'Update Registration' : 'Save Registration'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- ADD CASE MODAL -->
    <Dialog v-model:visible="caseModal" modal :style="{ width: '42vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <FiFilePlus class="text-white" size="17" />
          </div>
          <div>
            <h2 class="text-base font-semibold text-slate-800">Add New Case</h2>
            <p class="text-xs text-slate-400 mt-0.5">Record a new admission/visit for this patient</p>
          </div>
        </div>
      </template>
      <form @submit.prevent="createCase" class="flex flex-col gap-5 pt-2">
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-sm font-semibold shrink-0">
            {{ selectedPatient?.firstname?.charAt(0) ?? "?" }}
          </div>
          <div>
            <p class="text-sm font-semibold text-slate-800">
              {{ `${selectedPatient?.firstname ?? ""} ${selectedPatient?.middlename ? selectedPatient.middlename + " " : ""}${selectedPatient?.lastname ?? ""}`.trim() || "—" }}
            </p>
            <p class="text-xs text-slate-500 mt-0.5">
              MRN: {{ selectedPatient?.medical_record_number || "—" }}
              <span class="mx-1">·</span>
              Birthdate: {{ selectedPatient?.birthdate ? new Date(selectedPatient.birthdate).toLocaleDateString() : "—" }}
              <span class="mx-1">·</span>
              {{ selectedPatient?.gender || "—" }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Patient Type <span class="text-red-400">*</span></label>
            <Select v-model="caseInfo.type" :options="patientTypeOptions" optionLabel="label" optionValue="value" placeholder="Select patient type" required fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Admission Date/Time <span class="text-red-400">*</span></label>
            <DatePicker v-model="caseAdmissionDatetimeModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD HH:mm" fluid class="text-sm" />
          </div>
          <div class="col-span-2 flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Chief Complaint <span class="text-red-400">*</span></label>
            <InputText v-model="caseInfo.chief_complaint" fluid required class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Initial Diagnosis</label>
            <InputText v-model="caseInfo.initial_diagnosis" fluid class="text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">Final Diagnosis</label>
            <InputText v-model="caseInfo.final_diagnosis" fluid class="text-sm" />
          </div>
        </div>

        <div class="flex gap-2 pt-1">
          <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="caseModal = false" />
          <Button type="submit" label="Save Case" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
      </form>
    </Dialog>

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
          <FiUserPlus class="text-white" size="18" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-800">Patient Registration</h3>
          <p class="text-xs text-slate-400">Register a patient together with their case</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="relative w-full sm:w-64">
          <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
          <InputText v-model="search" placeholder="Search . . ." class="w-full text-sm pl-8!" />
        </div>
        <button
          type="button"
          @click="patientModal = true"
          class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0"
        >
          <BsPlusCircle size="16" />
          Register Patient
        </button>
      </div>
    </div>

    <!-- Table -->
    <DataTable
      :value="patients"
      paginator
      :rows="15"
      :rowsPerPageOptions="[10, 15, 25, 50, 100]"
      responsiveLayout="scroll"
      tableStyle="min-width: 55rem"
      :pt="{
        table: { class: 'text-sm' },
        thead: { class: 'bg-slate-50' },
        bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' },
      }"
    >
      <template #empty>
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
          <FiUserPlus size="40" class="mb-3 opacity-30" />
          <p class="text-sm font-medium">No patients registered</p>
          <p class="text-xs mt-1">Click "Register Patient" to add the first entry</p>
        </div>
      </template>

      <Column header="Patient" class="w-56">
        <template #body="{ data }">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
              {{ data.firstname?.charAt(0) ?? "?" }}
            </div>
            <div>
              <p class="text-slate-800 text-sm font-medium leading-tight">
                {{ `${data.firstname ?? ""} ${data.middlename ? data.middlename + " " : ""}${data.lastname ?? ""}`.trim() || "—" }}
              </p>
              <p class="text-slate-400 text-xs mt-0.5">{{ data.medical_record_number || "—" }}</p>
            </div>
          </div>
        </template>
      </Column>

      <Column header="Case Number" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-600 text-sm">{{ data.patient_cases?.[data.patient_cases.length - 1]?.case_number || "—" }}</span>
        </template>
      </Column>

      <Column header="Admission Date" class="w-40">
        <template #body="{ data }">
          <span class="text-slate-500 text-sm">{{ data.patient_cases?.[data.patient_cases.length - 1]?.admission_datetime || "—" }}</span>
        </template>
      </Column>

      <Column header="Chief Complaint">
        <template #body="{ data }">
          <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ data.patient_cases?.[data.patient_cases.length - 1]?.chief_complaint || "—" }}</p>
        </template>
      </Column>
      <Column header="Initial Diagnosis">
        <template #body="{ data }">
          <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ data.patient_cases?.[data.patient_cases.length - 1]?.initial_diagnosis || "—" }}</p>
        </template>
      </Column>
      <Column header="Final Diagnosis">
        <template #body="{ data }">
          <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ data.patient_cases?.[data.patient_cases.length - 1]?.final_diagnosis || "—" }}</p>
        </template>
      </Column>

      <Column header="Type" class="w-28">
        <template #body="{ data }">
          <Tag
            v-if="data.patient_cases?.[data.patient_cases.length - 1]?.type"
            :value="data.patient_cases[data.patient_cases.length - 1].type === 'inpatient' ? 'Inpatient' : 'Outpatient'"
            :severity="data.patient_cases[data.patient_cases.length - 1].type === 'inpatient' ? 'info' : 'success'"
          />
          <span v-else class="text-slate-300 text-sm italic">—</span>
        </template>
      </Column>

      <Column header="Actions" class="w-40">
        <template #body="{ data }">
          <div class="flex items-center gap-1">
            <button
              type="button"
              title="View patient chart"
              @click="viewChart()"
              class="p-1.5 rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 cursor-pointer"
            >
              <FiEye size="18" />
            </button>
            <button
              type="button"
              title="Add new case"
              @click="openCaseModal(data)"
              class="p-1.5 rounded-md text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 cursor-pointer"
            >
              <FiFilePlus size="18" />
            </button>
            <button
              type="button"
              title="Edit registration"
              @click="view(data.pid)"
              class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
            >
              <BiEdit size="18" />
            </button>
            <button
              type="button"
              title="Delete registration"
              @click="archive(data.pid)"
              class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
            >
              <BiTrash size="18" />
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { BsPlusCircle } from "vue-icons-plus/bs";
import { FiUserPlus, FiEye, FiSearch, FiFilePlus } from "vue-icons-plus/fi";
import { BiEdit, BiTrash } from "vue-icons-plus/bi";
import { usePatientStore } from "@/store/patients/PatientRegistration";
import { usePatientCaseStore } from "@/store/patients/PatientCase";
import { PatientRegistration, PatientCase } from "@/interface/Interfaces";
import { useConfirmToast } from "@/composables/confirm";
import { useAppToast } from "@/composables/toast";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const patientStore = usePatientStore();
const patientCaseStore = usePatientCaseStore();
const router = useRouter();

const genders = ["Male", "Female", "Other"];
const patientTypeOptions = [
  { label: "Inpatient", value: "inpatient" },
  { label: "Outpatient", value: "outpatient" },
];

const emptyPatient = (): PatientRegistration => ({
  pid: "",
  firstname: "",
  lastname: "",
  middlename: "",
  suffix: "",
  birthdate: "",
  gender: "",
  civil_status: "",
  contact_number: "",
  email_address: "",
  religion: "",
  birthplace: "",
  occupation: "",
  spouse_name: "",
  admission_datetime: "",
  chief_complaint: "",
  initial_diagnosis: "",
  final_diagnosis: "",
  type: "outpatient",
});

const patients = computed<PatientRegistration[]>(() => patientStore.patients);
const patient = computed<PatientRegistration>(() => patientStore.patient);
const patientInfo = reactive<PatientRegistration>(emptyPatient());
const patientModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const birthdateModel = ref<Date | null>(null);
const admissionDatetimeModel = ref<Date | null>(null);
const search = ref<string>("");

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => read(value), 400);
});

watch(birthdateModel, (val) => {
  patientInfo.birthdate = val ? val.toISOString() : "";
});
watch(admissionDatetimeModel, (val) => {
  patientInfo.admission_datetime = val ? val.toISOString() : "";
});

onMounted(async () => {
  await read();
});

const resetForm = () => {
  Object.assign(patientInfo, emptyPatient());
  birthdateModel.value = null;
  admissionDatetimeModel.value = null;
  isUpdate.value = false;
};

watch(
  () => patientModal.value,
  (newVal) => {
    if (!newVal) {
      resetForm();
    }
  }
);

const read = async (query?: string) => {
  try {
    await patientStore.read(undefined, query);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve registered patients");
  }
};

const create = async () => {
  try {
    await patientStore.create(patientInfo);
    toast.success("Patient registered successfully");
    patientModal.value = false;
    resetForm();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to register patient");
  }
};

const viewChart = () => {
  router.push({ name: "PatientInformation" });
};

const view = async (pid: string) => {
  try {
    await patientStore.view(pid);
    Object.assign(patientInfo, emptyPatient(), patient.value);
    const latestCase = patient.value.patientCases?.[0];
    if (latestCase) {
      patientInfo.admission_datetime = latestCase.admission_datetime;
      patientInfo.chief_complaint = latestCase.chief_complaint;
      patientInfo.initial_diagnosis = latestCase.initial_diagnosis;
      patientInfo.final_diagnosis = latestCase.final_diagnosis;
      patientInfo.type = latestCase.type ?? "outpatient";
      admissionDatetimeModel.value = latestCase.admission_datetime ? new Date(latestCase.admission_datetime) : null;
    }
    birthdateModel.value = patient.value.birthdate ? new Date(patient.value.birthdate) : null;
    isUpdate.value = true;
    patientModal.value = true;
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to retrieve patient");
  }
};

const update = async () => {
  try {
    await patientStore.update(patientInfo);
    toast.success("Registration updated successfully");
    patientModal.value = false;
    resetForm();
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to update registration");
  }
};

const archive = async (pid: string) => {
  showConfirm({
    message: "Are you sure you want to delete this patient registration?",
    header: "Delete Confirmation",
    onAccept: async () => {
      try {
        await patientStore.archive(pid);
        toast.success("Patient registration deleted successfully");
      } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to delete registration");
      }
    },
    onReject: () => {},
  });
};

/* ---------------- ADD CASE ---------------- */
const caseModal = ref<boolean>(false);
const selectedPatient = ref<PatientRegistration | null>(null);
const defaultCaseInfo = (): PatientCase => ({ patient_pid: "", type: "outpatient", admission_datetime: "", chief_complaint: "", initial_diagnosis: "", final_diagnosis: "" });
const caseInfo = reactive<PatientCase>(defaultCaseInfo());
const caseAdmissionDatetimeModel = ref<Date | null>(null);

watch(caseAdmissionDatetimeModel, (val) => {
  caseInfo.admission_datetime = val ? val.toISOString() : "";
});

watch(caseModal, (open) => {
  if (!open) {
    Object.assign(caseInfo, defaultCaseInfo());
    caseAdmissionDatetimeModel.value = null;
    selectedPatient.value = null;
  }
});

const openCaseModal = (data: PatientRegistration) => {
  selectedPatient.value = data;
  caseInfo.patient_pid = data.pid;
  caseModal.value = true;
};

const createCase = async () => {
  try {
    await patientCaseStore.create(caseInfo);
    toast.success("Case added successfully");
    caseModal.value = false;
    await read(search.value);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Failed to add case");
  }
};
</script>
