<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '40vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <MdMedicalServices class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Procedure' : 'New Procedure' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the radiology procedure details' : 'Fill in the procedure details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Modality <span class="text-red-400">*</span></label>
                        <Select v-model="info.modality_pid" :options="modalities" optionLabel="name" optionValue="pid" placeholder="Select modality" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Code <span class="text-red-400">*</span></label>
                        <InputText v-model="info.code" placeholder="e.g. RAD-CT-001" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Body Part</label>
                        <InputText v-model="info.body_part" placeholder="e.g. Head, Chest" fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Name <span class="text-red-400">*</span></label>
                        <InputText v-model="info.name" placeholder="e.g. CT Scan Head" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Price <span class="text-red-400">*</span></label>
                        <InputNumber v-model="info.price" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Est. Duration (minutes)</label>
                        <InputNumber v-model="info.estimated_duration_minutes" :useGrouping="false" placeholder="30" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Procedure' : 'Save Procedure'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <MdMedicalServices class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Radiology Procedures</h3>
                    <p class="text-xs text-slate-400">Manage imaging procedures, pricing and duration</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button v-if="can('radiology-procedures', 'create')" type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Procedure
                </button>
            </div>
        </div>

        <DataTable :value="procedures" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 62rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <MdMedicalServices size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No radiology procedures found</p>
                    <p class="text-xs mt-1">Click "Add Procedure" to create the first entry</p>
                </div>
            </template>
            <Column header="Modality" class="w-32">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.modality?.name || '—' }}</span></template>
            </Column>
            <Column field="code" header="Code" class="w-32">
                <template #body="{ data }"><span class="text-slate-700 text-sm font-mono">{{ data.code }}</span></template>
            </Column>
            <Column header="Name">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.name || '—' }}</span></template>
            </Column>
            <Column header="Body Part" class="w-28">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.body_part || '—' }}</span></template>
            </Column>
            <Column field="price" header="Price" class="w-28">
                <template #body="{ data }"><span class="text-slate-700 text-sm font-medium">₱{{ Number(data.price).toFixed(2) }}</span></template>
            </Column>
            <Column header="Duration" class="w-24">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.estimated_duration_minutes ? `${data.estimated_duration_minutes} min` : '—' }}</span></template>
            </Column>
            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button v-if="can('radiology-procedures', 'update')" type="button" title="Edit" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button v-if="can('radiology-procedures', 'delete')" type="button" title="Delete" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { MdMedicalServices } from 'vue-icons-plus/md';
import { useRadiologyProcedureStore } from '@/store/RadiologyProcedure';
import { useRadiologyModalityStore } from '@/store/RadiologyModality';
import { RadiologyProcedure } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';
import { usePermission } from '@/composables/permission';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const radiologyProcedureStore = useRadiologyProcedureStore();
const radiologyModalityStore = useRadiologyModalityStore();

const procedures = computed<RadiologyProcedure[]>(() => radiologyProcedureStore.procedures);
const modalities = computed(() => radiologyModalityStore.modalities);
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): RadiologyProcedure => ({ pid: '', modality_pid: '', code: '', name: '', body_part: '', price: 0, estimated_duration_minutes: 30 });
const info = reactive<RadiologyProcedure>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await radiologyProcedureStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve radiology procedures');
        }
    },
    () => radiologyProcedureStore.meta,
);

onMounted(() => {
    radiologyModalityStore.read();
});

const create = async () => {
    try {
        await radiologyProcedureStore.create(info);
        toast.success('Procedure created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create procedure');
    }
};

const edit = async (pid: string) => {
    try {
        await radiologyProcedureStore.view(pid);
        Object.assign(info, radiologyProcedureStore.procedure, { modality_pid: radiologyProcedureStore.procedure.modality?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve procedure');
    }
};

const update = async () => {
    try {
        await radiologyProcedureStore.update(info);
        toast.success('Procedure updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update procedure');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this procedure?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await radiologyProcedureStore.archive(pid);
                toast.success('Procedure deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete procedure');
            }
        },
    });
};
</script>
