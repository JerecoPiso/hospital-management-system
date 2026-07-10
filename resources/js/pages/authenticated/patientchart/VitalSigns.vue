<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden p-6">
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
            <button type="button" @click="vitalsModal = true;"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> Vital Signs
            </button>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="md:col-span-2 col-span-2 border border-slate-200 rounded-lg p-4">
                <p class="font-semibold">TPR</p>
                <Chart type="line" :data="tprChartData" :options="chartOptions" class="h-80" />
            </div>
            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-lg p-4">
                <p class="font-semibold">Blood Pressure</p>
                <Chart type="line" :data="bloodPressureChartData" :options="chartOptions" class="h-80" />
            </div>

            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-lg p-4">
                <p class="font-semibold">Oxygen Saturation</p>
                <Chart type="line" :data="oxygenSaturationChartData" :options="chartOptions" class="h-80" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref, reactive, watch, computed } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import Chart from 'primevue/chart';
import { VitalSigns } from '@/interface/Interfaces';
import { useVitalSignsStore } from '@/store/patientchart/VitalSigns';
import { useAppToast } from '@/composables/toast';
import { useVitalSignsChart } from '@/composables/vitalSignsChart';

const toast = useAppToast();
const vitalSignsStore = useVitalSignsStore();
const vitalSigns = computed<VitalSigns[]>(() => vitalSignsStore.vitalSigns);
const { tprChartData, bloodPressureChartData, oxygenSaturationChartData, chartOptions } = useVitalSignsChart(vitalSigns);

const defaultVitalSignInfo = (): VitalSigns => ({
    pid: "",
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
    try {
        await vitalSignsStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Vital Signs");
    }
});

const create = async () => {
    try {
        await vitalSignsStore.create(vitalSignInfo);
        toast.success("Vital Sign recorded successfully");
        vitalsModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to record Vital Sign");
    }
};

const update = async () => {
    try {
        await vitalSignsStore.update(vitalSignInfo);
        toast.success("Vital Sign updated successfully");
        vitalsModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Vital Sign");
    }
};
</script>