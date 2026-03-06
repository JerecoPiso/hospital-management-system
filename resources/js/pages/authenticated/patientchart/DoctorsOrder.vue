<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <Dialog v-model:visible="orderModal" modal header="Doctor's Order" :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-2">
                <label for="">Order</label>
                <Textarea v-model="doctorsOrderInfo.order" rows="3" autoResize fluid required />
                <label for="">Progress Notes</label>
                <Textarea v-model="doctorsOrderInfo.progress_notes" rows="2" autoResize fluid />
                <Button type="submit" :label="`${isUpdate ? 'Update' : 'Save'}`" fluid />
            </form>
        </Dialog>
        <div class="p-6 border-b border-slate-200 flex justify-between">
            <h3 class="text-lg font-bold text-slate-900">Doctor's Orders</h3>
            <button type="button" @click="orderModal = true;"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> Order
            </button>
        </div>
        <DataTable :value="doctorsOrders" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100, 250, 500]"
            responsiveLayout="scroll" tableStyle="min-width: 50rem">
            <Column header="Physician" class="w-48">
                <template #body="{ data }">
                    {{ `${data.user?.firstname} ${data.user?.middlename || ''} ${data.user?.lastname}` }}
                </template>
            </Column>
            <Column field="order" header="Order"></Column>
            <Column field="progress_notes" header="Progress Notes"></Column>
            <Column header="Actions" class="w-28">
                <template #body="{ data }">
                    <div class="flex gap-1">
                        <BiEdit @click="view(data.pid)" class="text-teal-600" size="22" />
                        <BiTrash @click="archive(data.pid)" class="text-red-600" size="22" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useDoctorsOrderStore } from '@/store/patientchart/DoctorsOrder';
import { DoctorsOrder } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from "@/composables/toast";
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const doctorsOrderStore = useDoctorsOrderStore();
const orderModal = ref<boolean>(false);
const doctorsOrders = computed<DoctorsOrder[]>(() => doctorsOrderStore.doctorsOrders);
const doctorsOrder = computed<DoctorsOrder>(() => doctorsOrderStore.doctorsOrder);
const doctorsOrderInfo = reactive<DoctorsOrder>({
    pid: "",
    order: "",
    progress_notes: ""
})
const isUpdate = ref<boolean>(false);
watch(
    () => orderModal.value,
    (newVal) => {
        if (!newVal) {
            Object.assign(doctorsOrderInfo, {
                pid: "",
                order: "",
                progress_notes: ""
            });
            isUpdate.value = false;
        }
    }
)
onMounted(async () => {
    await read();
})
const read = async () => {
    try {
        await doctorsOrderStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Doctor's Orders");
    }
}
const create = async () => {
    try {
        await doctorsOrderStore.create(doctorsOrderInfo);
        toast.success("Doctor's Order created successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Doctor's Order");
    }
}
const view = async (pid: string) => {
    try {
        await doctorsOrderStore.view(pid);
        Object.assign(doctorsOrderInfo, doctorsOrder.value);
        isUpdate.value = true;
        orderModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Doctor's Orders");
    }
}
const update = async () => {
    try {
        await doctorsOrderStore.update(doctorsOrderInfo);
        toast.success("Doctor's Order updated successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Doctor's Order");
    }

}
const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this record?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await doctorsOrderStore.archive(pid);
                toast.success("Doctor's Order deleted successfully");
            } catch (err: any) {
                toast.error(err.response.data.message || "Failed to delete Doctor's Order");
            }
        },
        onReject: () => {
            console.log("Delete cancelled");
        },
    });
}   
</script>