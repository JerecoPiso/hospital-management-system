<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">Doctor's Orders</h3>
        </div>
        <DataTable :value="orders" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            tableStyle="min-width: 50rem">

            <Column field="order" header="Order"></Column>
            <Column field="progress_notes" header="Progress Notes"></Column>
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
interface Order {
    order: string;
    progress_notes: string;
}
const orders = ref<Order[]>([
    { order: 'Blood Test', progress_notes: 'Blood test performed on 2024-06-01. Results pending.' },
    { order: 'X-Ray', progress_notes: 'X-Ray of chest performed on 2024-06-02. No abnormalities detected.' },
    { order: 'MRI Scan', progress_notes: 'MRI scan performed on 2024-06-03. Results pending.' },
    { order: 'Medication: Antibiotics', progress_notes: 'Antibiotics prescribed on 2024-06-04. Patient to take twice daily.' },
    { order: 'Physical Therapy', progress_notes: 'Physical therapy session scheduled for 2024-06-05.' },
]);
</script>