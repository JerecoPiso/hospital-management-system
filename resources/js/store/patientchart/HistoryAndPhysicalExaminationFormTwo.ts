import { defineStore } from "pinia";
import { ref } from "vue";
import { HistoryAndPhysicalExaminationFormTwo } from "@/interface/Interfaces";
import axios from "axios";
export const useHistoryAndPhysicalExaminationFormTwoStore = defineStore("historyAndPhysicalExaminationFormTwo", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const histories = ref<HistoryAndPhysicalExaminationFormTwo[]>([])
    const history = ref<HistoryAndPhysicalExaminationFormTwo>({
        pid: "",
        patient_case_pid: "",
        general_appearance: "",
        general_appearance_others: "",
        skin: "",
        skin_others: "",
        heent: "",
        heent_others: "",
        neck: "",
        neck_others: "",
        chest_lungs: "",
        chest_lungs_others: "",
        cardiovascular: "",
        cardiovascular_others: "",
        abdomen: "",
        abdomen_others: "",
        genitourinary: "",
        genitourinary_others: "",
        rectal: "",
        rectal_others: "",
        musculoskeletal: "",
        musculoskeletal_others: "",
        neurological: "",
        neurological_others: "",
        psychiatric_mental_status: "",
        psychiatric_mental_status_others: "",
        assessment_impression: "",
        plan_recommendations: "",
        remarks: ""
    })
    const create = async (data: HistoryAndPhysicalExaminationFormTwo) => {
        await axios.post(`${baseUrl}api/history-and-physical-examination-form-two`, data);
        read(data.patient_case_pid);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-two`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        histories.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-two/${pid}`);
        history.value = response.data.data;
    }
    const update = async (data: HistoryAndPhysicalExaminationFormTwo) => {
        await axios.put(`${baseUrl}api/history-and-physical-examination-form-two/${data.pid}`, data);
        read(data.patient_case_pid);
    }
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/history-and-physical-examination-form-two/${pid}`);
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
