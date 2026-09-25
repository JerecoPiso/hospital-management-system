import { defineStore } from "pinia";
import { ref } from "vue";
import { DispenseMedicineStocksReport, PatientInvoiceReport } from "@/interface/Interfaces";
import axios from "axios";
export const useReportStore = defineStore("report", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const dispenseMedicineStocks = ref<DispenseMedicineStocksReport>({
        rows: [],
        totals: { out_pcs: 0, sold_amount: 0, doctor_fee: 0, grand_total: 0 }
    })
    const patientInvoice = ref<PatientInvoiceReport>({
        cases: [],
        totals: { by_type: [], by_section: [], discount: 0, tax: 0, grand_total: 0, paid: 0, balance: 0 }
    })
    const readDispenseMedicineStocks = async (params: { date_from?: string; date_to?: string } = {}) => {
        const response = await axios.get(`${baseUrl}api/reports/dispense-medicine-stocks`, { params });
        dispenseMedicineStocks.value = response.data.data;
    }
    const readPatientInvoice = async (params: { date_from?: string; date_to?: string; case_type?: string; status?: string } = {}) => {
        const response = await axios.get(`${baseUrl}api/reports/patient-invoices`, { params });
        patientInvoice.value = response.data.data;
    }
    return {
        readDispenseMedicineStocks,
        readPatientInvoice,
        dispenseMedicineStocks,
        patientInvoice
    }
})
