<template>
    <div v-if="!patientCasePid" class="bg-white rounded-xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center text-slate-400">
        <BsPlusCircle size="40" class="mb-3 opacity-30" />
        <p class="text-sm font-medium">No patient selected</p>
        <p class="text-xs mt-1">Open this page from a patient's chart via the Inpatients or Outpatients list.</p>
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden p-6">
        <Dialog v-model:visible="vitalsModal" modal header="Vital Signs" :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <form @submit.prevent="isUpdate ? update() : create()" class="mt-5">
                <div class="grid grid-cols-4 gap-x-4 gap-y-7 ">
                    <div class="col-span-1">
                        <FloatLabel>
                            <Select v-model="vitalSignInfo.type" id="type" :options="vitalSignTypes" optionLabel="label" optionValue="value" fluid />
                            <label for="type">Type</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <DatePicker v-model="vitalSignInfo.measured_at" id="measured_at" showTime hourFormat="24" fluid />
                            <label for="measured_at">Measured At</label>
                        </FloatLabel>
                    </div>

                    <div class="col-span-4 text-sm font-semibold text-slate-500 uppercase tracking-wide -mb-3">Standard Vitals</div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.systolic" type="number" id="systolic" fluid />
                            <label for="systolic">Systolic</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.diastolic" type="number" id="diastolic" fluid/>
                            <label for="diastolic">Diastolic</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.temperature" id="temperature" type="number" step="0.1" fluid/>
                            <label for="temperature">Temperature</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.heart_rate" type="number" id="heart_rate" fluid />
                            <label for="heart_rate">Heart Rate</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.respiratory_rate" type="number" id="respiratory_rate" fluid />
                            <label for="respiratory_rate">Respiratory Rate</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.oxygen_saturation" type="number" id="oxygen_saturation" fluid/>
                            <label for="oxygen_saturation">Oxygen Saturation</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.weight" type="number" step="0.1" id="weight" fluid />
                            <label for="weight">Weight</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.height" type="number" step="0.1" id="height" fluid />
                            <label for="height">Height</label>
                        </FloatLabel>
                    </div>

                    <div class="col-span-4 text-sm font-semibold text-slate-500 uppercase tracking-wide -mb-3">Anthropometric</div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.bmi" type="number" step="0.1" id="bmi" fluid />
                            <label for="bmi">BMI</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.muac" type="number" step="0.01" id="muac" fluid />
                            <label for="muac">MUAC</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.length" type="number" step="0.01" id="length" fluid />
                            <label for="length">Length</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.z_score" type="number" step="0.01" id="z_score" fluid />
                            <label for="z_score">Z-Score</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.head_circumference" type="number" step="0.01" id="head_circumference" fluid />
                            <label for="head_circumference">Head Circumference</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.abdominal_circumference" type="number" step="0.01" id="abdominal_circumference" fluid />
                            <label for="abdominal_circumference">Abdominal Circumference</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.chest_circumference" type="number" step="0.01" id="chest_circumference" fluid />
                            <label for="chest_circumference">Chest Circumference</label>
                        </FloatLabel>
                    </div>

                    <div class="col-span-4 text-sm font-semibold text-slate-500 uppercase tracking-wide -mb-3">Glasgow Coma Scale</div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.eye_response" type="number" id="eye_response" fluid />
                            <label for="eye_response">Eye Response</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.verbal_response" type="number" id="verbal_response" fluid />
                            <label for="verbal_response">Verbal Response</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.motor_response" type="number" id="motor_response" fluid />
                            <label for="motor_response">Motor Response</label>
                        </FloatLabel>
                    </div>

                    <div class="col-span-4 text-sm font-semibold text-slate-500 uppercase tracking-wide -mb-3">Obstetric</div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.fht" type="number" id="fht" fluid />
                            <label for="fht">Fetal Heart Tone</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <DatePicker v-model="vitalSignInfo.lmp" id="lmp" fluid />
                            <label for="lmp">LMP</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText v-model="vitalSignInfo.aog" type="number" id="aog" fluid />
                            <label for="aog">AOG (weeks)</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <DatePicker v-model="vitalSignInfo.edc" id="edc" fluid />
                            <label for="edc">EDC</label>
                        </FloatLabel>
                    </div>

                    <div class="col-span-4">
                        <FloatLabel>
                            <Textarea v-model="vitalSignInfo.remarks" id="remarks" rows="1" fluid />
                            <label for="remarks">Remarks</label>
                        </FloatLabel>
                    </div>
                </div>
                <Button type="submit" :label="`${isUpdate ? 'Update' : 'Save'}`" class="mt-5" fluid />
            </form>
        </Dialog>
        <div class="pb-8  border-slate-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-900">Vital Signs</h3>
            <div class="flex items-center gap-2">
                <div class="flex items-center bg-slate-100 rounded-lg p-1">
                    <button
                        type="button"
                        @click="viewMode = 'chart'"
                        :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors', viewMode === 'chart' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700']"
                    >
                        <FiActivity size="15" /> Chart
                    </button>
                    <button
                        type="button"
                        @click="viewMode = 'table'"
                        :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors', viewMode === 'table' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700']"
                    >
                        <FiList size="15" /> Table
                    </button>
                </div>
                <button v-if="can('vital-signs', 'create')" type="button" @click="vitalsModal = true;"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                    <BsPlusCircle size="20" /> Vital Signs
                </button>
            </div>
        </div>
        <div v-if="viewMode === 'chart'" class="grid grid-cols-2 gap-4">
            <div class="md:col-span-2 col-span-2 border border-slate-200 rounded-xl p-5 bg-white">
                <div class="flex items-center gap-3 mb-2">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg" style="background:#eff6ff;color:#2a78d6;">
                        <FiThermometer size="18" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-900 leading-tight">Temperature, Pulse &amp; Respiration</p>
                        <p class="text-xs text-slate-400">TPR trend over time</p>
                    </div>
                </div>
                <Chart type="line" :data="tprChartData" :options="chartOptions" class="h-80" />
            </div>
            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-xl p-5 bg-white">
                <div class="flex items-center gap-3 mb-2">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg" style="background:#fdf1ed;color:#eb6834;">
                        <FiHeart size="18" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-900 leading-tight">Blood Pressure</p>
                        <p class="text-xs text-slate-400">Systolic / diastolic trend</p>
                    </div>
                </div>
                <Chart type="line" :data="bloodPressureChartData" :options="chartOptions" class="h-80" />
            </div>

            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-xl p-5 bg-white">
                <div class="flex items-center gap-3 mb-2">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg" style="background:#e9f7f1;color:#1baf7a;">
                        <FiDroplet size="18" />
                    </span>
                    <div>
                        <p class="font-semibold text-slate-900 leading-tight">Oxygen Saturation</p>
                        <p class="text-xs text-slate-400">SpO2 trend over time</p>
                    </div>
                </div>
                <Chart type="line" :data="oxygenSaturationChartData" :options="singleSeriesChartOptions" class="h-80" />
            </div>
        </div>

        <div v-else class="border border-slate-200 rounded-xl overflow-hidden">
            <DataTable
                :value="vitalSigns"
                responsiveLayout="scroll"
                tableStyle="min-width: 60rem"
                :pt="{ table: { class: 'text-sm' }, thead: { class: 'bg-slate-50' }, bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' } }"
            >
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <FiList size="36" class="mb-3 opacity-30" />
                        <p class="text-sm font-medium">No vital signs recorded</p>
                    </div>
                </template>
                <Column header="Type" class="w-28">
                    <template #body="{ data }"><span class="text-slate-700 text-sm uppercase">{{ data.type || "—" }}</span></template>
                </Column>
                <Column header="Measured At" class="w-44">
                    <template #body="{ data }"><span class="text-slate-500 text-sm">{{ data.measured_at ? new Date(data.measured_at).toLocaleString() : "—" }}</span></template>
                </Column>
                <Column header="BP" class="w-24">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.systolic && data.diastolic ? `${data.systolic}/${data.diastolic}` : "—" }}</span></template>
                </Column>
                <Column header="Temp" class="w-20">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.temperature ?? "—" }}</span></template>
                </Column>
                <Column header="HR" class="w-20">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.heart_rate ?? "—" }}</span></template>
                </Column>
                <Column header="RR" class="w-20">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.respiratory_rate ?? "—" }}</span></template>
                </Column>
                <Column header="SpO2" class="w-20">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.oxygen_saturation ?? "—" }}</span></template>
                </Column>
                <Column header="Weight" class="w-20">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.weight ?? "—" }}</span></template>
                </Column>
                <Column header="Remarks">
                    <template #body="{ data }"><span class="text-slate-600 text-sm">{{ data.remarks || "—" }}</span></template>
                </Column>
                <Column header="Actions" class="w-24">
                    <template #body="{ data }">
                        <div class="flex items-center gap-1">
                            <button
                                v-if="can('vital-signs', 'update')"
                                type="button"
                                title="Edit"
                                @click="edit(data.pid)"
                                class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                            >
                                <BiEdit size="18" />
                            </button>
                            <button
                                v-if="can('vital-signs', 'delete')"
                                type="button"
                                title="Delete"
                                @click="remove(data.pid)"
                                class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
                            >
                                <BiTrash size="18" />
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref, reactive, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { FiThermometer, FiHeart, FiDroplet, FiActivity, FiList } from 'vue-icons-plus/fi';
import Chart from 'primevue/chart';
import { VitalSigns } from '@/interface/Interfaces';
import { useVitalSignsStore } from '@/store/patientchart/VitalSigns';
import { useAppToast } from '@/composables/toast';
import { useConfirmToast } from '@/composables/confirm';
import { useVitalSignsChart } from '@/composables/vitalSignsChart';
import { usePermission } from '@/composables/permission';

const route = useRoute();
const toast = useAppToast();
const { showConfirm } = useConfirmToast();
const { can } = usePermission();
const vitalSignsStore = useVitalSignsStore();
const vitalSigns = computed<VitalSigns[]>(() => vitalSignsStore.vitalSigns);
const { tprChartData, bloodPressureChartData, oxygenSaturationChartData, chartOptions, singleSeriesChartOptions } = useVitalSignsChart(vitalSigns);

const patientCasePid = computed(() => route.params.patient_case_pid as string | undefined);
const viewMode = ref<'chart' | 'table'>('chart');

const defaultVitalSignInfo = (): VitalSigns => ({
    pid: "",
    patient_case_pid: "",
    type: "opr",
    measured_at: null,
    systolic: null,
    diastolic: null,
    temperature: null,
    heart_rate: null,
    respiratory_rate: null,
    oxygen_saturation: null,
    weight: null,
    height: null,
    bmi: null,
    muac: null,
    length: null,
    z_score: null,
    head_circumference: null,
    abdominal_circumference: null,
    chest_circumference: null,
    eye_response: null,
    verbal_response: null,
    motor_response: null,
    fht: null,
    lmp: null,
    aog: null,
    edc: null,
    remarks: ""
});
const vitalSignInfo = reactive<VitalSigns>(defaultVitalSignInfo());

const vitalsModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
const vitalSignTypes = ref([
    { label: 'OPR', value: 'opr' },
    { label: 'TPR', value: 'tpr' },
    { label: 'Monitoring', value: 'monitoring' },
]);

watch(
    () => vitalsModal.value,
    (newVal) => {
        if (!newVal) {
            isUpdate.value = false;
            Object.assign(vitalSignInfo, defaultVitalSignInfo());
        }
    },
    { immediate: true }
);

onMounted(async () => {
    if (!patientCasePid.value) return;
    try {
        await vitalSignsStore.read(patientCasePid.value);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Vital Signs");
    }
});

const create = async () => {
    try {
        vitalSignInfo.patient_case_pid = patientCasePid.value || "";
        await vitalSignsStore.create(vitalSignInfo);
        toast.success("Vital Sign recorded successfully");
        vitalsModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to record Vital Sign");
    }
};

const update = async () => {
    try {
        vitalSignInfo.patient_case_pid = patientCasePid.value || "";
        await vitalSignsStore.update(vitalSignInfo);
        toast.success("Vital Sign updated successfully");
        vitalsModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Vital Sign");
    }
};

const edit = async (pid: string) => {
    try {
        await vitalSignsStore.view(pid);
        const record = vitalSignsStore.vitalSign;
        Object.assign(vitalSignInfo, defaultVitalSignInfo(), record, {
            measured_at: record.measured_at ? new Date(record.measured_at) : null,
            lmp: record.lmp ? new Date(record.lmp) : null,
            edc: record.edc ? new Date(record.edc) : null,
        });
        isUpdate.value = true;
        vitalsModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Vital Sign");
    }
};

const remove = (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this vital sign record?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await vitalSignsStore.archive(pid, patientCasePid.value);
                toast.success("Vital Sign deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete Vital Sign");
            }
        },
        onReject: () => {},
    });
};
</script>