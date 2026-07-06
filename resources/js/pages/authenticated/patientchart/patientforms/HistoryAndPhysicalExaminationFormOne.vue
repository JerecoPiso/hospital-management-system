<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="historyModal" modal :style="{ width: '64vw' }"
            :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FaBookMedical class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Entry' : 'New Physical Examination Form 1' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">History taking and review of systems</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Chief Complaint <span class="text-red-400">*</span>
                        </label>
                        <Textarea v-model="historyInfo.chief_complaint" rows="2" autoResize fluid required class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            History of Present Illness <span class="text-red-400">*</span>
                        </label>
                        <Textarea v-model="historyInfo.history_of_present_illness" rows="3" autoResize fluid required class="text-sm" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div v-for="field in historyFields" :key="field.key" class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">{{ field.label }}</label>
                        <InputText v-model="(historyInfo as any)[field.key]" fluid class="text-sm" placeholder="Findings..." />
                        <InputText v-model="(historyInfo as any)[field.othersKey]" fluid class="text-sm" placeholder="Others, please specify..." />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Review of Systems</label>
                        <Textarea v-model="historyInfo.review_of_systems" rows="3" autoResize fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Remarks</label>
                        <Textarea v-model="historyInfo.remarks" rows="2" autoResize fluid class="text-sm" />
                    </div>
                </div>

                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="historyModal = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Entry' : 'Save Entry'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FaBookMedical class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Physical Examination Form 1</h3>
                    <p class="text-xs text-slate-400">History and physical examination — history taking</p>
                </div>
            </div>
            <button
                type="button"
                @click="historyModal = true"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
            >
                <BsPlusCircle size="16" />
                Add Entry
            </button>
        </div>

        <!-- Table -->
        <DataTable
            :value="histories"
            paginator
            :rows="15"
            :rowsPerPageOptions="[10, 15, 25, 50, 100]"
            responsiveLayout="scroll"
            tableStyle="min-width: 50rem"
            :pt="{
                table: { class: 'text-sm' },
                thead: { class: 'bg-slate-50' },
                bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' },
            }"
        >
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FaBookMedical size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No entries recorded</p>
                    <p class="text-xs mt-1">Click "Add Entry" to create the first entry</p>
                </div>
            </template>

            <Column header="Physician" class="w-52">
                <template #body="{ data }">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                            {{ data.user?.firstname?.charAt(0) ?? '?' }}
                        </div>
                        <span class="text-slate-700 text-sm font-medium leading-tight">
                            {{ `${data.user?.firstname ?? ''} ${data.user?.middlename ? data.user.middlename + ' ' : ''}${data.user?.lastname ?? ''}`.trim() || '—' }}
                        </span>
                    </div>
                </template>
            </Column>

            <Column field="chief_complaint" header="Chief Complaint">
                <template #body="{ data }">
                    <p class="text-slate-700 text-sm leading-relaxed line-clamp-3">{{ data.chief_complaint || '—' }}</p>
                </template>
            </Column>

            <Column field="history_of_present_illness" header="History of Present Illness">
                <template #body="{ data }">
                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ data.history_of_present_illness || '—' }}</p>
                </template>
            </Column>

            <Column field="remarks" header="Remarks" class="w-48">
                <template #body="{ data }">
                    <p v-if="data.remarks" class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ data.remarks }}</p>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            title="Edit entry"
                            @click="view(data.pid)"
                            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                            <BiEdit size="18" />
                        </button>
                        <button
                            type="button"
                            title="Delete entry"
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
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { FaBookMedical } from 'vue-icons-plus/fa';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useHistoryAndPhysicalExaminationFormOneStore } from '@/store/patientchart/HistoryAndPhysicalExaminationFormOne';
import { HistoryAndPhysicalExaminationFormOne } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const historyStore = useHistoryAndPhysicalExaminationFormOneStore();

const historyFields = [
    { key: 'past_medical_history', othersKey: 'past_medical_history_others', label: 'Past Medical History' },
    { key: 'past_surgical_history', othersKey: 'past_surgical_history_history', label: 'Past Surgical History' },
    { key: 'hospitalization_history', othersKey: 'hospitalization_history_others', label: 'Hospitalization History' },
    { key: 'medication_history', othersKey: 'medication_history_others', label: 'Medication History' },
    { key: 'allergies', othersKey: 'allergies_others', label: 'Allergies' },
    { key: 'family_history', othersKey: 'family_history_others', label: 'Family History' },
    { key: 'social_history', othersKey: 'social_history_others', label: 'Social History' },
    { key: 'immunization_history', othersKey: 'immunization_history_others', label: 'Immunization History' },
];

const emptyHistory = (): HistoryAndPhysicalExaminationFormOne => ({
    pid: "",
    chief_complaint: "",
    history_of_present_illness: "",
    past_medical_history: "",
    past_medical_history_others: "",
    past_surgical_history: "",
    past_surgical_history_history: "",
    hospitalization_history: "",
    hospitalization_history_others: "",
    medication_history: "",
    medication_history_others: "",
    allergies: "",
    allergies_others: "",
    family_history: "",
    family_history_others: "",
    social_history: "",
    social_history_others: "",
    immunization_history: "",
    immunization_history_others: "",
    review_of_systems: "",
    remarks: ""
});

const histories = computed<HistoryAndPhysicalExaminationFormOne[]>(() => historyStore.histories);
const history = computed<HistoryAndPhysicalExaminationFormOne>(() => historyStore.history);
const historyInfo = reactive<HistoryAndPhysicalExaminationFormOne>(emptyHistory());
const historyModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);

onMounted(async () => {
    await read();
});

const resetForm = () => {
    Object.assign(historyInfo, emptyHistory());
    isUpdate.value = false;
};

watch(
    () => historyModal.value,
    (newVal) => {
        if (!newVal) {
            resetForm();
        }
    }
);

const read = async () => {
    try {
        await historyStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Physical Examination Form 1 entries");
    }
};

const create = async () => {
    try {
        await historyStore.create(historyInfo);
        toast.success("Entry created successfully");
        historyModal.value = false;
        resetForm();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create entry");
    }
};

const view = async (pid: string) => {
    try {
        await historyStore.view(pid);
        Object.assign(historyInfo, emptyHistory(), history.value);
        isUpdate.value = true;
        historyModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve entry");
    }
};

const update = async () => {
    try {
        await historyStore.update(historyInfo);
        toast.success("Entry updated successfully");
        historyModal.value = false;
        resetForm();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update entry");
    }
};

const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this entry?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await historyStore.archive(pid);
                toast.success("Entry deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete entry");
            }
        },
        onReject: () => {},
    });
};
</script>
