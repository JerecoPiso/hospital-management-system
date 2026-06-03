<template>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <Dialog v-model:visible="noteModal" modal :style="{ width: '48vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '95vw' }"
            :pt="{ header: { class: 'border-b border-slate-100 pb-4' } }">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <BsJournalMedical class="text-white" size="17" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">{{ isUpdate ? 'Edit Note' : 'New Nurse\'s Note' }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ isUpdate ? 'Update the existing note details' : 'Fill in the note details below' }}</p>
                    </div>
                </div>
            </template>
            <form @submit.prevent="isUpdate ? update() : create()" class="flex flex-col gap-5 pt-2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Focus <span class="text-red-400">*</span>
                        </label>
                        <Textarea
                            v-model="nursesNotesInfo.focus"
                            rows="3"
                            autoResize
                            fluid
                            required
                            placeholder="Problem or concern..."
                            class="text-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Data <span class="text-red-400">*</span>
                        </label>
                        <Textarea
                            v-model="nursesNotesInfo.data"
                            rows="3"
                            autoResize
                            fluid
                            required
                            placeholder="Subjective / objective data..."
                            class="text-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">
                            Action <span class="text-red-400">*</span>
                        </label>
                        <Textarea
                            v-model="nursesNotesInfo.action"
                            rows="3"
                            autoResize
                            fluid
                            required
                            placeholder="Nursing interventions taken..."
                            class="text-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-slate-700">Response</label>
                        <Textarea
                            v-model="nursesNotesInfo.response"
                            rows="3"
                            autoResize
                            fluid
                            placeholder="Patient's response..."
                            class="text-sm"
                        />
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <Button
                        type="button"
                        label="Cancel"
                        severity="secondary"
                        outlined
                        fluid
                        @click="noteModal = false"
                    />
                    <Button
                        type="submit"
                        :label="isUpdate ? 'Update Note' : 'Save Note'"
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
                    <h3 class="text-base font-bold text-slate-800">Nurse's Notes</h3>
                    <p class="text-xs text-slate-400">DAR charting — Focus, Data, Action, Response</p>
                </div>
            </div>
            <button
                type="button"
                @click="noteModal = true"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium shadow-md hover:shadow-lg active:scale-95"
            >
                <BsPlusCircle size="16" />
                Add Note
            </button>
        </div>

        <!-- Table -->
        <DataTable
            :value="nursesNotes"
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
                    <p class="text-sm font-medium">No notes recorded</p>
                    <p class="text-xs mt-1">Click "Add Note" to create the first entry</p>
                </div>
            </template>

            <Column field="focus" header="Focus" class="w-44">
                <template #body="{ data }">
                    <p class="text-slate-700 text-sm font-medium leading-relaxed line-clamp-3">{{ data.focus || '—' }}</p>
                </template>
            </Column>

            <Column field="data" header="Data">
                <template #body="{ data }">
                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ data.data || '—' }}</p>
                </template>
            </Column>

            <Column field="action" header="Action">
                <template #body="{ data }">
                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ data.action || '—' }}</p>
                </template>
            </Column>

            <Column field="response" header="Response" class="w-48">
                <template #body="{ data }">
                    <p v-if="data.response" class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ data.response }}</p>
                    <span v-else class="text-slate-300 text-sm italic">None</span>
                </template>
            </Column>

            <Column header="Actions" class="w-24">
                <template #body="{ data }">
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            title="Edit note"
                            @click="view(data.pid)"
                            class="p-1.5 rounded-md text-teal-600 hover:bg-teal-50 hover:text-teal-700 transition-colors duration-150 cursor-pointer"
                        >
                            <BiEdit size="18" />
                        </button>
                        <button
                            type="button"
                            title="Delete note"
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
import { onMounted, ref, reactive, watch, computed } from 'vue';
import { BsPlusCircle, BsJournalMedical } from 'vue-icons-plus/bs';
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
);

onMounted(async () => {
    await read();
});

const read = async () => {
    try {
        await nursesNotesStore.read();
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Nurse's Notes");
    }
};

const create = async () => {
    try {
        await nursesNotesStore.create(nursesNotesInfo);
        toast.success("Nurse's Note created successfully");
        noteModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to create Nurse's Note");
    }
};

const view = async (pid: string) => {
    try {
        await nursesNotesStore.view(pid);
        Object.assign(nursesNotesInfo, nurseNote.value);
        isUpdate.value = true;
        noteModal.value = true;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to retrieve Nurse's Note");
    }
};

const update = async () => {
    try {
        await nursesNotesStore.update(nursesNotesInfo);
        toast.success("Nurse's Note updated successfully");
        noteModal.value = false;
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Failed to update Nurse's Note");
    }
};

const archive = async (pid: string) => {
    showConfirm({
        message: "Are you sure you want to delete this note?",
        header: "Delete Confirmation",
        onAccept: async () => {
            try {
                await nursesNotesStore.archive(pid);
                toast.success("Nurse's Note deleted successfully");
            } catch (err: any) {
                toast.error(err.response?.data?.message || "Failed to delete Nurse's Note");
            }
        },
        onReject: () => {},
    });
};
</script>
