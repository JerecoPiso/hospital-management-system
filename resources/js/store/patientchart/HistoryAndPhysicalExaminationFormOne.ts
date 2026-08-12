import { defineStore } from "pinia";
import { ref } from "vue";
import { HistoryAndPhysicalExaminationFormOne } from "@/interface/Interfaces";
import axios from "axios";
export const useHistoryAndPhysicalExaminationFormOneStore = defineStore("historyAndPhysicalExaminationFormOne", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const histories = ref<HistoryAndPhysicalExaminationFormOne[]>([])
    const history = ref<HistoryAndPhysicalExaminationFormOne>({
        pid: "",
        patient_case_pid: "",
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
        read(data.patient_case_pid);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-one`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        histories.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-one/${pid}`);
        history.value = response.data.data;
    }
    const update = async (data: HistoryAndPhysicalExaminationFormOne) => {
        await axios.put(`${baseUrl}api/history-and-physical-examination-form-one/${data.pid}`, data);
        read(data.patient_case_pid);
    }
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/history-and-physical-examination-form-one/${pid}`);
        read(patient_case_pid);
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
