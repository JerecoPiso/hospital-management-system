<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <Dialog v-model:visible="orderModal" modal header="Doctor's Order" :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <form action="">
                <label for="">Order</label>
                <Textarea rows="5" autoResize fluid required />
                <label for="">Progress Notes</label>
                <Textarea rows="5" autoResize fluid />
                <Button type="submit" label="Save" fluid />
            </form>
        </Dialog>
        <div class="p-6 border-b border-slate-200 flex justify-between">
            <h3 class="text-lg font-bold text-slate-900">Doctor's Orders</h3>
            <button type="button" @click="orderModal = true"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> Order
            </button>
        </div>
        <DataTable :value="orders" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            tableStyle="min-width: 50rem">

            <Column field="order" header="Order"></Column>
            <Column field="progress_notes" header="Progress Notes"></Column>
            <Column header="Actions" class="w-28">
                <template #body="{ data }">
                    <div class="flex gap-1">
                        <BiEdit class="text-teal-600" size="22" />
                        <BiTrash @click="deleteOrder()" class="text-red-600" size="22" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useConfirmToast } from '@/composables/confirm';
import axios from 'axios';
interface Order {
    order: string;
    progress_notes: string;
}
const { showConfirm } = useConfirmToast();
const orders = ref<Order[]>([
    { order: 'Blood Test', progress_notes: 'Blood test performed on 2024-06-01. Results pending.' },
    { order: 'X-Ray', progress_notes: 'X-Ray of chest performed on 2024-06-02. No abnormalities detected.' },
    { order: 'MRI Scan', progress_notes: 'MRI scan performed on 2024-06-03. Results pending.' },
    { order: 'Medication: Antibiotics', progress_notes: 'Antibiotics prescribed on 2024-06-04. Patient to take twice daily.' },
    { order: 'Physical Therapy', progress_notes: 'Physical therapy session scheduled for 2024-06-05.' },
]);
const orderModal = ref<boolean>(false)
const baseUrl = import.meta.env.VITE_APP_URL;

onMounted(async () => {
    await getOrders()
})
const getOrders = async () => {
    try {
        const response = await axios.get(`${baseUrl}api/doctors-order`)
        console.log(response.data)
    } catch (err: any) {
        console.error("Error: ", err)
    }
}
const deleteOrder = () => {
    showConfirm({
        message: "Are you sure you want to delete this record?",
        header: "Delete Confirmation",
        onAccept: async () => {
            alert("g")
        },
        onReject: () => {
            console.log("Delete cancelled");
        },
    });
}
</script>