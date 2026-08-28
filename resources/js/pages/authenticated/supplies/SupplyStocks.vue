<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '42vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BiBox class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Stock Batch' : 'New Stock Batch' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the stock batch details' : 'Receive a new stock batch for a supply item' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Supply Item <span class="text-red-400">*</span></label>
                        <Select v-model="info.supply_pid" :options="supplies" optionLabel="name" optionValue="pid" placeholder="Select supply item" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Quantity</label>
                        <InputNumber v-model="info.quantity" :useGrouping="false" placeholder="0" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Purchase Price</label>
                        <InputNumber v-model="info.purchase_price" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Unit Type</label>
                        <InputText v-model="info.unit_type" placeholder="e.g. box, roll, pack" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Units per Package</label>
                        <InputNumber v-model="info.units_per_package" :useGrouping="false" placeholder="1" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Reorder Level</label>
                        <InputNumber v-model="info.reorder_level" :useGrouping="false" placeholder="100" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Expiration Date</label>
                        <DatePicker v-model="expirationDate" dateFormat="yy-mm-dd" showIcon fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Batch Number</label>
                        <InputText v-model="info.batch_number" placeholder="e.g. LOT-2026-001" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" :label="isUpdate ? 'Update Batch' : 'Save Batch'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <BiBox class="text-white" size="20" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Supply Stocks</h3>
                    <p class="text-xs text-slate-400">Manage supply stock batches</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Add Stock Batch
                </button>
            </div>
        </div>

        <DataTable :value="supplyStocks" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 65rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <BiBox size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No stock batches found</p>
                </div>
            </template>
            <Column header="Supply">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.supply?.name || '—' }}</span></template>
            </Column>
            <Column field="batch_number" header="Batch #" class="w-32">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.batch_number || '—' }}</span></template>
            </Column>
            <Column field="quantity" header="Quantity" class="w-24">
                <template #body="{ data }"><span class="text-slate-700 text-sm font-medium">{{ data.quantity }}</span></template>
            </Column>
            <Column field="unit_type" header="Unit Type" class="w-28" />
            <Column field="units_per_package" header="Units/Pkg" class="w-24" />
            <Column field="reorder_level" header="Reorder Lvl" class="w-28" />
            <Column header="Expiration" class="w-32">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.expiration_date || '—' }}</span></template>
            </Column>
            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="Edit stock" @click="edit(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                            <BiEdit size="18" />
                        </button>
                        <button type="button" title="Delete stock" @click="archive(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
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
import { FiSearch } from 'vue-icons-plus/fi';
import { BiEdit, BiTrash, BiBox } from 'vue-icons-plus/bi';
import { useSupplyStockStore } from '@/store/SupplyStock';
import { useSupplyStore } from '@/store/Supply';
import { SupplyStock } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const supplyStockStore = useSupplyStockStore();
const supplyStore = useSupplyStore();

const supplyStocks = computed<SupplyStock[]>(() => supplyStockStore.supplyStocks);
const supplies = computed(() => supplyStore.supplies);
const modalOpen = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const defaultInfo = (): SupplyStock => ({ pid: '', supply_pid: '', quantity: 0, purchase_price: null, reorder_level: 100, unit_type: 'box', units_per_package: 1, expiration_date: null, batch_number: '' });
const info = reactive<SupplyStock>(defaultInfo());
const expirationDate = ref<Date | null>(null);

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); expirationDate.value = null; isUpdate.value = false; } });
watch(expirationDate, (date) => { info.expiration_date = date ? date.toISOString().slice(0, 10) : null; });

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await supplyStockStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve supply stocks');
        }
    },
    () => supplyStockStore.meta,
);

onMounted(() => {
    supplyStore.read();
});

const create = async () => {
    try {
        await supplyStockStore.create(info);
        toast.success('Stock batch created successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to create stock batch');
    }
};

const edit = async (pid: string) => {
    try {
        await supplyStockStore.view(pid);
        Object.assign(info, supplyStockStore.supplyStock, { supply_pid: supplyStockStore.supplyStock.supply?.pid || '' });
        expirationDate.value = supplyStockStore.supplyStock.expiration_date ? new Date(supplyStockStore.supplyStock.expiration_date) : null;
        isUpdate.value = true;
        modalOpen.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to retrieve stock batch');
    }
};

const update = async () => {
    try {
        await supplyStockStore.update(info);
        toast.success('Stock batch updated successfully');
        modalOpen.value = false;
        await reload();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to update stock batch');
    }
};

const archive = (pid: string) => {
    showConfirm({
        message: 'Are you sure you want to delete this stock batch?',
        header: 'Delete Confirmation',
        onAccept: async () => {
            try {
                await supplyStockStore.archive(pid);
                toast.success('Stock batch deleted successfully');
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to delete stock batch');
            }
        },
    });
};
</script>
