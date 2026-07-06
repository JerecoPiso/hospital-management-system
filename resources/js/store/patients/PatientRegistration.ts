import { defineStore } from "pinia";
import { ref } from "vue";
import { PatientRegistration } from "@/interface/Interfaces";
import axios from "axios";
export const usePatientRegistrationStore = defineStore("patientRegistration", () => {
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
        await axios.post(`${baseUrl}api/patient-registration`, data);
        read();
    }
    const read = async () => {
        const response = await axios.get(`${baseUrl}api/patient-registration`);
        patients.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/patient-registration/${pid}`);
        patient.value = response.data.data;
    }
    const update = async (data: PatientRegistration) => {
        await axios.put(`${baseUrl}api/patient-registration/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/patient-registration/${pid}`);
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
