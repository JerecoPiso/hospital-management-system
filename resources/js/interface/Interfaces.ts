export interface DoctorsOrder {
    pid?: string;
    order: string;
    progress_notes: string;
}

export interface NursesNotes {
    pid?: string;
    focus: string;
    data: string;
    action: string;
    response?: string;
}

export interface VitalSigns {
    pid?: string;
    temperature: number;
    pulse_rate: number;
    respiratory_rate: number;
    blood_pressure: string;
}

export interface Medicines {
    pid?: string,
    name: string,
    generic_name?: string,
    brand_name?: string,
    dosage?: Number | any,
    dosage_unit?: string,
    form?: string,
    administration_route?: string,
    price: Number | any
}

export interface User {
    pid?: string,
    email: string,
    firstname: string,
    middlename?: string,
    lastname: string,
    suffix?: string,
    license_no?: string,
    gender: string,
    date_of_birth: Date,
    password: string
}