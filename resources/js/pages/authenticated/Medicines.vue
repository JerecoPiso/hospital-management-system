<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="orderModal" modal :style="{ width: '52vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <GiMedicines class="text-white" size="18" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Medicine' : 'New Medicine' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the medicine details' : 'Fill in the medicine details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Name <span class="text-red-400">*</span>
                        </label>
                        <InputText v-model="medicineInfo.name" placeholder="Medicine name" required fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Generic Name</label>
                        <InputText v-model="medicineInfo.generic_name" placeholder="e.g. Paracetamol" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Brand Name</label>
                        <InputText v-model="medicineInfo.brand_name" placeholder="e.g. Biogesic" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Dosage</label>
                        <InputNumber v-model="medicineInfo.dosage" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Dosage Unit</label>
                        <InputText v-model="medicineInfo.dosage_unit" placeholder="e.g. mg, mL, g" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Form</label>
                        <InputText v-model="medicineInfo.form" placeholder="e.g. Tablet, Capsule, Syrup" fluid class="text-sm" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Administration Route</label>
                        <InputText v-model="medicineInfo.administration_route" placeholder="e.g. Oral, IV, IM" fluid class="text-sm" />
                    </div>
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Price <span class="text-red-400">*</span>
                        </label>
                        <InputNumber v-model="medicineInfo.price" :useGrouping="false" :minFractionDigits="2" placeholder="0.00" fluid class="text-sm" />
                    </div>
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
                        :label="isUpdate ? 'Update Medicine' : 'Save Medicine'"
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
                    <GiMedicines class="text-white" size="20" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Medicines</h3>
                    <p class="text-xs text-slate-400">Manage medicine inventory and pricing</p>
                </div>
            </div>
            <button
                type="button"
                @click="orderModal = true"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
            >
                <BsPlusCircle size="16" />
                Add Medicine
            </button>
        </div>

        <!-- Table -->
        <DataTable
            :value="medicines"
            paginator
            :rows="15"
            :rowsPerPageOptions="[10, 15, 25, 50, 100]"
            responsiveLayout="scroll"
            tableStyle="min-width: 60rem"
            :pt="{
                table: { class: 'text-sm' },
                thead: { class: 'bg-slate-50' },
                bodyRow: { class: 'hover:bg-slate-50 transition-colors duration-150 border-b border-slate-100' },
            }"
        >
            <template #empty>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <GiMedicines size="40" class="mb-3 opacity-30" />
                    <p class="text-sm font-medium">No medicines found</p>
                    <p class="text-xs mt-1">Click "Add Medicine" to create the first entry</p>
                </div>
            </template>

            <Column field="name" header="Name">
                <template #body="{ data }">
                    <span class="text-slate-800 text-sm font-medium">{{ data.name || '—' }}</span>
                </template>
            </Column>

            <Column field="generic_name" header="Generic Name">
                <template #body="{ data }">
                    <span class="text-slate-600 text-sm">{{ data.generic_name || '—' }}</span>
                </template>
            </Column>

            <Column field="brand_name" header="Brand Name">
                <template #body="{ data }">
                    <span class="text-slate-600 text-sm">{{ data.brand_name || '—' }}</span>
                </template>
            </Column>

            <Column header="Dosage" class="w-32">
                <template #body="{ data }">
                    <span v-if="data.dosage" class="text-slate-700 text-sm">
                        {{ data.dosage }}<span v-if="data.dosage_unit" class="text-slate-400 ml-0.5">{{ data.dosage_unit }}</span>
                    </span>
                    <span v-else class="text-slate-300 text-sm italic">—</span>
                </template>
            </Column>

            <Column field="form" header="Form" class="w-32">
                <template #body="{ data }">
                    <span v-if="data.form" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-teal-50 text-teal-700 border border-teal-100">
                        {{ data.form }}
                    </span>
                    <span v-else class="text-slate-300 text-sm italic">—</span>
                </template>
            </Column>

            <Column field="administration_route" header="Route" class="w-28">
                <template #body="{ data }">
                    <span v-if="data.administration_route" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                        {{ data.administration_route }}
                    </span>
                    <span v-else class="text-slate-300 text-sm italic">—</span>
                </template>
            </Column>

            <Column field="price" header="Price" class="w-28">
                <template #body="{ data }">
                    <span class="text-slate-700 text-sm font-medium">
                        ₱{{ Number(data.price).toFixed(2) }}
                    </span>
                </template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            title="Edit medicine"
                            @click="view(data.pid)"
                            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                            <BiEdit size="18" />
                        </button>
                        <button
                            type="button"
                            title="Delete medicine"
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
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { GiMedicines } from 'vue-icons-plus/gi';
import { useMedicineStore } from '@/store/Medicine';
import { Medicines } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from "@/composables/toast";

const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const medicineStore = useMedicineStore();
const orderModal = ref<boolean>(false);
const medicines = computed<Medicines[]>(() => medicineStore.medicines);
const medicine = computed<Medicines>(() => medicineStore.medicine);
const medicineInfo = reactive<Medicines>({
    pid: '',
    name: '',
    generic_name: '',
    brand_name: '',
    dosage: 0,
    dosage_unit: '',
    form: '',
    administration_route: '',
    price: 0
});
const isUpdate = ref<boolean>(false);

watch(
    () => orderModal.value,
    (newVal) => {
        if (!newVal) {
            Object.assign(medicineInfo, {
                pid: '',
                name: '',
                generic_name: '',
                brand_name: '',
                dosage: 0,
                dosage_unit: '',
                form: '',
                administration_route: '',
                price: 0
            });
            isUpdate.value = false;
        }
    }
);

onMounted(async () => {
    await read();
});

const read = async () => {
    try {
        await medicineStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Medicines");
    }
};

const create = async () => {
    try {
        await medicineStore.create(medicineInfo);
        toast.success("Medicine created successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Medicine");
    }
};

const view = async (pid: string) => {
    try {
        await medicineStore.view(pid);
        Object.assign(medicineInfo, medicine.value);
        isUpdate.value = true;
        orderModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Medicine");
    }
};

const update = async () => {
    try {
        await medicineStore.update(medicineInfo);
        toast.success("Medicine updated successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Medicine");
    }
};

const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this medicine?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await medicineStore.archive(pid);
                toast.success("Medicine deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete Medicine");
            }
        },
        onReject: () => {},
    });
};
</script>
