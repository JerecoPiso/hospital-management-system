<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-linear-to-r from-slate-50 to-white">
            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                <GiMedicalPack class="text-white" size="20" />
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Supplies &amp; Inventory</h3>
                <p class="text-xs text-slate-400">Manage supply items, stock batches, movements and station distributions</p>
            </div>
        </div>

        <Tabs value="items">
            <TabList>
                <Tab value="items">Items</Tab>
                <Tab value="stocks">Stocks</Tab>
                <Tab value="movements">Movements</Tab>
                <Tab value="distributions">Distributions</Tab>
            </TabList>
            <TabPanels>
                <!-- ITEMS TAB -->
                <TabPanel value="items">
                    <div class="flex justify-end mb-4">
                        <button type="button" @click="openSupplyModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                            <BsPlusCircle size="16" />
                            Add Item
                        </button>
                    </div>
                    <DataTable :value="supplies" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
                        :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
                        <template #empty>
                            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                <GiMedicalPack size="40" class="mb-3 opacity-30" />
                                <p class="text-sm font-medium">No supply items found</p>
                                <p class="text-xs mt-1">Click "Add Item" to create the first entry</p>
                            </div>
                        </template>
                        <Column field="name" header="Name">
                            <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.name }}</span></template>
                        </Column>
                        <Column field="unit" header="Unit" class="w-28">
                            <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.unit }}</span></template>
                        </Column>
                        <Column header="Selling Price" class="w-32">
                            <template #body="{ data }">
                                <span class="text-slate-700 text-sm font-medium">{{ data.selling_price != null ? `₱${Number(data.selling_price).toFixed(2)}` : '—' }}</span>
                            </template>
                        </Column>
                        <Column header="Total Stock" class="w-32">
                            <template #body="{ data }"><span class="text-slate-700 text-sm">{{ data.supply_stocks_sum_quantity ?? 0 }}</span></template>
                        </Column>
                        <Column header="Status" class="w-28">
                            <template #body="{ data }">
                                <Tag :value="data.is_active ? 'Active' : 'Inactive'" :severity="data.is_active ? 'success' : 'secondary'" />
                            </template>
                        </Column>
                        <Column header="Actions" class="w-24">
                            <template #body="{ data }">
                                <div class="flex items-center gap-1">
                                    <button type="button" title="Edit item" @click="editSupply(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                                        <BiEdit size="18" />
                                    </button>
                                    <button type="button" title="Delete item" @click="archiveSupply(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
                                        <BiTrash size="18" />
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

                <!-- STOCKS TAB -->
                <TabPanel value="stocks">
                    <div class="flex justify-end mb-4">
                        <button type="button" @click="openStockModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                            <BsPlusCircle size="16" />
                            Add Stock Batch
                        </button>
                    </div>
                    <DataTable :value="supplyStocks" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 65rem"
                        :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
                        <template #empty>
                            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                <GiMedicalPack size="40" class="mb-3 opacity-30" />
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
                                    <button type="button" title="Edit stock" @click="editStock(data.pid)" class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer">
                                        <BiEdit size="18" />
                                    </button>
                                    <button type="button" title="Delete stock" @click="archiveStock(data.pid)" class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer">
                                        <BiTrash size="18" />
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

                <!-- MOVEMENTS TAB -->
                <TabPanel value="movements">
                    <div class="flex justify-end mb-4">
                        <button type="button" @click="openMovementModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                            <BsPlusCircle size="16" />
                            Record Movement
                        </button>
                    </div>
                    <DataTable :value="supplyMovements" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 55rem"
                        :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
                        <template #empty>
                            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                <GiMedicalPack size="40" class="mb-3 opacity-30" />
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
                </TabPanel>

                <!-- DISTRIBUTIONS TAB -->
                <TabPanel value="distributions">
                    <div class="flex justify-end mb-4">
                        <button type="button" @click="openDistributionModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95">
                            <BsPlusCircle size="16" />
                            Distribute Stock
                        </button>
                    </div>
                    <DataTable :value="supplyDistributions" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 60rem"
                        :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
                        <template #empty>
                            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                <GiMedicalPack size="40" class="mb-3 opacity-30" />
                                <p class="text-sm font-medium">No distributions recorded</p>
                            </div>
                        </template>
                        <Column header="Supply">
                            <template #body="{ data }"><span class="text-slate-800 text-sm font-medium">{{ data.supplyStock?.supply?.name || '—' }}</span></template>
                        </Column>
                        <Column header="Station">
                            <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.station?.name || '—' }}</span></template>
                        </Column>
                        <Column field="quantity" header="Quantity" class="w-24" />
                        <Column header="Distributed By">
                            <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.distributedBy?.firstname ? `${data.distributedBy.firstname} ${data.distributedBy.lastname}` : '—' }}</span></template>
                        </Column>
                        <Column header="Distributed At" class="w-44">
                            <template #body="{ data }"><span class="text-slate-500 text-xs">{{ formatDate(data.distributed_at) }}</span></template>
                        </Column>
                    </DataTable>
                </TabPanel>
            </TabPanels>
        </Tabs>

        <!-- SUPPLY (ITEM) MODAL -->
        <Dialog v-model:visible="supplyModal" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicalPack class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isSupplyUpdate ? 'Edit Supply Item' : 'New Supply Item' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isSupplyUpdate ? 'Update the supply item details' : 'Fill in the supply item details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isSupplyUpdate ? updateSupply() : createSupply()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Name <span class="text-red-400">*</span></label>
                        <InputText v-model="supplyInfo.name" placeholder="Supply item name" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Unit <span class="text-red-400">*</span></label>
                        <InputText v-model="supplyInfo.unit" placeholder="e.g. pcs, box, roll" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Selling Price</label>
                        <InputNumber v-model="supplyInfo.selling_price" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex items-center gap-2">
                        <input id="is_active" type="checkbox" v-model="supplyInfo.is_active" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                        <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="supplyModal = false" />
                    <Button type="submit" :label="isSupplyUpdate ? 'Update Item' : 'Save Item'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- SUPPLY STOCK MODAL -->
        <Dialog v-model:visible="stockModal" modal :style="{ width: '42vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicalPack class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isStockUpdate ? 'Edit Stock Batch' : 'New Stock Batch' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isStockUpdate ? 'Update the stock batch details' : 'Receive a new stock batch for a supply item' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isStockUpdate ? updateStock() : createStock()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Supply Item <span class="text-red-400">*</span></label>
                        <Select v-model="stockInfo.supply_pid" :options="supplies" optionLabel="name" optionValue="pid" placeholder="Select supply item" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Quantity</label>
                        <InputNumber v-model="stockInfo.quantity" :useGrouping="false" placeholder="0" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Purchase Price</label>
                        <InputNumber v-model="stockInfo.purchase_price" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Unit Type</label>
                        <InputText v-model="stockInfo.unit_type" placeholder="e.g. box, roll, pack" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Units per Package</label>
                        <InputNumber v-model="stockInfo.units_per_package" :useGrouping="false" placeholder="1" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Reorder Level</label>
                        <InputNumber v-model="stockInfo.reorder_level" :useGrouping="false" placeholder="100" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Expiration Date</label>
                        <DatePicker v-model="stockExpirationDate" dateFormat="yy-mm-dd" showIcon fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Batch Number</label>
                        <InputText v-model="stockInfo.batch_number" placeholder="e.g. LOT-2026-001" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="stockModal = false" />
                    <Button type="submit" :label="isStockUpdate ? 'Update Batch' : 'Save Batch'" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- MOVEMENT MODAL -->
        <Dialog v-model:visible="movementModal" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicalPack class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Record Stock Movement</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Adjusts the selected batch's quantity immediately</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="createMovement" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Stock Batch <span class="text-red-400">*</span></label>
                        <Select v-model="movementInfo.supply_stock_pid" :options="supplyStocks" :optionLabel="stockOptionLabel" optionValue="pid" placeholder="Select stock batch" filter fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Type <span class="text-red-400">*</span></label>
                        <Select v-model="movementInfo.type" :options="['IN', 'OUT']" placeholder="Select type" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Quantity <span class="text-red-400">*</span></label>
                        <InputNumber v-model="movementInfo.quantity" :useGrouping="false" placeholder="0" required fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Used For</label>
                        <InputText v-model="movementInfo.used_for" placeholder="e.g. procedure, patient, department" fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="movementModal = false" />
                    <Button type="submit" label="Save Movement" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>

        <!-- DISTRIBUTION MODAL -->
        <Dialog v-model:visible="distributionModal" modal :style="{ width: '38vw' }" :breakpoints="{ '1199px': '75vw', '575px': '95vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicalPack class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Distribute Stock to Station</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Deducts the quantity from the selected batch</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="createDistribution" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Stock Batch <span class="text-red-400">*</span></label>
                        <Select v-model="distributionInfo.supply_stock_pid" :options="supplyStocks" :optionLabel="stockOptionLabel" optionValue="pid" placeholder="Select stock batch" filter fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Station <span class="text-red-400">*</span></label>
                        <Select v-model="distributionInfo.station_pid" :options="stations" optionLabel="name" optionValue="pid" placeholder="Select station" filter fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Quantity <span class="text-red-400">*</span></label>
                        <InputNumber v-model="distributionInfo.quantity" :useGrouping="false" placeholder="0" required fluid class="text-sm" />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button type="button" label="Cancel" severity="secondary" outlined fluid @click="distributionModal = false" />
                    <Button type="submit" label="Save Distribution" fluid class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { GiMedicalPack } from 'vue-icons-plus/gi';
import { useSupplyStore } from '@/store/Supply';
import { useSupplyStockStore } from '@/store/SupplyStock';
import { useSupplyMovementStore } from '@/store/SupplyMovement';
import { useSupplyDistributionStore } from '@/store/SupplyDistribution';
import { useStationStore } from '@/store/Station';
import { Supply, SupplyStock, SupplyMovement, SupplyDistribution } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from "@/composables/toast";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();

const supplyStore = useSupplyStore();
const supplyStockStore = useSupplyStockStore();
const supplyMovementStore = useSupplyMovementStore();
const supplyDistributionStore = useSupplyDistributionStore();
const stationStore = useStationStore();

const supplies = computed<Supply[]>(() => supplyStore.supplies);
const supplyStocks = computed<SupplyStock[]>(() => supplyStockStore.supplyStocks);
const supplyMovements = computed<SupplyMovement[]>(() => supplyMovementStore.supplyMovements);
const supplyDistributions = computed<SupplyDistribution[]>(() => supplyDistributionStore.supplyDistributions);
const stations = computed(() => stationStore.stations);

const formatDate = (value?: string) => value ? new Date(value).toLocaleString() : '—';
const stockOptionLabel = (data: SupplyStock) => `${data.supply?.name || 'Unknown'}${data.batch_number ? ' — ' + data.batch_number : ''} (qty: ${data.quantity ?? 0})`;

onMounted(async () => {
    await Promise.all([
        supplyStore.read(),
        supplyStockStore.read(),
        supplyMovementStore.read(),
        supplyDistributionStore.read(),
        stationStore.read(),
    ]);
});

/* ---------------- SUPPLY ITEM ---------------- */
const supplyModal = ref<boolean>(false);
const isSupplyUpdate = ref<boolean>(false);
const defaultSupplyInfo = (): Supply => ({ pid: '', name: '', unit: '', selling_price: null, is_active: true });
const supplyInfo = reactive<Supply>(defaultSupplyInfo());

watch(supplyModal, (open) => { if (!open) { Object.assign(supplyInfo, defaultSupplyInfo()); isSupplyUpdate.value = false; } });

const openSupplyModal = () => { supplyModal.value = true; };

const createSupply = async () => {
    try {
        await supplyStore.create(supplyInfo);
        toast.success("Supply item created successfully");
        supplyModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create supply item");
    }
};

const editSupply = async (pid: string) => {
    try {
        await supplyStore.view(pid);
        Object.assign(supplyInfo, supplyStore.supply);
        isSupplyUpdate.value = true;
        supplyModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve supply item");
    }
};

const updateSupply = async () => {
    try {
        await supplyStore.update(supplyInfo);
        toast.success("Supply item updated successfully");
        supplyModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update supply item");
    }
};

const archiveSupply = (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this supply item?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await supplyStore.archive(pid);
                toast.success("Supply item deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete supply item");
            }
        },
    });
};

/* ---------------- SUPPLY STOCK ---------------- */
const stockModal = ref<boolean>(false);
const isStockUpdate = ref<boolean>(false);
const defaultStockInfo = (): SupplyStock => ({ pid: '', supply_pid: '', quantity: 0, purchase_price: null, reorder_level: 100, unit_type: 'box', units_per_package: 1, expiration_date: null, batch_number: '' });
const stockInfo = reactive<SupplyStock>(defaultStockInfo());
const stockExpirationDate = ref<Date | null>(null);

watch(stockModal, (open) => { if (!open) { Object.assign(stockInfo, defaultStockInfo()); stockExpirationDate.value = null; isStockUpdate.value = false; } });
watch(stockExpirationDate, (date) => { stockInfo.expiration_date = date ? date.toISOString().slice(0, 10) : null; });

const openStockModal = () => { stockModal.value = true; };

const createStock = async () => {
    try {
        await supplyStockStore.create(stockInfo);
        toast.success("Stock batch created successfully");
        stockModal.value = false;
        await supplyStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create stock batch");
    }
};

const editStock = async (pid: string) => {
    try {
        await supplyStockStore.view(pid);
        Object.assign(stockInfo, supplyStockStore.supplyStock, { supply_pid: supplyStockStore.supplyStock.supply?.pid || '' });
        stockExpirationDate.value = supplyStockStore.supplyStock.expiration_date ? new Date(supplyStockStore.supplyStock.expiration_date) : null;
        isStockUpdate.value = true;
        stockModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve stock batch");
    }
};

const updateStock = async () => {
    try {
        await supplyStockStore.update(stockInfo);
        toast.success("Stock batch updated successfully");
        stockModal.value = false;
        await supplyStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update stock batch");
    }
};

const archiveStock = (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this stock batch?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await supplyStockStore.archive(pid);
                toast.success("Stock batch deleted successfully");
                await supplyStore.read();
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete stock batch");
            }
        },
    });
};

/* ---------------- SUPPLY MOVEMENT ---------------- */
const movementModal = ref<boolean>(false);
const defaultMovementInfo = (): SupplyMovement => ({ supply_stock_pid: '', quantity: 0, type: 'IN', used_for: '' });
const movementInfo = reactive<SupplyMovement>(defaultMovementInfo());

watch(movementModal, (open) => { if (!open) { Object.assign(movementInfo, defaultMovementInfo()); } });

const openMovementModal = () => { movementModal.value = true; };

const createMovement = async () => {
    try {
        await supplyMovementStore.create(movementInfo);
        toast.success("Movement recorded successfully");
        movementModal.value = false;
        await Promise.all([supplyStockStore.read(), supplyStore.read()]);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to record movement");
    }
};

/* ---------------- SUPPLY DISTRIBUTION ---------------- */
const distributionModal = ref<boolean>(false);
const defaultDistributionInfo = (): SupplyDistribution => ({ supply_stock_pid: '', station_pid: '', quantity: 0 });
const distributionInfo = reactive<SupplyDistribution>(defaultDistributionInfo());

watch(distributionModal, (open) => { if (!open) { Object.assign(distributionInfo, defaultDistributionInfo()); } });

const openDistributionModal = () => { distributionModal.value = true; };

const createDistribution = async () => {
    try {
        await supplyDistributionStore.create(distributionInfo);
        toast.success("Stock distributed successfully");
        distributionModal.value = false;
        await Promise.all([supplyStockStore.read(), supplyStore.read(), supplyMovementStore.read()]);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to distribute stock");
    }
};
</script>
