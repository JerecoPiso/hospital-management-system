import { computed, type ComputedRef, type Ref } from 'vue';
import { VitalSigns } from '@/interface/Interfaces';

const toNumber = (value: unknown): number | null => {
    if (value === null || value === undefined || value === '') return null;
    const parsed = Number(value);
    return Number.isNaN(parsed) ? null : parsed;
};

const formatLabel = (measuredAt: VitalSigns['measured_at']): string => {
    if (!measuredAt) return '';
    const date = new Date(measuredAt);
    if (Number.isNaN(date.getTime())) return '';
    return date.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

export function useVitalSignsChart(vitalSigns: Ref<VitalSigns[]> | ComputedRef<VitalSigns[]>) {
    const sorted = computed(() =>
        [...vitalSigns.value].sort((a, b) => {
            const aTime = a.measured_at ? new Date(a.measured_at).getTime() : 0;
            const bTime = b.measured_at ? new Date(b.measured_at).getTime() : 0;
            return aTime - bTime;
        })
    );

    const labels = computed(() => sorted.value.map((vitalSign) => formatLabel(vitalSign.measured_at)));

    const tprChartData = computed(() => {
        const documentStyle = getComputedStyle(document.documentElement);
        return {
            labels: labels.value,
            datasets: [
                {
                    label: 'Temperature',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.temperature)),
                    fill: true,
                    borderColor: documentStyle.getPropertyValue('--p-teal-500'),
                    tension: 0.4
                },
                {
                    label: 'Heart Rate',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.heart_rate)),
                    fill: false,
                    borderColor: documentStyle.getPropertyValue('--p-red-500'),
                    tension: 0.4
                },
                {
                    label: 'Respiratory Rate',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.respiratory_rate)),
                    fill: false,
                    borderColor: documentStyle.getPropertyValue('--p-blue-500'),
                    tension: 0.4
                }
            ]
        };
    });

    const bloodPressureChartData = computed(() => {
        const documentStyle = getComputedStyle(document.documentElement);
        return {
            labels: labels.value,
            datasets: [
                {
                    label: 'Systolic',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.systolic)),
                    fill: true,
                    borderColor: documentStyle.getPropertyValue('--p-teal-500'),
                    tension: 0.4
                },
                {
                    label: 'Diastolic',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.diastolic)),
                    fill: false,
                    borderColor: documentStyle.getPropertyValue('--p-red-500'),
                    tension: 0.4
                }
            ]
        };
    });

    const oxygenSaturationChartData = computed(() => {
        const documentStyle = getComputedStyle(document.documentElement);
        return {
            labels: labels.value,
            datasets: [
                {
                    label: 'Oxygen Saturation',
                    data: sorted.value.map((vitalSign) => toNumber(vitalSign.oxygen_saturation)),
                    fill: true,
                    borderColor: documentStyle.getPropertyValue('--p-teal-500'),
                    tension: 0.4
                }
            ]
        };
    });

    const chartOptions = computed(() => {
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
    });

    return {
        tprChartData,
        bloodPressureChartData,
        oxygenSaturationChartData,
        chartOptions
    };
}
