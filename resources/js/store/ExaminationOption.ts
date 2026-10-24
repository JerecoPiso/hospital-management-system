import { defineStore } from "pinia";
import { ref } from "vue";
import { ExaminationOption } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";

export const examinationOptionTypes = [
    { value: "past_medical_history", label: "Past Medical History" },
    { value: "past_surgical_history", label: "Past Surgical History" },
    { value: "hospitalization_history", label: "Hospitalization History" },
    { value: "medication_history", label: "Medication History" },
    { value: "allergies", label: "Allergies" },
    { value: "family_history", label: "Family History" },
    { value: "social_history", label: "Social History" },
    { value: "immunization_history", label: "Immunization History" },
    { value: "general_appearance", label: "General Appearance" },
    { value: "skin", label: "Skin" },
    { value: "heent", label: "HEENT" },
    { value: "neck", label: "Neck" },
    { value: "chest_lungs", label: "Chest / Lungs" },
    { value: "cardiovascular", label: "Cardiovascular" },
    { value: "abdomen", label: "Abdomen" },
    { value: "genitourinary", label: "Genitourinary" },
    { value: "rectal", label: "Rectal" },
    { value: "musculoskeletal", label: "Musculoskeletal" },
    { value: "neurological", label: "Neurological" },
    { value: "psychiatric_mental_status", label: "Psychiatric / Mental Status" },
];

export const useExaminationOptionStore = defineStore("examinationOption", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const items = ref<ExaminationOption[]>([]);
    const item = ref<ExaminationOption>({
        type: "",
        code: "",
        description: "",
        active: true,
    });
    const meta = ref<ApiTableMeta>(emptyMeta());

    const create = async (data: ExaminationOption) => {
        await axios.post(`${baseUrl}api/examination-options`, data);
    };
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/examination-options`, { params });
        items.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    };
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/examination-options/${pid}`);
        item.value = response.data.data;
    };
    const update = async (data: ExaminationOption) => {
        await axios.put(`${baseUrl}api/examination-options/${data.pid}`, data);
    };
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/examination-options/${pid}`);
    };

    return { create, read, view, update, archive, items, item, meta };
});
