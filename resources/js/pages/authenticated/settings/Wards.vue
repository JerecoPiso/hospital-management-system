<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <Fa6Hospital class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Ward' : 'New Ward' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the ward details' : 'Fill in the ward details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Floor <span class="text-red-400">*</span></label>
                        <Select v-model="info.floor_pid" :options="floors" :optionLabel="floorOptionLabel" optionValue="pid" placeholder="Select floor" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Code <span class="text-red-400">*</span></label>
                        <InputText v-model="info.code" placeholder="e.g. MED, SURG, PEDS" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Name <span class="text-red-400">*</span></label>
                        <InputText v-model="info.name" placeholder="e.g. Medical Ward" required fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Ward' : 'Save Ward'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <Fa6Hospital class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Wards</h3>
                    <p class="text-xs text-slate-400">Manage wards within each floor</p>
                </div>
            </div>
            <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                <BsPlusCircle size="16" />
                Add Ward
            </button>
        </div>

        <DataTable :value="wards" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <Fa6Hospital size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No wards found</p>
                </div>
            </template>
            <Column field="code" header="Code" class="w-28">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.code }}</span></template>
            </Column>
            <Column field="name" header="Name">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.name }}</span></template>
            </Column>
            <Column header="Floor">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.floor?.name || data.floor?.floor_number || '—' }}</span></template>
            </Column>
            <Column header="Building">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.floor?.building?.name || '—' }}</span></template>
            </Column>
            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
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
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { Fa6Hospital } from 'vue-icons-plus/fa6';
import { useWardStore } from '@/store/Ward';
import { useFloorStore } from '@/store/Floor';
import { Ward, Floor } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const wardStore = useWardStore();
const floorStore = useFloorStore();

const wards = computed<Ward[]>(() => wardStore.wards);
const floors = computed(() => floorStore.floors);
const floorOptionLabel = (data: Floor) => `${data.building?.name || 'Unknown'} — ${data.name || data.floor_number}`;
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): Ward => ({ pid: '', floor_pid: '', code: '', name: '' });
const info = reactive<Ward>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

onMounted(async () => {
    await Promise.all([read(), floorStore.read()]);
});

const read = async () => {
    try {
        await wardStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve wards');
    }
};

const create = async () => {
    try {
        await wardStore.create(info);
        toast.success('Ward created successfully');
        modalOpen.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create ward');
    }
};

const edit = async (pid: string) => {
    try {
        await wardStore.view(pid);
        Object.assign(info, wardStore.ward, { floor_pid: wardStore.ward.floor?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve ward');
    }
};

const update = async () => {
    try {
        await wardStore.update(info);
        toast.success('Ward updated successfully');
        modalOpen.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update ward');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this ward?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await wardStore.archive(pid);
                toast.success('Ward deleted successfully');
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete ward');
            }
        },
    });
};
</script>
