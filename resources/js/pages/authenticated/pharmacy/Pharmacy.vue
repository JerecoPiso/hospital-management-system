<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Rx paper dialog -->
        <Dialog v-model:visible="rxModalOpen" modal :style="{ width: '52vw' }" :breakpoints="{ '1199px': '80vw', '575px': '96vw' }" :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <FaFilePrescription class="text-white" size="15" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Prescription</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Rx view &bull; {{ formatDateTime(rxPrescription?.prescription_date) }}</p>
                    </div>
                </div>
            </template>
            <PrescriptionPaper :prescription="rxPrescription" />
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <FaCapsules class="text-white" size="17" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Pharmacy</h3>
                    <p class="text-xs text-slate-400">Review and dispense physician prescriptions</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <Select v-model="statusFilter" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="All statuses" class="text-sm w-full sm:w-44" @change="reload" />
                <div class="relative w-full sm:w-64">
                    <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                    <InputText v-model="search" @input="onSearch" placeholder="Search patient / medicine . . ." class="w-full text-sm pl-8!" />
                </div>
            </div>
        </div>

        <!-- Table -->
        <DataTable :value="rows" lazy paginator :rows="perPage" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 68rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <FaCapsules size="36" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No prescriptions found</p>
                </div>
            </template>

            <Column header="Date" class="w-40">
                <template #body="{ data }"><span class="text-slate-600 text-sm">{{ formatDateTime(data.prescription_date) }}</span></template>
            </Column>

            <Column header="Patient">
                <template #body="{ data }">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                            {{ patientName(data).charAt(0).toUpperCase() || '?' }}
                        </div>
                        <div>
                            <p class="text-slate-800 text-sm font-medium leading-tight">{{ patientName(data) || '—' }}</p>
                            <p class="text-slate-400 text-xs mt-0.5">{{ data.patient_case?.case_number || '—' }}</p>
                        </div>
                    </div>
                </template>
            </Column>

            <Column header="Physician" class="w-44">
                <template #body="{ data }">
                    <span class="text-slate-700 text-sm">{{ `${data.doctor?.firstname ?? ''} ${data.doctor?.lastname ?? ''}`.trim() || '—' }}</span>
                </template>
            </Column>

            <Column header="Medicines">
                <template #body="{ data }">
                    <ul class="space-y-0.5">
                        <li v-for="(item, idx) in data.items" :key="idx" class="text-slate-700 text-sm">
                            {{ item.medicine?.name || '—' }}
                            <span class="text-slate-400 text-xs">{{ [item.frequency, item.quantity ? `#${item.quantity}` : null].filter(Boolean).join(' • ') }}</span>
                        </li>
                    </ul>
                </template>
            </Column>

            <Column header="Status" class="w-32">
                <template #body="{ data }"><Tag :value="data.status" :severity="statusSeverity(data.status)" /></template>
            </Column>

            <Column header="Actions" class="w-64">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button type="button" title="View as prescription (Rx)" @click="openRx(data)" class="p-1.5 rounded-md text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 cursor-pointer">
                            <FaFilePrescription size="18" />
                        </button>
                        <button type="button" title="Mark as dispensed" :disabled="data.status === 'done'" @click="setStatus(data, 'done')"
                            class="px-2 py-1 rounded-md text-xs font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 transition-colors duration-150 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                            Dispense
                        </button>
                        <button type="button" title="Mark as picked-up" :disabled="data.status === 'picked-up'" @click="setStatus(data, 'picked-up')"
                            class="px-2 py-1 rounded-md text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 transition-colors duration-150 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                            Picked-up
                        </button>
                        <button type="button" title="Cancel prescription" :disabled="data.status === 'cancelled'" @click="setStatus(data, 'cancelled')"
                            class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                            <BiXCircle size="18" />
                        </button>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { FiSearch } from 'vue-icons-plus/fi';
import { FaFilePrescription, FaCapsules } from 'vue-icons-plus/fa';
import { BiXCircle } from 'vue-icons-plus/bi';
import PrescriptionPaper from '@/components/PrescriptionPaper.vue';
import { usePharmacyStore } from '@/store/Pharmacy';
import { Prescription } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const pharmacyStore = usePharmacyStore();

const statusFilter = ref<string | null>(null);
const statusFilterOptions = [
    { label: 'All statuses', value: null },
    { label: 'Requested', value: 'requested' },
    { label: 'Dispensed', value: 'done' },
    { label: 'Picked-up', value: 'picked-up' },
    { label: 'Cancelled', value: 'cancelled' },
];

const rxModalOpen = ref<boolean>(false);
const rxPrescription = ref<Prescription | null>(null);

const rows = computed<Prescription[]>(() => pharmacyStore.prescriptions);

const { search, rows: perPage, first, total, loading, onPage, onSearch, reload } = useApiTable(
    async (params) => {
        try {
            await pharmacyStore.read({ ...params, status: statusFilter.value || undefined });
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve prescriptions');
        }
    },
    () => pharmacyStore.meta,
);

const patientName = (row: Prescription) => {
    const p = row.patient_case?.patient;
    if (!p) return '';
    return `${p.firstname ?? ''} ${p.lastname ?? ''}`.trim();
};

const statusSeverity = (status?: string) => {
    switch (status) {
        case 'requested': return 'warn';
        case 'done': return 'success';
        case 'picked-up': return 'info';
        case 'cancelled': return 'danger';
        default: return undefined;
    }
};

const formatDateTime = (value?: string | null) => (value ? new Date(value).toLocaleString() : '—');

const openRx = (row: Prescription) => {
    rxPrescription.value = row;
    rxModalOpen.value = true;
};

const statusLabel: Record<string, string> = {
    done: 'dispensed',
    'picked-up': 'picked-up',
    cancelled: 'cancelled',
};

const setStatus = (row: Prescription, status: string) => {
    if (!row.pid) return;
    showConfirm({
        message: `Mark this prescription as ${statusLabel[status] || status}?`,
        header: 'Update Status',
        onAccept: async () => {
            try {
                await pharmacyStore.updateStatus(row.pid as string, status);
                toast.success(`Prescription marked as ${statusLabel[status] || status}`);
                await reload();
            } catch (err: any) {
                toast.error(err.response?.data?.message || 'Failed to update status');
            }
        },
    });
};
</script>
