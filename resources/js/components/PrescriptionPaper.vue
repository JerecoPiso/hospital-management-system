<template>
    <div class="flex flex-col gap-4">
        <div ref="paperRef" class="rx-paper">
            <!-- Letterhead -->
            <div class="rx-letterhead">
                <div class="rx-clinic">
                    <p class="rx-clinic-name">{{ hospitalName }}</p>
                    <p class="rx-clinic-line">{{ hospitalAddress }}</p>
                    <p class="rx-clinic-line">{{ hospitalContact }}</p>
                </div>
            </div>

            <!-- Patient bar -->
            <div class="rx-patient">
                <div class="rx-field rx-field--grow">
                    <span class="rx-label">Patient</span>
                    <span class="rx-value">{{ patientName }}</span>
                </div>
                <div class="rx-field">
                    <span class="rx-label">Age / Sex</span>
                    <span class="rx-value">{{ ageSex }}</span>
                </div>
                <div class="rx-field">
                    <span class="rx-label">Date</span>
                    <span class="rx-value">{{ prescriptionDate }}</span>
                </div>
            </div>
            <div class="rx-patient rx-patient--sub">
                <div class="rx-field rx-field--grow">
                    <span class="rx-label">Case No.</span>
                    <span class="rx-value">{{ caseNumber }}</span>
                </div>
                <div class="rx-field">
                    <span class="rx-label">Status</span>
                    <span class="rx-value rx-value--caps">{{ prescription?.status || '—' }}</span>
                </div>
            </div>

            <!-- Rx body -->
            <div class="rx-body">
                <div class="rx-symbol">&#8478;</div>

                <ol class="rx-list">
                    <li v-for="(item, idx) in items" :key="idx" class="rx-item">
                        <p class="rx-drug">
                            <span class="rx-drug-name">{{ item.medicine?.name || 'Medicine' }}</span>
                            <span v-if="drugStrength(item)" class="rx-drug-strength">{{ drugStrength(item) }}</span>
                        </p>
                        <p class="rx-sig">
                            <span class="rx-sig-label">Sig:</span>
                            {{ sig(item) || '—' }}
                        </p>
                        <p v-if="item.quantity" class="rx-qty">Dispense: #{{ item.quantity }}</p>
                        <p v-if="item.instructions" class="rx-note">{{ item.instructions }}</p>
                        <p v-if="item.remarks" class="rx-note rx-note--muted">{{ item.remarks }}</p>
                    </li>
                </ol>

                <p v-if="!items.length" class="rx-empty">No medicines on this prescription.</p>
            </div>

            <!-- Remarks + signature -->
            <div class="rx-footer">
                <div class="rx-remarks">
                    <template v-if="prescription?.remarks">
                        <span class="rx-label">Remarks</span>
                        <span class="rx-value">{{ prescription.remarks }}</span>
                    </template>
                </div>
                <div class="rx-sign">
                    <div class="rx-sign-line"></div>
                    <p class="rx-sign-name">{{ doctorName }}</p>
                    <p class="rx-sign-lic">Lic. No. {{ doctorLicense || '__________' }}</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 rx-actions">
            <Button type="button" label="Print" icon="pi pi-print" @click="printRx" class="bg-linear-to-r from-emerald-500 to-teal-600 border-0" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Prescription, PrescriptionItem } from '@/interface/Interfaces';

const props = withDefaults(
    defineProps<{
        prescription: Prescription | null;
        hospitalName?: string;
        hospitalAddress?: string;
        hospitalContact?: string;
    }>(),
    {
        prescription: null,
        hospitalName: 'Hospital Management System',
        hospitalAddress: 'Provincial Government of Leyte',
        hospitalContact: 'Tel. (053) 000-0000',
    }
);

const paperRef = ref<HTMLElement | null>(null);

const patient = computed(() => props.prescription?.patient_case?.patient ?? props.prescription?.patientCase?.patient);
const patientName = computed(() => {
    const p = patient.value;
    if (!p) return '—';
    return `${p.firstname ?? ''} ${p.middlename ? p.middlename + ' ' : ''}${p.lastname ?? ''}`.replace(/\s+/g, ' ').trim() || '—';
});

const ageSex = computed(() => {
    const p = patient.value;
    const parts: string[] = [];
    if (p?.birthdate) {
        const dob = new Date(p.birthdate);
        if (!isNaN(dob.getTime())) {
            const now = new Date();
            let age = now.getFullYear() - dob.getFullYear();
            const m = now.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
            parts.push(`${age}y`);
        }
    }
    if (p?.gender) parts.push(p.gender);
    return parts.join(' / ') || '—';
});

const caseNumber = computed(
    () => props.prescription?.patient_case?.case_number ?? props.prescription?.patientCase?.case_number ?? '—'
);

const prescriptionDate = computed(() => {
    const value = props.prescription?.prescription_date;
    return value ? new Date(value).toLocaleString() : '—';
});

const doctor = computed(() => props.prescription?.doctor);
const doctorName = computed(() => {
    const d = doctor.value;
    if (!d) return 'Attending Physician';
    const name = `${d.firstname ?? ''} ${d.middlename ? d.middlename + ' ' : ''}${d.lastname ?? ''}`.replace(/\s+/g, ' ').trim();
    return name ? `${name}${d.suffix ? ', ' + d.suffix : ''}, M.D.` : 'Attending Physician';
});
const doctorLicense = computed(() => doctor.value?.license_no || '');

const items = computed<PrescriptionItem[]>(() => props.prescription?.items ?? []);

const drugStrength = (item: PrescriptionItem) => {
    const m = item.medicine;
    if (!m) return '';
    return [m.dosage ? `${m.dosage}${m.dosage_unit || ''}` : null, m.form].filter(Boolean).join(' ');
};

const sig = (item: PrescriptionItem) => {
    const duration = item.duration ? `for ${item.duration} ${item.duration_unit || 'days'}`.trim() : null;
    return [item.frequency, duration].filter(Boolean).join(', ');
};

const printRx = () => {
    const node = paperRef.value;
    if (!node) return;
    const win = window.open('', '_blank', 'width=900,height=1000');
    if (!win) return;
    win.document.write(`<!doctype html><html><head><title>Prescription</title><meta charset="utf-8"><style>${printStyles}</style></head><body>${node.outerHTML}</body></html>`);
    win.document.close();
    win.focus();
    win.onafterprint = () => win.close();
    // Give the new window a tick to lay out before printing.
    setTimeout(() => win.print(), 300);
};

const printStyles = `
* { box-sizing: border-box; }
body { margin: 0; padding: 24px; font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; background: #fff; }
.rx-paper { max-width: 720px; margin: 0 auto; border: 1px solid #cbd5e1; border-radius: 10px; padding: 28px 32px; }
.rx-actions { display: none !important; }
.rx-letterhead { text-align: center; border-bottom: 2px solid #0f766e; padding-bottom: 12px; }
.rx-clinic-name { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: .5px; color: #0f766e; }
.rx-clinic-line { margin: 2px 0 0; font-size: 12px; color: #64748b; }
.rx-patient { display: flex; flex-wrap: wrap; gap: 18px; margin-top: 16px; }
.rx-patient--sub { margin-top: 8px; }
.rx-field { display: flex; flex-direction: column; min-width: 120px; }
.rx-field--grow { flex: 1 1 200px; }
.rx-label { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: #94a3b8; }
.rx-value { font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; min-height: 20px; }
.rx-value--caps { text-transform: capitalize; }
.rx-body { position: relative; margin-top: 26px; min-height: 320px; padding-left: 8px; }
.rx-symbol { font-size: 46px; font-weight: 700; font-style: italic; color: #0f766e; line-height: 1; }
.rx-list { list-style: decimal; margin: 14px 0 0; padding-left: 26px; display: flex; flex-direction: column; gap: 16px; }
.rx-item { padding-left: 4px; }
.rx-drug { margin: 0; font-size: 15px; }
.rx-drug-name { font-weight: 700; }
.rx-drug-strength { color: #475569; font-weight: 500; margin-left: 6px; }
.rx-sig { margin: 3px 0 0; font-size: 13px; color: #334155; }
.rx-sig-label { font-style: italic; color: #64748b; margin-right: 4px; }
.rx-qty { margin: 3px 0 0; font-size: 12px; color: #475569; }
.rx-note { margin: 3px 0 0; font-size: 12px; color: #475569; }
.rx-note--muted { color: #94a3b8; font-style: italic; }
.rx-empty { color: #94a3b8; font-style: italic; margin-top: 14px; }
.rx-footer { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; margin-top: 34px; }
.rx-remarks { display: flex; flex-direction: column; max-width: 60%; font-size: 12px; }
.rx-sign { text-align: center; min-width: 220px; }
.rx-sign-line { border-top: 1px solid #1e293b; margin-bottom: 4px; }
.rx-sign-name { margin: 0; font-size: 13px; font-weight: 700; }
.rx-sign-lic { margin: 2px 0 0; font-size: 11px; color: #64748b; }
@media print { body { padding: 0; } .rx-paper { border: none; } }
`;
</script>

<style scoped>
.rx-paper {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 28px 32px;
    color: #1e293b;
    font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.rx-letterhead {
    text-align: center;
    border-bottom: 2px solid #0f766e;
    padding-bottom: 12px;
}
.rx-clinic-name {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #0f766e;
}
.rx-clinic-line {
    margin: 2px 0 0;
    font-size: 12px;
    color: #64748b;
}
.rx-patient {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    margin-top: 16px;
}
.rx-patient--sub {
    margin-top: 8px;
}
.rx-field {
    display: flex;
    flex-direction: column;
    min-width: 120px;
}
.rx-field--grow {
    flex: 1 1 200px;
}
.rx-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #94a3b8;
}
.rx-value {
    font-size: 14px;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 3px;
    min-height: 20px;
}
.rx-value--caps {
    text-transform: capitalize;
}
.rx-body {
    position: relative;
    margin-top: 26px;
    min-height: 300px;
    padding-left: 8px;
}
.rx-symbol {
    font-size: 46px;
    font-weight: 700;
    font-style: italic;
    color: #0f766e;
    line-height: 1;
}
.rx-list {
    list-style: decimal;
    margin: 14px 0 0;
    padding-left: 26px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.rx-drug {
    margin: 0;
    font-size: 15px;
}
.rx-drug-name {
    font-weight: 700;
}
.rx-drug-strength {
    color: #475569;
    font-weight: 500;
    margin-left: 6px;
}
.rx-sig {
    margin: 3px 0 0;
    font-size: 13px;
    color: #334155;
}
.rx-sig-label {
    font-style: italic;
    color: #64748b;
    margin-right: 4px;
}
.rx-qty,
.rx-note {
    margin: 3px 0 0;
    font-size: 12px;
    color: #475569;
}
.rx-note--muted {
    color: #94a3b8;
    font-style: italic;
}
.rx-empty {
    color: #94a3b8;
    font-style: italic;
    margin-top: 14px;
}
.rx-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 24px;
    margin-top: 34px;
}
.rx-remarks {
    display: flex;
    flex-direction: column;
    max-width: 60%;
    font-size: 12px;
}
.rx-sign {
    text-align: center;
    min-width: 220px;
}
.rx-sign-line {
    border-top: 1px solid #1e293b;
    margin-bottom: 4px;
}
.rx-sign-name {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
}
.rx-sign-lic {
    margin: 2px 0 0;
    font-size: 11px;
    color: #64748b;
}
</style>
