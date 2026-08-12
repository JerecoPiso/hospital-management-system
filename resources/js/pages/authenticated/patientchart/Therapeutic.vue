<template>
    <div v-if="!patientPid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <GiMedicines size="40" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '56vw' }" :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicines class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Prescription' : 'New Prescription' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the prescription details' : 'Prescribe medicines for this case' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Prescription Date <span class="text-red-400">*</span></label>
                        <DatePicker v-model="prescriptionDateModel" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD HH:mm" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Status</label>
                        <Select v-model="info.status" :options="statusOptions" placeholder="Select status" fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Remarks</label>
                        <Textarea v-model="info.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-slate-700">Medicines <span class="text-red-400">*</span></label>
                        <button type="button" @click="addItem" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-emerald-700 hover:bg-emerald-50 text-xs font-medium transition-colors duration-150">
                            <BsPlusCircle size="14" />
                            Add Medicine
                        </button>
                    </div>

                    <div v-for="(item, index) in info.items" :key="index" class="rounded-lg border border-slate-200 p-4 flex flex-col gap-3 bg-slate-50/50">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Medicine {{ index + 1 }}</span>
                            <button type="button" title="Remove medicine" @click="removeItem(index)" :disabled="info.items.length === 1"
                                class="p-1 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:hover:bg-transparent transition-colors duration-150 cursor-pointer">
                                <BiTrash size="16" />
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-3 flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Medicine <span class="text-red-400">*</span></label>
                                <Select v-model="item.medicine_pid" :options="medicines" :optionLabel="medicineOptionLabel" optionValue="pid" placeholder="Select medicine" filter fluid class="text-sm" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Frequency</label>
                                <InputText v-model="item.frequency" placeholder="e.g. TID, BID" fluid class="text-sm" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Duration</label>
                                <InputNumber v-model="item.duration" :minFractionDigits="0" :maxFractionDigits="2" fluid class="text-sm" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Duration Unit</label>
                                <InputText v-model="item.duration_unit" placeholder="e.g. days" fluid class="text-sm" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Quantity</label>
                                <InputNumber v-model="item.quantity" :minFractionDigits="0" :maxFractionDigits="2" fluid class="text-sm" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Item Status</label>
                                <Select v-model="item.status" :options="itemStatusOptions" placeholder="Select status" fluid class="text-sm" />
                            </div>
                            <div class="col-span-3 flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Instructions</label>
                                <Textarea v-model="item.instructions" rows="2" autoResize fluid placeholder="Optional instructions..." class="text-sm" />
                            </div>
                            <div class="col-span-3 flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-slate-600">Remarks</label>
                                <Textarea v-model="item.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Prescription' : 'Save Prescription'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <GiMedicines class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Prescriptions</h3>
                    <p class="text-xs text-slate-400">
                        <template v-if="patient">{{ patientName }} &bull; Case {{ currentCase?.case_number || '—' }}</template>
                        <template v-else>Loading patient...</template>
                    </p>
                </div>
            </div>
            <button type="button" @click="openCreate" :disabled="!patientCasePid" title="This patient has no case record yet"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
                <BsPlusCircle size="16" />
                Add Prescription
            </button>
        </div>

        <!-- Table -->
        <DataTable :value="prescriptions" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <GiMedicines size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No prescriptions recorded</p>
                    <p class="text-xs mt-1">Click "Add Prescription" to create the first entry</p>
                </div>
            </template>

            <Column header="Date" class="w-40">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ formatDateTime(data.prescription_date) }}</span></template>
            </Column>

            <Column header="Physician" class="w-44">
                <template #body="{ data }">
                    <span class="text-slate-700 text-sm">{{ `${data.doctor?.firstname ?? ''} ${data.doctor?.lastname ?? ''}`.trim() || '—' }}</span>
                </template>
            </Column>

            <Column header="Medicines">
                <template #body="{ data }">
                    <ul class="space-y-0.5">
                        <li v-for="(item, idx) in data.items" :key="idx" class="text-slate-700 text-sm">
                            {{ item.medicine?.name || '—' }}
                            <span class="text-slate-400 text-xs">{{ [item.frequency, item.duration ? `${item.duration} ${item.duration_unit || ''}`.trim() : null].filter(Boolean).join(' • ') }}</span>
                        </li>
                    </ul>
                </template>
            </Column>

            <Column header="Remarks" class="w-52">
                <template #body="{ data }">
                    <p v-if="data.remarks" class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ data.remarks }}</p>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Status" class="w-32">
                <template #body="{ data }"><Tag :value="data.status" :severity="statusSeverity(data.status)" /></template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="Edit prescription" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button type="button" title="Delete prescription" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
                            <BiTrash size="18" />
                        </button>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { GiMedicines } from 'vue-icons-plus/gi';
import { usePrescriptionStore } from '@/store/patientchart/Prescriptions';
import { usePatientStore } from '@/store/patients/PatientRegistration';
import { useMedicineStore } from '@/store/Medicine';
import { Prescription, PrescriptionItem, Medicines } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const route = useRoute();
const prescriptionStore = usePrescriptionStore();
const patientStore = usePatientStore();
const medicineStore = useMedicineStore();

const patientPid = computed(() => route.params.patient_pid as string | undefined);
const patient = computed(() => patientStore.patient);
const medicines = computed<Medicines[]>(() => medicineStore.medicines);
const prescriptions = computed<Prescription[]>(() => prescriptionStore.prescriptions);

const currentCase = computed(() => {
    const cases = patient.value?.patient_cases;
    return cases && cases.length ? cases[cases.length - 1] : null;
});
const patientCasePid = computed(() => currentCase.value?.pid || '');
const patientName = computed(() => `${patient.value?.firstname ?? ''} ${patient.value?.lastname ?? ''}`.trim() || '—');

const medicineOptionLabel = (data: Medicines) => `${data.name}${data.dosage ? ' — ' + data.dosage + (data.dosage_unit || '') : ''}`;
const statusOptions = ['requested', 'done', 'picked-up', 'cancelled'];
const itemStatusOptions = ['continue', 'discontinue', 'hold'];

const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const prescriptionDateModel = ref<Date | null>(null);

const defaultItem = (): PrescriptionItem => ({
    medicine_pid: '',
    frequency: '',
    duration: null,
    duration_unit: '',
    quantity: null,
    instructions: '',
    remarks: '',
    status: 'continue'
});
const defaultInfo = (): Prescription => ({
    patient_case_pid: '',
    prescription_date: '',
    remarks: '',
    status: 'requested',
    items: [defaultItem()]
});
const info = reactive<Prescription>(defaultInfo());

watch(modalOpen, (open) => {
    if (!open) {
        Object.assign(info, defaultInfo());
        prescriptionDateModel.value = null;
        isUpdate.value = false;
    }
});
watch(prescriptionDateModel, (val) => {
    info.prescription_date = val ? val.toISOString() : '';
});

const statusSeverity = (status?: string) => {
    switch (status) {
        case 'requested': return 'warn';
        case 'done': return 'success';
        case 'picked-up': return 'info';
        case 'cancelled': return 'danger';
        default: return undefined;
    }
};

const formatDateTime = (value?: string) => (value ? new Date(value).toLocaleString() : '—');

const loadContext = async () => {
    if (!patientPid.value) return;
    try {
        await patientStore.view(patientPid.value);
        console.log("Patient loaded:", patientStore.patient);
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to load patient');
    }
};

const refresh = async () => {
    if (!patientCasePid.value) return;
    try {
        await prescriptionStore.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve prescriptions');
    }
};

onMounted(async () => {
    await Promise.all([loadContext(), medicineStore.read()]);
    await refresh();
});

const addItem = () => info.items.push(defaultItem());
const removeItem = (index: number) => {
    if (info.items.length > 1) info.items.splice(index, 1);
};

const openCreate = () => {
    if (!patientCasePid.value) return;
    modalOpen.value = true;
};

const create = async () => {
    try {
        info.patient_case_pid = patientCasePid.value;
        await prescriptionStore.create(info);
        toast.success('Prescription created successfully');
        modalOpen.value = false;
        await refresh();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create prescription');
    }
};

const edit = async (pid: string) => {
    try {
        await prescriptionStore.view(pid);
        const view = prescriptionStore.prescription;
        Object.assign(info, view, {
            patient_case_pid: patientCasePid.value,
            items: (view.items || []).map((item) => ({ ...item, medicine_pid: item.medicine?.pid || item.medicine_pid }))
        });
        prescriptionDateModel.value = info.prescription_date ? new Date(info.prescription_date) : null;
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve prescription');
    }
};

const update = async () => {
    try {
        await prescriptionStore.update(info);
        toast.success('Prescription updated successfully');
        modalOpen.value = false;
        await refresh();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update prescription');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this prescription?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await prescriptionStore.archive(pid);
                toast.success('Prescription deleted successfully');
                await refresh();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete prescription');
            }
        },
    });
};
</script>
