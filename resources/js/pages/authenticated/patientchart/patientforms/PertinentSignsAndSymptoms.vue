<template>
    <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <FiActivity size="40" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="formModal" modal :style="{ width: '58vw' }" :breakpoints="{ '1199px': '85vw', '575px': '96vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FiActivity class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Entry' : 'New Pertinent Signs & Symptoms' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tick all that apply</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-2.5 max-h-[46vh] overflow-y-auto pr-1">
                    <label
                        v-for="item in activeList"
                        :key="item.code"
                        class="flex items-start gap-2 text-sm text-slate-700 cursor-pointer select-none"
                    >
                        <input type="checkbox" :value="item.code" v-model="selectedCodes" class="mt-0.5 w-4 h-4 rounded border-slate-300 shrink-0" />
                        <span>{{ item.name }}</span>
                    </label>
                </div>

                <div v-if="painCode && selectedCodes.includes(painCode)" class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Pain — please specify</label>
                    <InputText v-model="formInfo.pain" placeholder="e.g. location, character, severity" maxlength="255" fluid class="text-sm" />
                </div>

                <div v-if="othersCode && selectedCodes.includes(othersCode)" class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Others — please specify</label>
                    <InputText v-model="formInfo.others" placeholder="Specify other signs / symptoms" maxlength="255" fluid class="text-sm" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Remarks</label>
                    <Textarea v-model="formInfo.remarks" rows="2" autoResize fluid class="text-sm" placeholder="Optional notes" />
                </div>

                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="formModal = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Entry' : 'Save Entry'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FiActivity class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Pertinent Signs &amp; Symptoms</h3>
                    <p class="text-xs text-slate-400">Recorded signs and symptoms for this case</p>
                </div>
            </div>
            <button type="button" @click="openCreate" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                <BsPlusCircle size="16" />
                Add Entry
            </button>
        </div>

        <!-- Table -->
        <DataTable :value="records" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FiActivity size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No entries recorded</p>
                    <p class="text-xs mt-1">Click "Add Entry" to create the first entry</p>
                </div>
            </template>

            <Column header="Physician" class="w-48">
                <template #body="{ data }">
                    <span class="text-slate-700 text-sm">
                        {{ `${data.user?.firstname ?? ''} ${data.user?.lastname ?? ''}`.trim() || '—' }}
                    </span>
                </template>
            </Column>

            <Column header="Signs &amp; Symptoms">
                <template #body="{ data }">
                    <div class="flex flex-wrap gap-1">
                        <span v-for="code in splitCodes(data.values)" :key="code" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                            {{ codeToName[code] || code }}
                        </span>
                        <span v-if="!splitCodes(data.values).length" class="text-slate-300 text-sm italic">None</span>
                    </div>
                </template>
            </Column>

            <Column header="Pain" class="w-40">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.pain || '—' }}</span></template>
            </Column>

            <Column header="Others" class="w-40">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.others || '—' }}</span></template>
            </Column>

            <Column header="Remarks" class="w-44">
                <template #body="{ data }">
                    <span v-if="data.remarks" class="text-slate-500 text-sm">{{ data.remarks }}</span>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="Edit entry" @click="view(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button type="button" title="Delete entry" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { FiActivity } from 'vue-icons-plus/fi';
import { usePertinentSignsAndSymptomsStore } from '@/store/patientchart/PertinentSignsAndSymptoms';
import { usePertinentSignsAndSymptomsListStore } from '@/store/PertinentSignsAndSymptomsList';
import { PertinentSignsAndSymptoms, PertinentSignsAndSymptomsList } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const route = useRoute();
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const store = usePertinentSignsAndSymptomsStore();
const listStore = usePertinentSignsAndSymptomsListStore();

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const records = computed<PertinentSignsAndSymptoms[]>(() => store.records);

const masterList = computed<PertinentSignsAndSymptomsList[]>(() => listStore.items);
const activeList = computed(() => masterList.value.filter((i) => i.status !== false));
const codeToName = computed<Record<string, string>>(() =>
    Object.fromEntries(masterList.value.map((i) => [i.code, i.name]))
);
const painCode = computed(() => masterList.value.find((i) => i.name?.trim().toLowerCase() === 'pain')?.code || '');
const othersCode = computed(() => masterList.value.find((i) => i.name?.trim().toLowerCase() === 'others')?.code || '');

const splitCodes = (values?: string) => (values ? values.split(';').map((c) => c.trim()).filter(Boolean) : []);

const formModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const selectedCodes = ref<string[]>([]);
const emptyForm = (): PertinentSignsAndSymptoms => ({ pid: '', patient_case_pid: '', values: '', pain: '', others: '', remarks: '' });
const formInfo = reactive<PertinentSignsAndSymptoms>(emptyForm());

const resetForm = () => {
    Object.assign(formInfo, emptyForm());
    selectedCodes.value = [];
    isUpdate.value = false;
};

watch(formModal, (open) => { if (!open) resetForm(); });

onMounted(async () => {
    await Promise.all([read(), loadMasterList()]);
});

const loadMasterList = async () => {
    try {
        await listStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve signs & symptoms list');
    }
};

const read = async () => {
    if (!patientCasePid.value) return;
    try {
        await store.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve entries');
    }
};

const openCreate = () => {
    resetForm();
    formModal.value = true;
};

const buildPayload = () => {
    formInfo.patient_case_pid = patientCasePid.value || '';
    formInfo.values = selectedCodes.value.join(';');
    if (painCode.value && !selectedCodes.value.includes(painCode.value)) formInfo.pain = '';
    if (othersCode.value && !selectedCodes.value.includes(othersCode.value)) formInfo.others = '';
    return formInfo;
};

const create = async () => {
    try {
        await store.create(buildPayload());
        toast.success('Entry created successfully');
        formModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create entry');
    }
};

const view = async (pid: string) => {
    try {
        await store.view(pid);
        const current = store.record;
        Object.assign(formInfo, emptyForm(), current, { patient_case_pid: patientCasePid.value || '' });
        selectedCodes.value = splitCodes(current.values);
        isUpdate.value = true;
        formModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve entry');
    }
};

const update = async () => {
    try {
        await store.update(buildPayload());
        toast.success('Entry updated successfully');
        formModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update entry');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this entry?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await store.archive(pid, patientCasePid.value);
                toast.success('Entry deleted successfully');
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete entry');
            }
        },
    });
};
</script>
