import { defineStore } from "pinia";
import { ref } from "vue";
import { Invoice } from "@/interface/Interfaces";
import axios from "axios";
export const useInvoiceStore = defineStore("invoice", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const invoices = ref<Invoice[]>([])
    const invoice = ref<Invoice>({
        patient_case_pid: "",
    })
    const read = async (patient_case_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/invoices`, {
            params: patient_case_pid ? { patient_case_pid } : {}
        });
        invoices.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/invoices/${pid}`);
        invoice.value = response.data.data;
    }
    const generate = async (patient_case_pid: string) => {
        const response = await axios.post(`${baseUrl}api/invoices/generate`, { patient_case_pid });
        return response.data.data.invoice as Invoice;
    }
    const pay = async (pid: string, data: { amount_paid: number | string; payment_method: string; reference_number?: string | null; paid_at?: string | null }) => {
        const response = await axios.post(`${baseUrl}api/invoices/${pid}/payments`, data);
        return response.data.data.invoice as Invoice;
    }
    return {
        read,
        view,
        generate,
        pay,
        invoices,
        invoice
    }
})
