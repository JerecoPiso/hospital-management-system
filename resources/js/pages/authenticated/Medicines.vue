<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <Dialog v-model:visible="orderModal" modal header="Medicines"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" class="w-2/6">
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-2">
                <label for="">Name</label>
                <InputText v-model="medicineInfo.name" />
                <label for="">Generic Name</label>
                <InputText v-model="medicineInfo.generic_name" />
                <label for="">Brand Name</label>
                <InputText v-model="medicineInfo.brand_name" />
                <label for="">Dosage</label>
                <InputNumber v-model="medicineInfo.dosage" :useGrouping="false" :minFractionDigits="2" />
                <label for="">Dosage Unit</label>
                <InputText v-model="medicineInfo.dosage_unit" />
                <label for="">Form</label>
                <InputText v-model="medicineInfo.form" />
                <label for="">Administration Route</label>
                <InputText v-model="medicineInfo.administration_route" />
                <label for="">Price</label>
                <InputNumber v-model="medicineInfo.price" :useGrouping="false" :minFractionDigits="2" />
                <Button type="submit" :label="`${isUpdate ? 'Update' : 'Save'}`" fluid />
            </form>
        </Dialog>
        <div class="p-6 border-b border-slate-200 flex justify-between">
            <h3 class="text-lg font-bold text-slate-900">Medicines</h3>
            <button type="button" @click="orderModal = true;"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> Medicine
            </button>
        </div>
        <DataTable :value="medicines" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100, 250, 500]"
            responsiveLayout="scroll" tableStyle="min-width: 50rem">
            <Column field="name" header="Name"></Column>
            <Column field="generic_name" header="Generic Name"></Column>
            <Column field="brand_name" header="Brand Name"></Column>
            <Column field="dosage" header="Dosage"></Column>
            <Column field="dosage_unit" header="Dosage Unit"></Column>
            <Column field="form" header="Form"></Column>
            <Column field="administration_route" header="Administration Route"></Column>
            <Column field="price" header="Price"></Column>
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
})
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
)
onMounted(async () => {
    await read();
})
const read = async () => {
    try {
        await medicineStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Medicines");
    }
}
const create = async () => {
    try {
        await medicineStore.create(medicineInfo);
        toast.success("Medicine created successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Medicine");
    }
}
const view = async (pid: string) => {
    try {
        await medicineStore.view(pid);
        Object.assign(medicineInfo, medicine.value);
        isUpdate.value = true;
        orderModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Medicines");
    }
}
const update = async () => {
    try {
        await medicineStore.update(medicineInfo);
        toast.success("Medicine updated successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Medicine");
    }

}
const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this record?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await medicineStore.archive(pid);
                toast.success("Medicine deleted successfully");
            } catch (err: any) {
                toast.error(err.response.data.message || "Failed to delete Medicine");
            }
        },
        onReject: () => {
            console.log("Delete cancelled");
        },
    });
}   
</script>