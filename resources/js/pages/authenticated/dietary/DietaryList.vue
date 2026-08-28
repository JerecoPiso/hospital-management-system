<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <BiFoodMenu class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Dietary List</h3>
                    <p class="text-xs text-slate-400">Patients and their currently assigned diets</p>
                </div>
            </div>
            <div class="relative w-full sm:w-64">
                <FiSearch class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 z-10" size="16" />
                <InputText v-model="search" @input="onSearch" placeholder="Search patient / diet . . ." class="w-full text-sm pl-8!" />
            </div>
        </div>

        <!-- Table -->
        <DataTable :value="items" lazy paginator :rows="rows" :first="first" :totalRecords="total" :loading="loading" @page="onPage" :rowsPerPageOptions="[10, 15, 25, 50, 100]" responsiveLayout="scroll" tableStyle="min-width: 65rem"
            :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }">
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <BiFoodMenu size="38" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No dietary assignments found</p>
                    <p class="text-xs mt-1">Assign diets from a patient's chart</p>
                </div>
            </template>

            <Column header="Patient">
                <template #body="{ data }">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                            {{ patientName(data).charAt(0).toUpperCase() || '?' }}
                        </div>
                        <div>
                            <p class="text-slate-800 text-sm font-medium leading-tight">{{ patientName(data) || '—' }}</p>
                            <p class="text-slate-400 text-xs mt-0.5">{{ data.patient_case?.patient?.medical_record_number || '—' }}</p>
                        </div>
                    </div>
                </template>
            </Column>

            <Column header="Case Number" class="w-40">
                <template #body="{ data }">
                    <span class="text-slate-600 text-sm">{{ data.patient_case?.case_number || '—' }}</span>
                </template>
            </Column>

            <Column header="Diet" class="w-48">
                <template #body="{ data }">
                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                        {{ data.diet?.name || '—' }}
                    </span>
                </template>
            </Column>

            <Column header="Remarks">
                <template #body="{ data }">
                    <span class="text-slate-500 text-sm">{{ data.remarks || '—' }}</span>
                </template>
            </Column>

            <Column header="Set By" class="w-44">
                <template #body="{ data }">
                    <span class="text-slate-600 text-sm">{{ userName(data.user) || '—' }}</span>
                </template>
            </Column>

            <Column header="Servings" class="w-28">
                <template #body="{ data }">
                    <span class="text-slate-700 text-sm font-medium">{{ data.diets_served_count ?? data.diets_served?.length ?? 0 }}</span>
                </template>
            </Column>

            <Column header="Last Served" class="w-44">
                <template #body="{ data }">
                    <span class="text-slate-500 text-sm">{{ lastServed(data) }}</span>
                </template>
            </Column>

            <Column header="Actions" class="w-16">
                <template #body="{ data }">
                    <button type="button" title="Open patient chart" :disabled="!data.patient_case?.pid" @click="viewChart(data.patient_case.pid)"
                        class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                        <FiEye size="18" />
                    </button>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { BiFoodMenu } from 'vue-icons-plus/bi';
import { FiSearch, FiEye } from 'vue-icons-plus/fi';
import { usePatientCaseDietStore } from '@/store/patientchart/PatientCaseDiet';
import { PatientCaseDiet, User } from '@/interface/Interfaces';
import { useApiTable } from '@/composables/apiTable';
import { useAppToast } from '@/composables/toast';

const router = useRouter();
const toast = useAppToast();
const patientCaseDietStore = usePatientCaseDietStore();

const items = computed<PatientCaseDiet[]>(() => patientCaseDietStore.patientCaseDiets);

const { search, rows, first, total, loading, onPage, onSearch } = useApiTable(
    async (params) => {
        try {
            await patientCaseDietStore.read(params);
        } catch (err: any) {
            toast.error(err.response?.data?.message || 'Failed to retrieve dietary list');
        }
    },
    () => patientCaseDietStore.meta,
);

const patientName = (row: PatientCaseDiet) => {
    const p = row.patient_case?.patient;
    if (!p) return '';
    return `${p.firstname ?? ''} ${p.lastname ?? ''}`.trim();
};

const userName = (user?: User) => {
    if (!user) return '';
    return `${user.firstname ?? ''} ${user.lastname ?? ''}`.trim();
};

const lastServed = (row: PatientCaseDiet) => {
    const first = row.diets_served?.[0];
    const value = first?.served_at || first?.created_at;
    return value ? new Date(value).toLocaleString() : '—';
};

const viewChart = (patientCasePid: string) => {
    router.push({ name: 'PatientInformation', params: { patient_case_pid: patientCasePid } });
};
</script>
