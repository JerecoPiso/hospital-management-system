<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FaBed class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Bed' : 'New Bed' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the bed details' : 'Fill in the bed details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Room <span class="text-red-400">*</span></label>
                        <Select v-model="info.room_pid" :options="rooms" :optionLabel="roomOptionLabel" optionValue="pid" placeholder="Select room" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Bed Number <span class="text-red-400">*</span></label>
                        <InputText v-model="info.bed_number" placeholder="e.g. A, B, 1, 2" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Status</label>
                        <Select v-model="info.status" :options="statusOptions" placeholder="Select status" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Bed' : 'Save Bed'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FaBed class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Beds</h3>
                    <p class="text-xs text-slate-400">Manage beds within each room</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Bed
                </button>
            </div>
        </div>

        <DataTable :value="beds" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 50rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FaBed size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No beds found</p>
                </div>
            </template>
            <Column field="bed_number" header="Bed #" class="w-24">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.bed_number }}</span></template>
            </Column>
            <Column header="Room" class="w-32">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.room?.room_number || '—' }}</span></template>
            </Column>
            <Column header="Ward">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.room?.ward?.name || '—' }}</span></template>
            </Column>
            <Column header="Status" class="w-32">
                <template #body="{ data }">
                    <Tag :value="data.status" :severity="statusSeverity(data.status)" />
                </template>
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
import { FiSearch } from 'vue-icons-plus/fi';
import { FaBed } from 'vue-icons-plus/fa';
import { useBedStore } from '@/store/Bed';
import { useRoomStore } from '@/store/Room';
import { Bed, Room } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const bedStore = useBedStore();
const roomStore = useRoomStore();

const beds = computed<Bed[]>(() => bedStore.beds);
const rooms = computed(() => roomStore.rooms);
const roomOptionLabel = (data: Room) => `${data.room_number}${data.ward?.name ? ' — ' + data.ward.name : ''}`;
const statusOptions = ['available', 'occupied', 'cleaning', 'maintenance'];
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): Bed => ({ room_pid: '', bed_number: '', status: 'available' });
const info = reactive<Bed>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

onMounted(() => {
    roomStore.read();
});

const statusSeverity = (status?: string) => {
    switch (status) {
        case 'available': return 'success';
        case 'occupied': return 'danger';
        case 'cleaning': return 'warn';
        case 'maintenance': return 'secondary';
        default: return undefined;
    }
};

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await bedStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve beds');
        }
    },
    () => bedStore.meta,
);

const create = async () => {
    try {
        await bedStore.create(info);
        toast.success('Bed created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create bed');
    }
};

const edit = async (pid: string) => {
    try {
        await bedStore.view(pid);
        Object.assign(info, bedStore.bed, { room_pid: bedStore.bed.room?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve bed');
    }
};

const update = async () => {
    try {
        await bedStore.update(info);
        toast.success('Bed updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update bed');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this bed?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await bedStore.archive(pid);
                toast.success('Bed deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete bed');
            }
        },
    });
};
</script>
