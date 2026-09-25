import { defineStore } from "pinia";
import { ref } from "vue";
import { DoctorFee, User } from "@/interface/Interfaces";
import axios from "axios";
export const useDoctorFeeStore = defineStore("doctorFee", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const doctorFees = ref<DoctorFee[]>([])
    const doctors = ref<User[]>([])
    const doctorFee = ref<DoctorFee>({
        patient_case_pid: "",
        doctor_pid: "",
        professional_fee: 0
    })
    const create = async (data: DoctorFee) => {
        await axios.post(`${baseUrl}api/doctor-fees`, data);
    }
    const update = async (data: DoctorFee) => {
        await axios.put(`${baseUrl}api/doctor-fees/${data.pid}`, data);
    }
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/doctor-fees`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        doctorFees.value = response.data.data;
    }
    const readDoctors = async () => {
        const response = await axios.get(`${baseUrl}api/doctor-fees/doctors`);
        doctors.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/doctor-fees/${pid}`);
        doctorFee.value = response.data.data;
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/doctor-fees/${pid}`);
    }
    return {
        create,
        update,
        read,
        readDoctors,
        view,
        archive,
        doctorFees,
        doctors,
        doctorFee
    }
})
