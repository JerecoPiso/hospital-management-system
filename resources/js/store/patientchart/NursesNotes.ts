import { defineStore } from "pinia";
import { ref } from "vue";
import { NursesNotes } from "@/interface/Interfaces";
import axios from "axios";
export const useNursesNotesStore = defineStore("nursesNotes", () => {
    const baseUrl = import.meta.env.VITE_APP_URL;
    const nursesNotes = ref<NursesNotes[]>([])
    const nursesNote = ref<NursesNotes>({
        pid: "",
        focus: "",
        data: "",
        action: "",
        response: ""
    })
    const create = async (data: NursesNotes) => {
        await axios.post(`${baseUrl}api/nurses-notes`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/nurses-notes`);
        nursesNotes.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/nurses-notes/${pid}`);
        nursesNote.value = response.data.data;
    }
    const update = async (data: NursesNotes) => {
        await axios.put(`${baseUrl}api/nurses-notes/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/nurses-notes/${pid}`);
        read();
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