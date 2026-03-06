<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <Dialog v-model:visible="noteModal" modal header="Nurse's Notes" :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-2">
                <label for="">Focus</label>
                <Textarea v-model="nursesNotesInfo.focus" rows="2" autoResize fluid required />
                <label for="">Data</label>
                <Textarea v-model="nursesNotesInfo.data" rows="2" autoResize fluid required />
                <label for="">Action</label>
                <Textarea v-model="nursesNotesInfo.action" rows="2" autoResize fluid required />
                <label for="">Response</label>
                <Textarea v-model="nursesNotesInfo.response" rows="2" autoResize fluid />
                <Button type="submit" :label="`${isUpdate ? 'Update' : 'Save'}`" fluid />
            </form>
        </Dialog>
        <div class="p-6 border-b border-slate-200 flex justify-between">
            <h3 class="text-lg font-bold text-slate-900">Nurse's Notes</h3>
            <button type="button" @click="noteModal = true;"
                class="flex items-center gap-3 px-4 py-3  rounded-lg transition-all bg-linear-to-r from-emerald-500 to-teal-600 text-white shadow-md">
                <BsPlusCircle size="20" /> Nurse Notes
            </button>
        </div>
        <DataTable :value="nursesNotes" paginator :rows="15" :rowsPerPageOptions="[10, 15, 25, 50, 100, 250, 500]"
            responsiveLayout="scroll" tableStyle="min-width: 50rem">
            <Column field="focus" header="Focus"></Column>
            <Column field="data" header="Data"></Column>
            <Column field="action" header="Action"></Column>
            <Column field="response" header="Response"></Column>
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
import { onMounted, ref, reactive, watch, computed } from 'vue';
import { BsPlusCircle } from 'vue-icons-plus/bs';
import { BiEdit, BiTrash } from 'vue-icons-plus/bi';
import { NursesNotes } from '@/interface/Interfaces';
import { useNursesNotesStore } from '@/store/patientchart/NursesNotes';
import { useConfirmToast } from '@/composables/confirm';
import { useAppToast } from '@/composables/toast';
const { showConfirm } = useConfirmToast();
const toast = useAppToast();
const nursesNotesStore = useNursesNotesStore();
const nursesNotes = computed<NursesNotes[]>(() => nursesNotesStore.nursesNotes);
const nurseNote = computed<NursesNotes>(() => nursesNotesStore.nursesNote);
const nursesNotesInfo = reactive<NursesNotes>({
    pid: "",
    focus: "",
    data: "",
    action: "",
    response: ""
});
const noteModal = ref<boolean>(false);
const isUpdate = ref<boolean>(false);
watch(
    () => noteModal.value,
    (newVal) => {
        if (!newVal) {
            isUpdate.value = false;
            Object.assign(nursesNotesInfo, {
                pid: "",
                focus: "",
                data: "",
                action: "",
                response: ""
            });
        }
    },
    { immediate: true }
)
onMounted(async () => {
    await read();
})
const read = async () => {
    try {
        await nursesNotesStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Doctor's Orders");
    }
}
const create = async () => {
    try {
        await nursesNotesStore.create(nursesNotesInfo);
        toast.success("Nurse's Note created successfully");
        noteModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Nurse's Note");
    }
}
const view = async (pid: string) => {
    try {
        await nursesNotesStore.view(pid);
        Object.assign(nursesNotesInfo, nurseNote.value);
        isUpdate.value = true;
        noteModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Nurse's Notes");
    }
}
const update = async () => {
    try {
        await nursesNotesStore.update(nursesNotesInfo);
        toast.success("Nurse's Note updated successfully");
        noteModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Nurse's Note");
    }

}
const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this record?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await nursesNotesStore.archive(pid);
                toast.success("Nurse's Note deleted successfully");
            } catch (err: any) {
                toast.error(err.response.data.message || "Failed to delete Nurse's Note");
            }
        },
        onReject: () => {
            console.log("Delete cancelled");
        },
    });
}   
</script>
