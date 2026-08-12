import { defineStore } from "pinia";
import { ref } from "vue";
import { VitalSigns } from "@/interface/Interfaces";
import axios from "axios";
export const useVitalSignsStore = defineStore("vitalSigns", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const vitalSigns = ref<VitalSigns[]>([])
    const vitalSign = ref<VitalSigns>({
        pid: "",
        patient_case_pid: "",
        type: "opr",
        measured_at: null,
        systolic: null,
        diastolic: null,
        temperature: null,
        heart_rate: null,
        respiratory_rate: null,
        oxygen_saturation: null,
        weight: null,
        height: null,
        bmi: null,
        muac: null,
        length: null,
        z_score: null,
        head_circumference: null,
        abdominal_circumference: null,
        chest_circumference: null,
        eye_response: null,
        verbal_response: null,
        motor_response: null,
        fht: null,
        lmp: null,
        aog: null,
        edc: null,
        remarks: ""
    })
    const create = async (data: VitalSigns) => {
        await axios.post(`${baseUrl}api/vital-signs`, data);
        read(data.patient_case_pid);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/vital-signs`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        vitalSigns.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/vital-signs/${pid}`);
        vitalSign.value = response.data.data;
    }
    const update = async (data: VitalSigns) => {
        await axios.put(`${baseUrl}api/vital-signs/${data.pid}`, data);
        read(data.patient_case_pid);
    }
    const archive = async (pid: string, patient_case_pid?: string) => {
        await axios.delete(`${baseUrl}api/vital-signs/${pid}`);
        read(patient_case_pid);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        vitalSigns,
        vitalSign
    }
})
