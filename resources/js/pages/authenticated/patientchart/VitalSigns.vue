<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden p-6">
        <Dialog v-model:visible="vitalsModal" modal header="Vital Signs" :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <form action="" class="mt-5">
                <div class="grid grid-cols-4 gap-x-4 gap-y-7 ">
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" id="systolic" fluid />
                            <label for="systolic">Systolic</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" id="diastolic" fluid/>
                            <label for="diastolic">Diastolic</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText id="temperature" step="0.1" fluid/>
                            <label for="temperature">Temperature</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" id="heart_rate" fluid />
                            <label for="heart_rate">Heart Rate</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" id="respiratory_rate" fluid />
                            <label for="respiratory_rate">Respiratory Rate</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" id="oxygen_saturation" fluid/>
                            <label for="oxygen_saturation">Oxygen Saturation</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" step="0.1" id="weight" fluid />
                            <label for="weight">Weight</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" step="0.1" id="height" fluid />
                            <label for="height">Height</label>
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
                <Chart type="line" :data="tprChartData" :options="chartOptions" class="h-120" />
            </div>
            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-lg p-4">
                <p class="font-semibold">Blood Pressure</p>
                <Chart type="line" :data="chartData" :options="chartOptions" class="h-120" />
            </div>

            <div class="md:col-span-1 col-span-2 border border-slate-200 rounded-lg p-4">
                <p class="font-semibold">Oxygen Saturation</p>
                <Chart type="line" :data="chartData" :options="chartOptions" class="h-120" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref, reactive, watch, computed } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import Chart from 'primevue/chart';
const chartData = ref();
const tprChartData = ref();
const chartOptions = ref();
const vitalsModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
onMounted(() => {
    chartData.value = setChartData();
    tprChartData.value = setTprChartData();
    chartOptions.value = setChartOptions();
});
const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: 'Systolic',
                data: [65, 59, 80, 81, 56, 55, 40],
                fill: true,
                borderColor: documentStyle.getPropertyValue('--p-teal-500'),
                tension: 0.4
            },
            {
                label: 'Diastolic',
                data: [28, 48, 40, 19, 86, 27, 90],
                fill: false,
                borderColor: documentStyle.getPropertyValue('--p-red-500'),
                tension: 0.4
            }
        ]
    };
};
const setTprChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                label: 'Temperature',
                data: [65, 59, 80, 81, 56, 55, 40],
                fill: true,
                borderColor: documentStyle.getPropertyValue('--p-teal-500'),
                tension: 0.4
            },
            {
                label: 'Heart Rate',
                data: [28, 48, 40, 19, 86, 27, 90],
                fill: false,
                borderColor: documentStyle.getPropertyValue('--p-red-500'),
                tension: 0.4
            },
            {
                label: 'Respiratory Rate',
                data: [17, 48, 77, 99, 55, 27, 33],
                fill: false,
                borderColor: documentStyle.getPropertyValue('--p-blue-500'),
                tension: 0.4
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}
</script>