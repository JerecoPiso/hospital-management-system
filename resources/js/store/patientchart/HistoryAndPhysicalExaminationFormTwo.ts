import { defineStore } from "pinia";
import { ref } from "vue";
import { HistoryAndPhysicalExaminationFormTwo } from "@/interface/Interfaces";
import axios from "axios";
export const useHistoryAndPhysicalExaminationFormTwoStore = defineStore("historyAndPhysicalExaminationFormTwo", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const histories = ref<HistoryAndPhysicalExaminationFormTwo[]>([])
    const history = ref<HistoryAndPhysicalExaminationFormTwo>({
        pid: "",
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
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-two`);
        histories.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-two/${pid}`);
        history.value = response.data.data;
    }
    const update = async (data: HistoryAndPhysicalExaminationFormTwo) => {
        await axios.put(`${baseUrl}api/history-and-physical-examination-form-two/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/history-and-physical-examination-form-two/${pid}`);
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
