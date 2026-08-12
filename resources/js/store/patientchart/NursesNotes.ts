import { defineStore } from "pinia";
import { ref } from "vue";
import { NursesNotes } from "@/interface/Interfaces";
import axios from "axios";
export const useNursesNotesStore = defineStore("nursesNotes", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const nursesNotes = ref<NursesNotes[]>([])
    const nursesNote = ref<NursesNotes>({
        pid: "",
        patient_case_pid: "",
        focus: "",
        data: "",
        action: "",
        response: ""
    })
    const create = async (data: NursesNotes) => {
        await axios.post(`${baseUrl}api/nurses-notes`, data);
        read(data.patient_case_pid);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/nurses-notes`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        nursesNotes.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/nurses-notes/${pid}`);
        nursesNote.value = response.data.data;
    }
    const update = async (data: NursesNotes) => {
        await axios.put(`${baseUrl}api/nurses-notes/${data.pid}`, data);
        read(data.patient_case_pid);
    }
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/nurses-notes/${pid}`);
        read(patient_case_pid);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        nursesNotes,
        nursesNote
    }
})