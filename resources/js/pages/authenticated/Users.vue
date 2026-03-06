<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <Dialog v-model:visible="orderModal" modal header="User" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            class="w-2/6">
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-2">
                <label for="" v-if="!isUpdate">Email</label>
                <InputText type="email" v-model="userInfo.email" v-if="!isUpdate" />
                <label for="">Firstname</label>
                <InputText v-model="userInfo.firstname" />
                <label for="">Middlename</label>
                <InputText v-model="userInfo.middlename" />
                <label for="">Lastname</label>
                <InputText v-model="userInfo.lastname" />

                <label for="">Suffix</label>
                <InputText v-model="userInfo.suffix" />
                <label for="">License No</label>
                <InputText v-model="userInfo.license_no" />
                <label for="">Gender</label>
                <Select v-model="userInfo.gender" :options="genders"> </Select>
                <label for="">Date of Birth</label>
                <DatePicker v-model="userInfo.date_of_birth" dateFormat="yy-mm-dd" />
                <label for="" v-if="!isUpdate">Password</label>
                <Password v-model="userInfo.password" fluid v-if="!isUpdate">
                </Password>
                <Button type="submit" :label="`${isUpdate ? 'Update' : 'Save'}`" fluid />
            </form>
        </Dialog>
        <div class="p-6 border-b border-slate-200 flex justify-between">
            <h3 class="text-lg font-bold text-slate-900">User</h3>
            <button type="button" @click="orderModal = true;"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> User
            </button>
        </div>
        <DataTable :value="users" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100, 250, 500]"
            responsiveLayout="scroll" tableStyle="min-width: 50rem">
            <Column field="email" header="Email" />

            <Column field="lastname" header="Lastname" />
            <Column field="firstname" header="Firstname" />
            <Column field="middlename" header="Middlename" />

            <Column field="suffix" header="Suffix" />

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
import Password from 'primevue/password';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { useUserStore } from '@/store/User';
import { User } from '@/interface/Interfaces';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from "@/composables/toast";
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const userStore = useUserStore();
const orderModal = ref<boolean>(false);
const users = computed<User[]>(() => userStore.users);
const user = computed<User>(() => userStore.user);
const genders = computed(() => userStore.genders)
const userInfo = reactive<User>({
    pid: '',
    email: '',
    firstname: '',
    middlename: '',
    lastname: '',
    suffix: '',
    license_no: '',
    gender: '',
    date_of_birth: new Date(),
    password: ''
})
const isUpdate = ref<boolean>(false);

watch(
    () => orderModal.value,
    (newVal) => {
        if (!newVal) {
            Object.assign(userInfo, {
                pid: '',
                email: '',
                firstname: '',
                middlename: '',
                lastname: '',
                suffix: '',
                license_no: '',
                gender: '',
                date_of_birth: new Date(),
                password: ''
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
        await userStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve User");
    }
}
const create = async () => {
    try {
        await userStore.create(userInfo);
        toast.success("User created successfully");
        orderModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Medicine");
    }
}
const view = async (pid: string) => {
    try {
        await userStore.view(pid);
        Object.assign(userInfo, user.value);
        isUpdate.value = true;
        orderModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve User");
    }
}
const update = async () => {
    try {
        await userStore.update(userInfo);
        toast.success("User updated successfully");
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
                await userStore.archive(pid);
                toast.success("User deleted successfully");
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