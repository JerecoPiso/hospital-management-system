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

export interface HistoryAndPhysicalExaminationFormOne {
    pid?: string;
    chief_complaint: string;
    history_of_present_illness: string;
    past_medical_history?: string;
    past_medical_history_others?: string;
    past_surgical_history?: string;
    past_surgical_history_history?: string;
    hospitalization_history?: string;
    hospitalization_history_others?: string;
    medication_history?: string;
    medication_history_others?: string;
    allergies?: string;
    allergies_others?: string;
    family_history?: string;
    family_history_others?: string;
    social_history?: string;
    social_history_others?: string;
    immunization_history?: string;
    immunization_history_others?: string;
    review_of_systems?: string;
    remarks?: string;
}

export interface HistoryAndPhysicalExaminationFormTwo {
    pid?: string;
    general_appearance?: string;
    general_appearance_others?: string;
    skin?: string;
    skin_others?: string;
    heent?: string;
    heent_others?: string;
    neck?: string;
    neck_others?: string;
    chest_lungs?: string;
    chest_lungs_others?: string;
    cardiovascular?: string;
    cardiovascular_others?: string;
    abdomen?: string;
    abdomen_others?: string;
    genitourinary?: string;
    genitourinary_others?: string;
    rectal?: string;
    rectal_others?: string;
    musculoskeletal?: string;
    musculoskeletal_others?: string;
    neurological?: string;
    neurological_others?: string;
    psychiatric_mental_status?: string;
    psychiatric_mental_status_others?: string;
    assessment_impression?: string;
    plan_recommendations?: string;
    remarks?: string;
}

export interface PatientCase {
    pid?: string;
    station_id?: number | null;
    bed_id?: number | null;
    patient_type_id?: number | null;
    case_number?: string;
    admission_datetime: string;
    chief_complaint: string;
    initial_diagnosis?: string;
    final_diagnosis?: string;
}

export interface PatientRegistration {
    pid?: string;
    medical_record_number?: string;
    firstname: string;
    lastname: string;
    middlename?: string;
    suffix?: string;
    birthdate: string;
    gender?: string;
    civil_status?: string;
    contact_number?: string;
    email_address?: string;
    religion?: string;
    birthplace?: string;
    occupation?: string;
    spouse_name?: string;
    admission_datetime: string;
    chief_complaint: string;
    initial_diagnosis?: string;
    final_diagnosis?: string;
    patientCases?: PatientCase[];
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