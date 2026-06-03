<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="orderModal" modal :style="{ width: '48vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BsJournalMedical class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Order' : 'New Doctor\'s Order' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the existing order details' : 'Fill in the order details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">
                        Order <span class="text-red-400">*</span>
                    </label>
                    <Textarea
                        v-model="doctorsOrderInfo.order"
                        rows="4"
                        autoResize
                        fluid
                        required
                        placeholder="Enter physician order here..."
                        class="text-sm"
                    />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700">Progress Notes</label>
                    <Textarea
                        v-model="doctorsOrderInfo.progress_notes"
                        rows="3"
                        autoResize
                        fluid
                        placeholder="Optional progress notes..."
                        class="text-sm"
                    />
                </div>
                <div class="flex gap-2 pt-1">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        outlined
                        fluid
                        @click="orderModal = false"
                    />
                    <Button
                        type="submit"
                        :label="isUpdate ? 'Update Order' : 'Save Order'"
                        fluid
                        class="bg-linear-to-r from-emerald-500 to-teal-600 border-0"
                    />
                </div>
            </form>
        </Dialog>

        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-linear-to-r from-slate-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                    <BsJournalMedical class="text-white" size="18" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Doctor's Orders</h3>
                    <p class="text-xs text-slate-400">Physician-issued treatment directives</p>
                </div>
            </div>
            <button
                type="button"
                @click="orderModal = true"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
            >
                <BsPlusCircle size="16" />
                Add Order
            </button>
        </div>

        <!-- Table -->
        <DataTable
            :value="doctorsOrders"
            paginator
            :rows="15"
            :rowsPerPageOptions="[10, 15, 25, 50, 100]"
            responsiveLayout="scroll"
            tableStyle="min-width: 50rem"
            :pt="{
                table: { class: 'text-sm' },
                thead: { class: 'bg-slate-50' },
                bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' },
            }"
        >
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <BsJournalMedical size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No orders recorded</p>
                    <p class="text-xs mt-1">Click "Add Order" to create the first entry</p>
                </div>
            </template>

            <Column header="Physician" class="w-52">
                <template #body="{ data }">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-linear-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                            {{ data.user?.firstname?.charAt(0) ?? '?' }}
                        </div>
                        <span class="text-slate-700 text-sm font-medium leading-tight">
                            {{ `${data.user?.firstname ?? ''} ${data.user?.middlename ? data.user.middlename + ' ' : ''}${data.user?.lastname ?? ''}`.trim() || '—' }}
                        </span>
                    </div>
                </template>
            </Column>

            <Column field="order" header="Order">
                <template #body="{ data }">
                    <p class="text-slate-700 text-sm leading-relaxed line-clamp-3">{{ data.order || '—' }}</p>
                </template>
            </Column>

            <Column field="progress_notes" header="Progress Notes" class="w-56">
                <template #body="{ data }">
                    <p v-if="data.progress_notes" class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ data.progress_notes }}</p>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            title="Edit order"
                            @click="view(data.pid)"
                            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                            <BiEdit size="18" />
                        </button>
                        <button
                            type="button"
                            title="Delete order"
                            @click="archive(data.pid)"
                            class="p-1.5 rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-150 cursor-pointer"
                        >
                            <BiTrash size="18" />
                        </button>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle, BsJournalMedical } from 'vue-icons-plus/bs';
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
});
const isUpdate = ref<boolean>(false);

watch(
    () => orderModal.value,
    (newVal) => {
        if (!newVal) {
            Object.assign(doctorsOrderInfo, { pid: "", order: "", progress_notes: "" });
            isUpdate.value = false;
        }
    }
);

onMounted(async () => {
    await read();
});

const read = async () => {
    try {
        await doctorsOrderStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Doctor's Orders");
    }
};

const create = async () => {
    try {
        await doctorsOrderStore.create(doctorsOrderInfo);
        toast.success("Doctor's Order created successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Doctor's Order");
    }
};

const view = async (pid: string) => {
    try {
        await doctorsOrderStore.view(pid);
        Object.assign(doctorsOrderInfo, doctorsOrder.value);
        isUpdate.value = true;
        orderModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Doctor's Order");
    }
};

const update = async () => {
    try {
        await doctorsOrderStore.update(doctorsOrderInfo);
        toast.success("Doctor's Order updated successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Doctor's Order");
    }
};

const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this order?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await doctorsOrderStore.archive(pid);
                toast.success("Doctor's Order deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete Doctor's Order");
            }
        },
        onReject: () => {},
    });
};
</script>
