import { defineStore } from "pinia";
import { ref } from "vue";
import { ExaminationOption, HistoryAndPhysicalExaminationFormTwo } from "@/interface/Interfaces";
import axios from "axios";
export const useHistoryAndPhysicalExaminationFormTwoStore = defineStore("historyAndPhysicalExaminationFormTwo", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const histories = ref<HistoryAndPhysicalExaminationFormTwo[]>([])
    const history = ref<HistoryAndPhysicalExaminationFormTwo>({
        pid: "",
        patient_case_pid: "",
        findings: [],
        general_appearance_others: "",
        skin_others: "",
        heent_others: "",
        neck_others: "",
        chest_lungs_others: "",
        cardiovascular_others: "",
        abdomen_others: "",
        genitourinary_others: "",
        rectal_others: "",
        musculoskeletal_others: "",
        neurological_others: "",
        psychiatric_mental_status_others: "",
        assessment_impression: "",
        plan_recommendations: "",
        remarks: ""
    })
    const examinationOptions = ref<ExaminationOption[]>([])
    const readOptions = async () => {
        const response = await axios.get(`${baseUrl}api/history-and-physical-examination-form-two/options`);
        examinationOptions.value = response.data.data;
    }
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
        readOptions,
        view,
        update,
        histories,
        history,
        examinationOptions
    }
})
