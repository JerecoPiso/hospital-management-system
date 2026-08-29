<template>
    <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <BsJournalMedical size="40" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="soapModal" modal :style="{ width: '52vw' }"
            :breakpoints="{ '1199px': '80vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BsJournalMedical class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit SOAP Note' : 'New SOAP Note' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the existing SOAP note' : 'Record a subjective, objective, assessment & plan note' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">ICD Diagnosis <span class="text-red-400">*</span></label>
                    <Select v-model="soapInfo.icd_pid" :options="icds" optionLabel="name" optionValue="pid" filter required fluid placeholder="Select ICD code" class="text-sm">
                        <template #option="{ option }">
                            <span class="text-sm"><span class="font-medium text-slate-700">{{ option.code }}</span> — {{ option.name }}</span>
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
                        <label class="text-sm font-medium text-slate-700">Remarks</label>
                        <Textarea v-model="soapInfo.remarks" rows="2" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="soapModal = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Note' : 'Save Note'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <BsJournalMedical class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">SOAP Notes</h3>
                    <p class="text-xs text-slate-400">Subjective, Objective, Assessment & Plan documentation</p>
                </div>
            </div>
            <button type="button" @click="openCreate" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                <BsPlusCircle size="16" />
                Add SOAP Note
            </button>
        </div>

        <!-- Table -->
        <DataTable :value="soaps" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 60rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <BsJournalMedical size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No SOAP notes recorded</p>
                    <p class="text-xs mt-1">Click "Add SOAP Note" to create the first entry</p>
                </div>
            </template>

            <Column header="Physician" class="w-48">
                <template #body="{ data }">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                            {{ data.doctor?.firstname?.charAt(0) ?? '?' }}
                        </div>
                        <span class="text-slate-700 text-sm font-medium leading-tight">
                            {{ `${data.doctor?.firstname ?? ''} ${data.doctor?.lastname ?? ''}`.trim() || '—' }}
                        </span>
                    </div>
                </template>
            </Column>

            <Column header="ICD" class="w-44">
                <template #body="{ data }">
                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                        {{ data.icd?.code }} — {{ data.icd?.name }}
                    </span>
                </template>
            </Column>

            <Column field="subjective" header="Subjective">
                <template #body="{ data }"><p class="text-slate-700 text-sm leading-relaxed line-clamp-2">{{ data.subjective || '—' }}</p></template>
            </Column>

            <Column field="plan" header="Plan">
                <template #body="{ data }"><p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ data.plan || '—' }}</p></template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="Edit note" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button type="button" title="Delete note" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { BsPlusCircle, BsJournalMedical } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useSoapStore } from '@/store/patientchart/Soap';
import { useIcdStore } from '@/store/Icd';
import { Soap, Icd } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const route = useRoute();
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const soapStore = useSoapStore();
const icdStore = useIcdStore();

const soapModal = ref<boolean>(false);
const soaps = computed<Soap[]>(() => soapStore.soaps);
const soap = computed<Soap>(() => soapStore.soap);
const icds = computed<Icd[]>(() => icdStore.icds);
const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);

const defaultInfo = (): Soap => ({
    pid: '',
    patient_case_pid: '',
    icd_pid: '',
    subjective: '',
    objective: '',
    assessment: '',
    plan: '',
    remarks: '',
});
const soapInfo = reactive<Soap>(defaultInfo());
const isUpdate = ref<boolean>(false);

const selectedIcd = (pid?: string) => icds.value.find((i) => i.pid === pid);

watch(soapModal, (open) => { if (!open) { Object.assign(soapInfo, defaultInfo()); isUpdate.value = false; } });

onMounted(async () => {
    await Promise.all([read(), loadIcds()]);
});

const read = async () => {
    if (!patientCasePid.value) return;
    try {
        await soapStore.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve SOAP notes');
    }
};

const loadIcds = async () => {
    try {
        await icdStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve ICD codes');
    }
};

const openCreate = () => {
    Object.assign(soapInfo, defaultInfo());
    isUpdate.value = false;
    soapModal.value = true;
};

const create = async () => {
    try {
        soapInfo.patient_case_pid = patientCasePid.value || '';
        await soapStore.create(soapInfo);
        toast.success('SOAP note created successfully');
        soapModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create SOAP note');
    }
};

const edit = async (pid: string) => {
    try {
        await soapStore.view(pid);
        Object.assign(soapInfo, defaultInfo(), soap.value, {
            patient_case_pid: patientCasePid.value || '',
            icd_pid: soap.value.icd?.pid || soap.value.icd_pid || '',
        });
        isUpdate.value = true;
        soapModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve SOAP note');
    }
};

const update = async () => {
    try {
        soapInfo.patient_case_pid = patientCasePid.value || '';
        await soapStore.update(soapInfo);
        toast.success('SOAP note updated successfully');
        soapModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update SOAP note');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this SOAP note?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await soapStore.archive(pid, patientCasePid.value);
                toast.success('SOAP note deleted successfully');
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete SOAP note');
            }
        },
        onReject: () => {},
    });
};
</script>
