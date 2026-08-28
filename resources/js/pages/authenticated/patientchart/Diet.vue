<template>
    <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <FaUtensils size="36" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Assign / Edit dialog -->
        <Dialog v-model:visible="dietModal" modal :style="{ width: '40vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FaUtensils class="text-white" size="15" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Diet Assignment' : 'Assign Diet' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the assigned diet for this case' : 'Assign a diet to this patient case' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Diet <span class="text-red-400">*</span></label>
                    <Select v-model="info.diet_pid" :options="diets" optionLabel="name" optionValue="pid" placeholder="Select diet" filter required fluid class="text-sm" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Remarks</label>
                    <Textarea v-model="info.remarks" rows="3" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="dietModal = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Assignment' : 'Assign Diet'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Serve dialog -->
        <Dialog v-model:visible="serveModal" modal :style="{ width: '36vw' }" :breakpoints="{ '1199px': '70vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BiDish class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Record Serving</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ serveTarget?.diet?.name || 'Diet' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="serve()" class="flex flex-col gap-5 pt-2">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Served At</label>
                    <DatePicker v-model="serveInfo.served_at" showTime hourFormat="24" dateFormat="yy-mm-dd" placeholder="Defaults to now" fluid class="text-sm" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Remarks</label>
                    <Textarea v-model="serveInfo.remarks" rows="3" autoResize fluid placeholder="Optional remarks..." class="text-sm" />
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="serveModal = false" />
                    <Button type="submit" label="Save Serving" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- Servings history dialog -->
        <Dialog v-model:visible="historyModal" modal :style="{ width: '44vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BiDish class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Diets Served</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ historyTarget?.diet?.name || 'Diet' }} &bull; {{ historyTarget?.diets_served?.length || 0 }} serving(s)</p>
                    </div>
                </div>
            </template>
            <div class="pt-2">
                <div v-if="!historyTarget?.diets_served?.length" class="py-10 text-center text-slate-400 text-sm">
                    No servings recorded yet.
                </div>
                <ul v-else class="flex flex-col divide-y divide-slate-100">
                    <li v-for="served in historyTarget.diets_served" :key="served.pid" class="py-3 flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center shrink-0 mt-0.5">
                            <BiDish class="text-emerald-600" size="14" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-700 font-medium">{{ formatDate(served.served_at || served.created_at) }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Served by {{ userName(served.user) || '—' }}</p>
                            <p v-if="served.remarks" class="text-sm text-slate-500 mt-1">{{ served.remarks }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FaUtensils class="text-white" size="17" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Diet</h3>
                    <p class="text-xs text-slate-400">Assigned diets and servings for this case</p>
                </div>
            </div>
            <button type="button" @click="openCreate" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                <BsPlusCircle size="16" />
                Assign Diet
            </button>
        </div>

        <!-- Table -->
        <DataTable :value="patientCaseDiets" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FaUtensils size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No diet assigned</p>
                    <p class="text-xs mt-1">Click "Assign Diet" to set one</p>
                </div>
            </template>

            <Column header="Diet" class="w-48">
                <template #body="{ data }">
                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">{{ data.diet?.name || '—' }}</span>
                </template>
            </Column>

            <Column field="remarks" header="Remarks">
                <template #body="{ data }">
                    <p v-if="data.remarks" class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ data.remarks }}</p>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Set By" class="w-40">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ userName(data.user) || '—' }}</span></template>
            </Column>

            <Column header="Assigned" class="w-40">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ formatDate(data.created_at) }}</span></template>
            </Column>

            <Column header="Servings" class="w-36">
                <template #body="{ data }">
                    <button type="button" @click="openHistory(data)" class="inline-flex items-center gap-1.5 text-sm font-medium text-teal-600 hover:text-teal-700 cursor-pointer">
                        <BiDish size="15" />
                        {{ data.diets_served_count ?? data.diets_served?.length ?? 0 }}
                    </button>
                </template>
            </Column>

            <Column header="Actions" class="w-32">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="Record serving" @click="openServe(data)" class="p-1.5 rounded-md text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 cursor-pointer">
                            <BiDish size="18" />
                        </button>
                        <button type="button" title="Edit" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button type="button" title="Delete" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { BiEdit, BiTrash, BiDish } from 'vue-icons-plus/bi';
import { FaUtensils } from 'vue-icons-plus/fa';
import { usePatientCaseDietStore } from '@/store/patientchart/PatientCaseDiet';
import { useDietStore } from '@/store/Diet';
import { Diet, PatientCaseDiet, User } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const route = useRoute();
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const patientCaseDietStore = usePatientCaseDietStore();
const dietStore = useDietStore();

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const patientCaseDiets = computed<PatientCaseDiet[]>(() => patientCaseDietStore.patientCaseDiets);
const diets = computed<Diet[]>(() => dietStore.diets);

const dietModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): PatientCaseDiet => ({ pid: '', patient_case_pid: '', diet_pid: '', remarks: '' });
const info = reactive<PatientCaseDiet>(defaultInfo());

const serveModal = ref<boolean>(false);
const serveTarget = ref<PatientCaseDiet | null>(null);
const serveInfo = reactive<{ served_at: Date | null; remarks: string }>({ served_at: null, remarks: '' });

const historyModal = ref<boolean>(false);
const historyTarget = ref<PatientCaseDiet | null>(null);

watch(dietModal, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });
watch(serveModal, (open) => { if (!open) { serveInfo.served_at = null; serveInfo.remarks = ''; serveTarget.value = null; } });

const userName = (user?: User) => {
    if (!user) return '';
    return `${user.firstname ?? ''} ${user.lastname ?? ''}`.trim();
};

const formatDate = (value?: string | null) => (value ? new Date(value).toLocaleString() : '—');

onMounted(async () => {
    await Promise.all([read(), loadDiets()]);
});

const read = async () => {
    if (!patientCasePid.value) return;
    try {
        await patientCaseDietStore.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve diets');
    }
};

const loadDiets = async () => {
    try {
        await dietStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve diet catalog');
    }
};

const openCreate = () => {
    Object.assign(info, defaultInfo());
    isUpdate.value = false;
    dietModal.value = true;
};

const create = async () => {
    try {
        info.patient_case_pid = patientCasePid.value || '';
        await patientCaseDietStore.create(info);
        toast.success('Diet assigned successfully');
        dietModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to assign diet');
    }
};

const edit = async (pid: string) => {
    try {
        await patientCaseDietStore.view(pid);
        const current = patientCaseDietStore.patientCaseDiet;
        Object.assign(info, {
            pid: current.pid,
            patient_case_pid: patientCasePid.value || '',
            diet_pid: current.diet?.pid || current.diet_pid || '',
            remarks: current.remarks || '',
        });
        isUpdate.value = true;
        dietModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve diet assignment');
    }
};

const update = async () => {
    try {
        info.patient_case_pid = patientCasePid.value || '';
        await patientCaseDietStore.update(info);
        toast.success('Diet assignment updated successfully');
        dietModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update diet assignment');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to remove this diet assignment? Its serving records will also be removed.',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await patientCaseDietStore.archive(pid, patientCasePid.value);
                toast.success('Diet assignment removed');
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to remove diet assignment');
            }
        },
    });
};

const openServe = (row: PatientCaseDiet) => {
    serveTarget.value = row;
    serveInfo.served_at = null;
    serveInfo.remarks = '';
    serveModal.value = true;
};

const serve = async () => {
    if (!serveTarget.value?.pid) return;
    try {
        await patientCaseDietStore.serve(
            serveTarget.value.pid,
            {
                served_at: serveInfo.served_at ? formatForApi(serveInfo.served_at) : null,
                remarks: serveInfo.remarks || null,
            },
            patientCasePid.value
        );
        toast.success('Serving recorded');
        serveModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to record serving');
    }
};

const openHistory = (row: PatientCaseDiet) => {
    historyTarget.value = row;
    historyModal.value = true;
};

const formatForApi = (date: Date) => {
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
};
</script>
