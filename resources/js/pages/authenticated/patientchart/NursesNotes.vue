<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">Nurse's Notes</h3>
        </div>
        <DataTable :value="nursesNotes" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            tableStyle="min-width: 50rem">
            <Column field="focus" header="Focus"></Column>
            <Column field="data" header="Data"></Column>
            <Column header="Actions" class="w-40">
                <template #body="{ data }">

                </template>
            </Column>
        </DataTable>
    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
interface NursesNote {
    focus: string;
    data: String
}
const nursesNotes = ref<NursesNote[]>([
    { focus: 'Blood Test', data: 'Blood test performed on 2024-06-01. Results pending.' },
    { focus: 'X-Ray', data: 'X-Ray of chest performed on 2024-06-02. No abnormalities detected.' },
    { focus: 'MRI Scan', data: 'MRI scan performed on 2024-06-03. Results pending.' },
    { focus: 'Medication: Antibiotics', data: 'Antibiotics prescribed on 2024-06-04. Patient to take twice daily.' },
    { focus: 'Physical Therapy', data: 'Physical therapy session scheduled for 2024-06-05.' },
]);
</script>