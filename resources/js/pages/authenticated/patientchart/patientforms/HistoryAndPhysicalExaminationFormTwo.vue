<template>
    <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <FaBookMedical size="40" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="historyModal" modal :style="{ width: '64vw' }"
            :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FaBookMedical class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Entry' : 'New Physical Examination Form 2' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Physical examination findings and assessment</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="field in historyFields" :key="field.key" class="flex flex-col gap-2 rounded-lg border border-slate-200 p-3">
                        <label class="text-sm font-semibold text-slate-700">{{ field.label }}</label>
                        <div v-if="optionsByType[field.key]?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1.5">
                            <div v-for="option in optionsByType[field.key]" :key="option.pid" class="flex items-start gap-2">
                                <Checkbox v-model="historyInfo.findings" :inputId="`finding-${option.pid}`" :value="option.pid"
                                    :disabled="isOptionDisabled(field.key, option)" @change="onFindingChange(field.key, option)" />
                                <label :for="`finding-${option.pid}`" class="text-sm leading-tight"
                                    :class="isOptionDisabled(field.key, option) ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 cursor-pointer'">{{ option.description }}</label>
                            </div>
                        </div>
                        <p v-else class="text-xs text-slate-400 italic">No options configured</p>
                        <InputText v-model="(historyInfo as any)[field.othersKey]" fluid class="text-sm" placeholder="Others, please specify..." />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Assessment / Impression</label>
                        <Textarea v-model="historyInfo.assessment_impression" rows="3" autoResize fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Plan / Recommendations</label>
                        <Textarea v-model="historyInfo.plan_recommendations" rows="3" autoResize fluid class="text-sm" />
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
                    <h3 class="text-base font-bold text-slate-800">Physical Examination Form 2</h3>
                    <p class="text-xs text-slate-400">History and physical examination — physical findings</p>
                </div>
            </div>
            <button
                v-if="can('history-and-physical-examination-form-two', 'create')"
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

            <Column field="assessment_impression" header="Assessment / Impression">
                <template #body="{ data }">
                    <p class="text-slate-700 text-sm leading-relaxed line-clamp-3">{{ data.assessment_impression || '—' }}</p>
                </template>
            </Column>

            <Column field="plan_recommendations" header="Plan / Recommendations">
                <template #body="{ data }">
                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ data.plan_recommendations || '—' }}</p>
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
                            v-if="can('history-and-physical-examination-form-two', 'update')"
                            type="button"
                            title="Edit entry"
                            @click="view(data.pid)"
                            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                            <BiEdit size="18" />
                        </button>
                        <button
                            v-if="can('history-and-physical-examination-form-two', 'delete')"
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
import { useRoute } from 'vue-router';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { FaBookMedical } from 'vue-icons-plus/fa';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useHistoryAndPhysicalExaminationFormTwoStore } from '@/store/patientchart/HistoryAndPhysicalExaminationFormTwo';
import { ExaminationOption, HistoryAndPhysicalExaminationFormTwo } from '@/interface/Interfaces';
import Checkbox from 'primevue/checkbox';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';
import { usePermission } from '@/composables/permission';

const route = useRoute();
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const historyStore = useHistoryAndPhysicalExaminationFormTwoStore();
const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);

const historyFields = [
    { key: 'general_appearance', othersKey: 'general_appearance_others', label: 'General Appearance' },
    { key: 'skin', othersKey: 'skin_others', label: 'Skin' },
    { key: 'heent', othersKey: 'heent_others', label: 'HEENT' },
    { key: 'neck', othersKey: 'neck_others', label: 'Neck' },
    { key: 'chest_lungs', othersKey: 'chest_lungs_others', label: 'Chest / Lungs' },
    { key: 'cardiovascular', othersKey: 'cardiovascular_others', label: 'Cardiovascular' },
    { key: 'abdomen', othersKey: 'abdomen_others', label: 'Abdomen' },
    { key: 'genitourinary', othersKey: 'genitourinary_others', label: 'Genitourinary' },
    { key: 'rectal', othersKey: 'rectal_others', label: 'Rectal' },
    { key: 'musculoskeletal', othersKey: 'musculoskeletal_others', label: 'Musculoskeletal' },
    { key: 'neurological', othersKey: 'neurological_others', label: 'Neurological' },
    { key: 'psychiatric_mental_status', othersKey: 'psychiatric_mental_status_others', label: 'Psychiatric / Mental Status' },
];

const emptyHistory = (): HistoryAndPhysicalExaminationFormTwo => ({
    pid: "",
    patient_case_pid: "",
    findings: [],
    general_appearance_others: "",
    skin_others: "",
    heent_others: "",
    neck_others: "",
    chest_lungs_others: "",
    cardiovascular_others: "",
    abdomen_others: "",
    genitourinary_others: "",
    rectal_others: "",
    musculoskeletal_others: "",
    neurological_others: "",
    psychiatric_mental_status_others: "",
    assessment_impression: "",
    plan_recommendations: "",
    remarks: ""
});

const histories = computed<HistoryAndPhysicalExaminationFormTwo[]>(() => historyStore.histories);
const history = computed<HistoryAndPhysicalExaminationFormTwo>(() => historyStore.history);
const historyInfo = reactive<HistoryAndPhysicalExaminationFormTwo>(emptyHistory());
const historyModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
// Findings already saved on the entry being edited, kept so options deactivated since still show up
const savedFindings = ref<ExaminationOption[]>([]);

const optionsByType = computed<Record<string, ExaminationOption[]>>(() => {
    const options = [...historyStore.examinationOptions];
    savedFindings.value.forEach((saved) => {
        if (!options.some((option) => option.pid === saved.pid)) options.push(saved);
    });
    return options.reduce((groups, option) => {
        (groups[option.type] ??= []).push(option);
        return groups;
    }, {} as Record<string, ExaminationOption[]>);
});

// "Essentially normal" excludes every other finding within the same section
const isEssentiallyNormal = (option: ExaminationOption) => /essentially normal/i.test(option.description ?? "");

const isNormalSelected = (type: string) =>
    (optionsByType.value[type] ?? []).some((option) => isEssentiallyNormal(option) && (historyInfo.findings ?? []).includes(option.pid as string));

const isOptionDisabled = (type: string, option: ExaminationOption) => !isEssentiallyNormal(option) && isNormalSelected(type);

const onFindingChange = (type: string, option: ExaminationOption) => {
    const findings = historyInfo.findings ?? [];
    if (!isEssentiallyNormal(option) || !findings.includes(option.pid as string)) return;
    const otherPids = (optionsByType.value[type] ?? [])
        .filter((other) => !isEssentiallyNormal(other))
        .map((other) => other.pid as string);
    historyInfo.findings = findings.filter((pid) => !otherPids.includes(pid));
};

onMounted(async () => {
    await Promise.all([read(), readOptions()]);
});

const readOptions = async () => {
    try {
        await historyStore.readOptions();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve examination options");
    }
};

const resetForm = () => {
    Object.assign(historyInfo, emptyHistory());
    savedFindings.value = [];
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
    if (!patientCasePid.value) return;
    try {
        await historyStore.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Physical Examination Form 2 entries");
    }
};

const create = async () => {
    try {
        historyInfo.patient_case_pid = patientCasePid.value || "";
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
        // The API returns findings as option objects; the form works with their pids
        savedFindings.value = (history.value.findings ?? []) as unknown as ExaminationOption[];
        Object.assign(historyInfo, emptyHistory(), history.value, {
            patient_case_pid: patientCasePid.value || "",
            findings: savedFindings.value.map((option) => option.pid as string),
        });
        isUpdate.value = true;
        historyModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve entry");
    }
};

const update = async () => {
    try {
        historyInfo.patient_case_pid = patientCasePid.value || "";
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
                await historyStore.archive(pid, patientCasePid.value);
                toast.success("Entry deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete entry");
            }
        },
        onReject: () => {},
    });
};
</script>
