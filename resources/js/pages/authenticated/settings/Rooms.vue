<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BsFillDoorOpenFill class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Room' : 'New Room' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the room details' : 'Fill in the room details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Ward <span class="text-red-400">*</span></label>
                        <Select v-model="info.ward_pid" :options="wards" :optionLabel="wardOptionLabel" optionValue="pid" placeholder="Select ward" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Room Number <span class="text-red-400">*</span></label>
                        <InputText v-model="info.room_number" placeholder="e.g. 201, 305A" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Room Type</label>
                        <InputText v-model="info.room_type" placeholder="e.g. private, semi-private, isolation" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Room' : 'Save Room'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <BsFillDoorOpenFill class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Rooms</h3>
                    <p class="text-xs text-slate-400">Manage rooms within each ward</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Room
                </button>
            </div>
        </div>

        <DataTable :value="rooms" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <BsFillDoorOpenFill size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No rooms found</p>
                </div>
            </template>
            <Column field="room_number" header="Room #" class="w-28">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.room_number }}</span></template>
            </Column>
            <Column header="Type" class="w-36">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.room_type || '—' }}</span></template>
            </Column>
            <Column header="Ward">
                <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.ward?.name || '—' }}</span></template>
            </Column>
            <Column header="Floor / Building">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.ward?.floor?.name || data.ward?.floor?.floor_number || '—' }} · {{ data.ward?.floor?.building?.name || '—' }}</span></template>
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
import { BsPlusCircle, BsFillDoorOpenFill } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { FiSearch } from 'vue-icons-plus/fi';
import { useRoomStore } from '@/store/Room';
import { useWardStore } from '@/store/Ward';
import { Room, Ward } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const roomStore = useRoomStore();
const wardStore = useWardStore();

const rooms = computed<Room[]>(() => roomStore.rooms);
const wards = computed(() => wardStore.wards);
const wardOptionLabel = (data: Ward) => `${data.name} (${data.code})`;
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): Room => ({ pid: '', ward_pid: '', room_number: '', room_type: '' });
const info = reactive<Room>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await roomStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve rooms');
        }
    },
    () => roomStore.meta,
);

onMounted(() => {
    wardStore.read();
});

const create = async () => {
    try {
        await roomStore.create(info);
        toast.success('Room created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create room');
    }
};

const edit = async (pid: string) => {
    try {
        await roomStore.view(pid);
        Object.assign(info, roomStore.room, { ward_pid: roomStore.room.ward?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve room');
    }
};

const update = async () => {
    try {
        await roomStore.update(info);
        toast.success('Room updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update room');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this room?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await roomStore.archive(pid);
                toast.success('Room deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete room');
            }
        },
    });
};
</script>
