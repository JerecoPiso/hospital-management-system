import { defineStore } from "pinia";
import { ref } from "vue";
import { HistoryAndPhysicalExaminationFormOne } from "@/interface/Interfaces";
import axios from "axios";
export const useHistoryAndPhysicalExaminationFormOneStore = defineStore("historyAndPhysicalExaminationFormOne", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const histories = ref<HistoryAndPhysicalExaminationFormOne[]>([])
    const history = ref<HistoryAndPhysicalExaminationFormOne>({
        pid: "",
        chief_complaint: "",
        history_of_present_illness: "",
        past_medical_history: "",
        past_medical_history_others: "",
        past_surgical_history: "",
        past_surgical_history_history: "",
        hospitalization_history: "",
        hospitalization_history_others: "",
        medication_history: "",
        medication_history_others: "",
        allergies: "",
        allergies_others: "",
        family_history: "",
        family_history_others: "",
        social_history: "",
        social_history_others: "",
        immunization_history: "",
        immunization_history_others: "",
        review_of_systems: "",
        remarks: ""
    })
    const create = async (data: HistoryAndPhysicalExaminationFormOne) => {
        await axios.post(`${baseUrl}api/history-and-physical-examination-form-one`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-one`);
        histories.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-one/${pid}`);
        history.value = response.data.data;
    }
    const update = async (data: HistoryAndPhysicalExaminationFormOne) => {
        await axios.put(`${baseUrl}api/history-and-physical-examination-form-one/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/history-and-physical-examination-form-one/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        histories,
        history
    }
})
