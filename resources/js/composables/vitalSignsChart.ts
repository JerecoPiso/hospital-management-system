import { computed, type ComputedRef, type Ref } from 'vue';
import { VitalSigns } from '@/interface/Interfaces';

const PALETTE = {
    blue: '#2a78d6',
    orange: '#eb6834',
    aqua: '#1baf7a'
};

const INK = {
    primary: '#0b0b0b',
    secondary: '#52514e',
    muted: '#898781',
    grid: '#e1e0d9'
};

const FONT_FAMILY = "system-ui, -apple-system, 'Segoe UI', sans-serif";

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

const withAlpha = (hex: string, alpha: number): string => {
    const normalized = hex.replace('#', '');
    const r = parseInt(normalized.substring(0, 2), 16);
    const g = parseInt(normalized.substring(2, 4), 16);
    const b = parseInt(normalized.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const createAreaGradient = (ctx: CanvasRenderingContext2D, chartArea: { top: number; bottom: number }, color: string) => {
    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, withAlpha(color, 0.3));
    gradient.addColorStop(1, withAlpha(color, 0));
    return gradient;
};

const buildLineDataset = (label: string, data: (number | null)[], color: string, unit: string, filled = false) => ({
    label,
    data,
    unit,
    fill: filled ? 'start' : false,
    backgroundColor: filled
        ? (context: any) => {
              const { chart } = context;
              const { ctx, chartArea } = chart;
              if (!chartArea) return withAlpha(color, 0.15);
              return createAreaGradient(ctx, chartArea, color);
          }
        : withAlpha(color, 0.1),
    borderColor: color,
    borderWidth: 2,
    tension: 0.35,
    pointStyle: 'circle',
    pointRadius: 0,
    pointHoverRadius: 5,
    pointHitRadius: 12,
    pointBackgroundColor: color,
    pointBorderColor: '#ffffff',
    pointBorderWidth: 2
});

export function useVitalSignsChart(vitalSigns: Ref<VitalSigns[]> | ComputedRef<VitalSigns[]>) {
    const sorted = computed(() =>
        [...vitalSigns.value].sort((a, b) => {
            const aTime = a.measured_at ? new Date(a.measured_at).getTime() : 0;
            const bTime = b.measured_at ? new Date(b.measured_at).getTime() : 0;
            return aTime - bTime;
        })
    );

    const labels = computed(() => sorted.value.map((vitalSign) => formatLabel(vitalSign.measured_at)));

    const tprChartData = computed(() => ({
        labels: labels.value,
        datasets: [
            buildLineDataset('Temperature', sorted.value.map((v) => toNumber(v.temperature)), PALETTE.blue, '°C', true),
            buildLineDataset('Heart Rate', sorted.value.map((v) => toNumber(v.heart_rate)), PALETTE.orange, 'bpm'),
            buildLineDataset('Respiratory Rate', sorted.value.map((v) => toNumber(v.respiratory_rate)), PALETTE.aqua, 'breaths/min')
        ]
    }));

    const bloodPressureChartData = computed(() => ({
        labels: labels.value,
        datasets: [
            buildLineDataset('Systolic', sorted.value.map((v) => toNumber(v.systolic)), PALETTE.blue, 'mmHg', true),
            buildLineDataset('Diastolic', sorted.value.map((v) => toNumber(v.diastolic)), PALETTE.orange, 'mmHg')
        ]
    }));

    const oxygenSaturationChartData = computed(() => ({
        labels: labels.value,
        datasets: [buildLineDataset('Oxygen Saturation', sorted.value.map((v) => toNumber(v.oxygen_saturation)), PALETTE.blue, '%', true)]
    }));

    const tooltip = {
        enabled: true,
        mode: 'index' as const,
        intersect: false,
        backgroundColor: INK.primary,
        titleColor: '#ffffff',
        bodyColor: 'rgba(255, 255, 255, 0.85)',
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
        padding: 10,
        cornerRadius: 8,
        boxPadding: 6,
        usePointStyle: true,
        titleFont: { size: 12, weight: 600, family: FONT_FAMILY },
        bodyFont: { size: 12, family: FONT_FAMILY },
        callbacks: {
            label: (context: any) => {
                const unit = context.dataset.unit ? ` ${context.dataset.unit}` : '';
                const value = context.parsed.y;
                return `${context.dataset.label}: ${value === null || value === undefined ? '—' : value + unit}`;
            }
        }
    };

    const scales = {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: INK.muted, font: { size: 11, family: FONT_FAMILY }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
        },
        y: {
            grid: { color: INK.grid, drawTicks: false },
            border: { display: false },
            ticks: { color: INK.muted, font: { size: 11, family: FONT_FAMILY }, padding: 8 }
        }
    };

    const chartOptions = computed(() => ({
        maintainAspectRatio: false,
        interaction: { mode: 'index' as const, intersect: false },
        plugins: {
            legend: {
                position: 'top' as const,
                align: 'end' as const,
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    boxWidth: 8,
                    boxHeight: 8,
                    padding: 16,
                    color: INK.secondary,
                    font: { size: 12, family: FONT_FAMILY },
                    generateLabels: (chart: any) =>
                        (chart.data.datasets || []).map((dataset: any, index: number) => ({
                            text: dataset.label,
                            fillStyle: dataset.borderColor,
                            strokeStyle: dataset.borderColor,
                            pointStyle: 'circle',
                            hidden: !chart.isDatasetVisible(index),
                            datasetIndex: index
                        }))
                }
            },
            tooltip
        },
        scales
    }));

    const singleSeriesChartOptions = computed(() => ({
        ...chartOptions.value,
        plugins: {
            ...chartOptions.value.plugins,
            legend: { display: false }
        }
    }));

    return {
        tprChartData,
        bloodPressureChartData,
        oxygenSaturationChartData,
        chartOptions,
        singleSeriesChartOptions
    };
}
