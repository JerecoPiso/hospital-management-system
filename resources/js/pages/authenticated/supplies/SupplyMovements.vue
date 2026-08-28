<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="modalOpen" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <MdSwapVert class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Record Stock Movement</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Adjusts the selected batch's quantity immediately</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="create" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Stock Batch <span class="text-red-400">*</span></label>
                        <Select v-model="info.supply_stock_pid" :options="supplyStocks" :optionLabel="stockOptionLabel" optionValue="pid" placeholder="Select stock batch" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Type <span class="text-red-400">*</span></label>
                        <Select v-model="info.type" :options="['IN', 'OUT']" placeholder="Select type" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Quantity <span class="text-red-400">*</span></label>
                        <InputNumber v-model="info.quantity" :useGrouping="false" placeholder="0" required fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Used For</label>
                        <InputText v-model="info.used_for" placeholder="e.g. procedure, patient, department" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="modalOpen = false" />
                    <Button type="submit" label="Save Movement" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <MdSwapVert class="text-white" size="20" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Supply Movements</h3>
                    <p class="text-xs text-slate-400">Track supply usage and stock adjustments</p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search . . ." class="w-full text-sm pl-8!" />
                </div>
                <button type="button" @click="modalOpen = true" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95 shrink-0">
                    <BsPlusCircle size="16" />
                    Record Movement
                </button>
            </div>
        </div>

        <DataTable :value="supplyMovements" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <MdSwapVert size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No movements recorded</p>
                </div>
            </template>
            <Column header="Supply">
                <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.supplyStock?.supply?.name || '—' }}</span></template>
            </Column>
            <Column header="Batch #" class="w-28">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.supplyStock?.batch_number || '—' }}</span></template>
            </Column>
            <Column header="Type" class="w-24">
                <template #body="{ data }">
                    <Tag :value="data.type" :severity="data.type === 'IN' ? 'success' : 'warn'" />
                </template>
            </Column>
            <Column field="quantity" header="Quantity" class="w-24" />
            <Column header="Used For">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.used_for || '—' }}</span></template>
            </Column>
            <Column header="Date" class="w-40">
                <template #body="{ data }"><span class="text-slate-500 text-xs">{{ formatDate(data.created_at) }}</span></template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { FiSearch } from 'vue-icons-plus/fi';
import { MdSwapVert } from 'vue-icons-plus/md';
import { useSupplyMovementStore } from '@/store/SupplyMovement';
import { useSupplyStockStore } from '@/store/SupplyStock';
import { SupplyMovement, SupplyStock } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useAppToast } from '@/composables/toast';

const toast = useAppToast();
const supplyMovementStore = useSupplyMovementStore();
const supplyStockStore = useSupplyStockStore();

const supplyMovements = computed<SupplyMovement[]>(() => supplyMovementStore.supplyMovements);
const supplyStocks = computed<SupplyStock[]>(() => supplyStockStore.supplyStocks);
const modalOpen = ref<boolean>(false);
const defaultInfo = (): SupplyMovement => ({ supply_stock_pid: '', quantity: 0, type: 'IN', used_for: '' });
const info = reactive<SupplyMovement>(defaultInfo());

watch(modalOpen, (open) => { if (!open) { Object.assign(info, defaultInfo()); } });

const formatDate = (value?: string) => value ? new Date(value).toLocaleString() : '—';
const stockOptionLabel = (data: SupplyStock) => `${data.supply?.name || 'Unknown'}${data.batch_number ? ' — ' + data.batch_number : ''} (qty: ${data.quantity ?? 0})`;

const { search, rows, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await supplyMovementStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve movements');
        }
    },
    () => supplyMovementStore.meta,
);

onMounted(() => {
    supplyStockStore.read();
});

const create = async () => {
    try {
        await supplyMovementStore.create(info);
        toast.success('Movement recorded successfully');
        modalOpen.value = false;
        await reload();
        await supplyStockStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || 'Failed to record movement');
    }
};
</script>
