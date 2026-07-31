<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <MdLocationOn class="text-white" size="16" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Station' : 'New Station' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the station details' : 'Fill in the station details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Ward <span class="text-red-400">*</span></label>
                        <Select v-model="info.ward_pid" :options="wards" :optionLabel="wardOptionLabel" optionValue="pid" placeholder="Select ward" filter fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Name <span class="text-red-400">*</span></label>
                        <InputText v-model="info.name" placeholder="e.g. Nurse Station 1" required fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Description</label>
                        <Textarea v-model="info.description" rows="3" placeholder="Optional description" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Station' : 'Save Station'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <MdLocationOn class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Stations</h3>
                    <p class="text-xs text-slate-400">Manage nursing/service stations within each ward</p>
                </div>
            </div>
            <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                <BsPlusCircle size="16" />
                Add Station
            </button>
        </div>

        <DataTable :value="stations" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 50rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <MdLocationOn size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No stations found</p>
                </div>
            </template>
            <Column field="name" header="Name">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.name }}</span></template>
            </Column>
            <Column header="Ward">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.ward?.name || '—' }}</span></template>
            </Column>
            <Column header="Description">
                <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.description || '—' }}</span></template>
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
import { MdLocationOn } from 'vue-icons-plus/md';
import { useStationStore } from '@/store/Station';
import { useWardStore } from '@/store/Ward';
import { Station, Ward } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const stationStore = useStationStore();
const wardStore = useWardStore();

const stations = computed<Station[]>(() => stationStore.stations);
const wards = computed(() => wardStore.wards);
const wardOptionLabel = (data: Ward) => `${data.name} (${data.code})`;
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): Station => ({ pid: '', ward_pid: '', name: '', description: '' });
const info = reactive<Station>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); isUpdate.value = false; } });

onMounted(async () => {
    await Promise.all([read(), wardStore.read()]);
});

const read = async () => {
    try {
        await stationStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve stations');
    }
};

const create = async () => {
    try {
        await stationStore.create(info);
        toast.success('Station created successfully');
        modalOpen.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create station');
    }
};

const edit = async (pid: string) => {
    try {
        await stationStore.view(pid);
        Object.assign(info, stationStore.station, { ward_pid: stationStore.station.ward?.pid || '' });
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve station');
    }
};

const update = async () => {
    try {
        await stationStore.update(info);
        toast.success('Station updated successfully');
        modalOpen.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update station');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this station?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await stationStore.archive(pid);
                toast.success('Station deleted successfully');
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete station');
            }
        },
    });
};
</script>
