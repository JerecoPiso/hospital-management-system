<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FiClipboard class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Examination Option' : 'New Examination Option' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the option details' : 'Fill in the option details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-3 gap-x-4 gap-y-4">
                    <div class="col-span-3 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Type <span class="text-red-400">*</span></label>
                        <Select v-model="info.type" :options="examinationOptionTypes" optionLabel="label" optionValue="value" placeholder="Select type" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Code <span class="text-red-400">*</span></label>
                        <InputText v-model="info.code" placeholder="e.g. 1, A" maxlength="255" required fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Description <span class="text-red-400">*</span></label>
                        <InputText v-model="info.description" placeholder="e.g. Awake and alert" maxlength="255" required fluid class="text-sm" />
                    </div>
                    <div class="col-span-3 flex items-center gap-2">
                        <input id="eo-active" type="checkbox" v-model="info.active" class="w-4 h-4 rounded border-slate-300" />
                        <label for="eo-active" class="text-sm font-medium text-slate-700">Active</label>
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Option' : 'Save Option'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FiClipboard class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Examination Options</h3>
                    <p class="text-xs text-slate-400">Choices used by the physical examination forms</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                <Select v-model="typeFilter" :options="examinationOptionTypes" optionLabel="label" optionValue="value" placeholder="All types" showClear @change="onTypeChange" class="text-sm sm:w-56" />
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button v-if="can('examination-options', 'create')" type="button" @click="openCreate" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Option
                </button>
            </div>
        </div>

        <DataTable :value="items" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 44rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FiClipboard size="34" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No options found</p>
                </div>
            </template>
            <Column header="Type" class="w-56">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ typeLabel(data.type) }}</span></template>
            </Column>
            <Column field="code" header="Code" class="w-24">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.code }}</span></template>
            </Column>
            <Column field="description" header="Description">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.description }}</span></template>
            </Column>
            <Column header="Status" class="w-28">
                <template #body="{ data }">
                    <Tag :value="data.active ? 'Active' : 'Inactive'" :severity="data.active ? 'success' : 'secondary'" />
                </template>
            </Column>
            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button v-if="can('examination-options', 'update')" type="button" title="Edit" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button v-if="can('examination-options', 'delete')" type="button" title="Delete" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
                            <BiTrash size="18" />
                        </button>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { FiClipboard, FiSearch } from 'vue-icons-plus/fi';
import { useExaminationOptionStore, examinationOptionTypes } from '@/store/ExaminationOption';
import { ExaminationOption } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';
import { usePermission } from '@/composables/permission';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const { can } = usePermission();
const store = useExaminationOptionStore();

const items = computed<ExaminationOption[]>(() => store.items);
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const typeFilter = ref<string | null>(null);
const defaultInfo = (): ExaminationOption => ({ pid: '', type: '', code: '', description: '', active: true });
const info = reactive<ExaminationOption>(defaultInfo());

const typeLabel = (type: string) => examinationOptionTypes.find((t) => t.value === type)?.label ?? type;

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

const { search, page, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await store.read({ ...params, type: typeFilter.value || undefined });
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve options');
        }
    },
    () => store.meta,
);

const onTypeChange = () => {
    page.value = 1;
    first.value = 0;
    reload();
};

const openCreate = () => {
    // Pre-select the filtered type so adding several options to one section is quicker
    info.type = typeFilter.value || '';
    modalOpen.value = true;
};

const create = async () => {
    if (!info.type) {
        toast.error('Please select a type');
        return;
    }
    try {
        await store.create(info);
        toast.success('Option created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.errors?.code?.[0] || err.response?.data?.message || 'Failed to create option');
    }
};

const edit = async (pid: string) => {
    try {
        await store.view(pid);
        Object.assign(info, defaultInfo(), store.item);
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve option');
    }
};

const update = async () => {
    try {
        await store.update(info);
        toast.success('Option updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.errors?.code?.[0] || err.response?.data?.message || 'Failed to update option');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this option?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await store.archive(pid);
                toast.success('Option deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete option');
            }
        },
    });
};
</script>
