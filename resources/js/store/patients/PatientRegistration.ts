import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientRegistration } from "@/interface/Interfaces";
import axios from "axios";
export const usePatientStore = defineStore("patient", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const patients = ref<PatientRegistration[]>([])
    const patient = ref<PatientRegistration>({
        pid: "",
        firstname: "",
        lastname: "",
        middlename: "",
        suffix: "",
        birthdate: "",
        gender: "",
        civil_status: "",
        contact_number: "",
        email_address: "",
        religion: "",
        birthplace: "",
        occupation: "",
        spouse_name: "",
        admission_datetime: "",
        chief_complaint: "",
        initial_diagnosis: "",
        final_diagnosis: ""
    })
    const create = async (data: PatientRegistration) => {
        await axios.post(`${baseUrl}api/patient`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/patient`);
        patients.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient/${pid}`);
        patient.value = response.data.data;
    }
    const update = async (data: PatientRegistration) => {
        await axios.put(`${baseUrl}api/patient/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/patient/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        patients,
        patient
    }
})
