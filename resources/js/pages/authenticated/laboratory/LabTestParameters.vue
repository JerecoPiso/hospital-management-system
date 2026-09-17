<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '40vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMicroscope class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Parameter' : 'New Parameter' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the test parameter details' : 'Fill in the test parameter details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Lab Test <span class="text-red-400">*</span></label>
                        <Select v-model="info.lab_test_pid" :options="tests" :optionLabel="testLabel" optionValue="pid" placeholder="Select lab test" filter fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Parameter Name <span class="text-red-400">*</span></label>
                        <InputText v-model="info.parameter_name" placeholder="e.g. Hemoglobin" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Unit</label>
                        <InputText v-model="info.unit" placeholder="e.g. g/dL" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Reference Range</label>
                        <InputText v-model="info.reference_range" placeholder="e.g. 13.8 - 17.2" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Min Value</label>
                        <InputNumber v-model="info.min_val" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Max Value</label>
                        <InputNumber v-model="info.max_val" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Parameter' : 'Save Parameter'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <GiMicroscope class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Lab Test Parameters</h3>
                    <p class="text-xs text-slate-400">Manage per-test measured parameters and reference ranges</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button v-if="can('lab-test-parameters', 'create')" type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Parameter
                </button>
            </div>
        </div>

        <DataTable :value="parameters" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 60rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <GiMicroscope size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No test parameters found</p>
                    <p class="text-xs mt-1">Click "Add Parameter" to create the first entry</p>
                </div>
            </template>
            <Column header="Lab Test">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.lab_test?.name || '—' }}</span></template>
            </Column>
            <Column header="Parameter">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.parameter_name || '—' }}</span></template>
            </Column>
            <Column field="unit" header="Unit" class="w-24">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.unit || '—' }}</span></template>
            </Column>
            <Column header="Reference Range" class="w-40">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.reference_range || '—' }}</span></template>
            </Column>
            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button v-if="can('lab-test-parameters', 'update')" type="button" title="Edit" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button v-if="can('lab-test-parameters', 'delete')" type="button" title="Delete" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { FiSearch } from 'vue-icons-plus/fi';
import { GiMicroscope } from 'vue-icons-plus/gi';
import { useLabTestParameterStore } from '@/store/LabTestParameter';
import { useLabTestStore } from '@/store/LabTest';
import { LabTestParameter } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';
import { usePermission } from '@/composables/permission';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const labTestParameterStore = useLabTestParameterStore();
const labTestStore = useLabTestStore();

const parameters = computed<LabTestParameter[]>(() => labTestParameterStore.parameters);
const tests = computed(() => labTestStore.tests);
const testLabel = (row: any) => `${row.code} — ${row.name}`;
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): LabTestParameter => ({ pid: '', lab_test_pid: '', parameter_name: '', unit: '', reference_range: '', min_val: null, max_val: null });
const info = reactive<LabTestParameter>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await labTestParameterStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve test parameters');
        }
    },
    () => labTestParameterStore.meta,
);

onMounted(() => {
    labTestStore.read();
});

const create = async () => {
    try {
        await labTestParameterStore.create(info);
        toast.success('Parameter created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create parameter');
    }
};

const edit = async (pid: string) => {
    try {
        await labTestParameterStore.view(pid);
        Object.assign(info, labTestParameterStore.parameter, { lab_test_pid: labTestParameterStore.parameter.lab_test?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve parameter');
    }
};

const update = async () => {
    try {
        await labTestParameterStore.update(info);
        toast.success('Parameter updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update parameter');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this parameter?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await labTestParameterStore.archive(pid);
                toast.success('Parameter deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete parameter');
            }
        },
    });
};
</script>
