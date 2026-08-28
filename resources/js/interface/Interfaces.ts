export interface DoctorsOrder {
    pid?: string;
    patient_case_pid: string;
    patientCase?: PatientCase;
    order: string;
    progress_notes: string;
}

export interface NursesNotes {
    pid?: string;
    patient_case_pid: string;
    patientCase?: PatientCase;
    focus: string;
    data: string;
    action: string;
    response?: string;
}

export interface VitalSigns {
    pid?: string;
    patient_case_pid: string;
    patientCase?: PatientCase;
    type?: string;
    measured_at?: Date | null;
    systolic?: string | null;
    diastolic?: string | null;
    temperature?: string | null;
    heart_rate?: string | null;
    respiratory_rate?: string | null;
    oxygen_saturation?: string | null;
    weight?: string | null;
    height?: string | null;
    bmi?: string | null;
    muac?: string | null;
    length?: string | null;
    z_score?: string | null;
    head_circumference?: string | null;
    abdominal_circumference?: string | null;
    chest_circumference?: string | null;
    eye_response?: string | null;
    verbal_response?: string | null;
    motor_response?: string | null;
    fht?: string | null;
    lmp?: Date | null;
    aog?: string | null;
    edc?: Date | null;
    remarks?: string;
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

export interface MedicineStock {
    pid?: string;
    medicine_pid: string;
    medicine?: Medicines;
    quantity?: number;
    purchase_price: number | null;
    reorder_level?: number;
    unit_type?: string;
    units_per_package?: number;
    expiration_date?: string | null;
    batch_number?: string | null;
}

export interface MedicineStockMovement {
    pid?: string;
    medicine_pid: string;
    medicine?: Medicines;
    type: 'IN' | 'OUT';
    quantity: number;
    reference?: string | null;
    remarks?: string | null;
}

export interface MedicineDistribution {
    pid?: string;
    medicine_stock_pid: string;
    medicineStock?: MedicineStock;
    station_pid: string;
    station?: Station;
    quantity: number;
    distributedBy?: User | null;
    distributed_at?: string;
}

export interface PrescriptionItem {
    pid?: string;
    medicine_pid: string;
    medicine?: Medicines;
    frequency?: string | null;
    duration?: number | null;
    duration_unit?: string | null;
    quantity?: number | null;
    instructions?: string | null;
    remarks?: string | null;
    status?: string;
}

export interface Prescription {
    pid?: string;
    patient_case_pid: string;
    patientCase?: PatientCase;
    // Laravel serializes the `patientCase` relation as snake_case.
    patient_case?: PatientCase;
    doctor?: User;
    prescription_date: string;
    remarks?: string | null;
    status?: 'requested' | 'done' | 'picked-up' | 'cancelled';
    items: PrescriptionItem[];
    created_at?: string;
}

export interface Diet {
    pid?: string;
    name: string;
    description?: string | null;
}

export interface DietServed {
    pid?: string;
    patient_case_diet_pid?: string;
    user?: User;
    served_at?: string | null;
    remarks?: string | null;
    created_at?: string;
}

export interface PatientCaseDiet {
    pid?: string;
    patient_case_pid: string;
    // Laravel serializes the `patientCase` relation as snake_case.
    patient_case?: PatientCase;
    diet_pid: string;
    diet?: Diet;
    user?: User;
    remarks?: string | null;
    diets_served?: DietServed[];
    diets_served_count?: number;
    created_at?: string;
}

export interface HistoryAndPhysicalExaminationFormOne {
    pid?: string;
    patient_case_pid: string;
    patientCase?: PatientCase;
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
    patient_case_pid: string;
    patientCase?: PatientCase;
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
    patient_pid?: string;
    patient?: PatientRegistration;
    station_id?: number | null;
    bed_id?: number | null;
    patient_type_id?: number | null;
    patient_type_pid?: string;
    // Laravel serializes the `patientType` relation as snake_case.
    patient_type?: PatientType;
    case_number?: string;
    admission_datetime: string;
    chief_complaint: string;
    initial_diagnosis?: string;
    final_diagnosis?: string;
    type?: 'inpatient' | 'outpatient';
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
    type: 'inpatient' | 'outpatient';
    patient_type_pid?: string;
    // patientCases?: PatientCase[];
    patient_cases?: PatientCase[];

}

export interface Supply {
    pid?: string;
    name: string;
    unit: string;
    selling_price?: number | null;
    is_active?: boolean;
    supply_stocks_sum_quantity?: number | null;
}

export interface SupplyStock {
    pid?: string;
    supply_pid: string;
    supply?: Supply;
    quantity?: number;
    purchase_price?: number | null;
    reorder_level?: number;
    unit_type?: string;
    units_per_package?: number;
    expiration_date?: string | null;
    batch_number?: string | null;
}

export interface SupplyMovement {
    pid?: string;
    supply_stock_pid: string;
    supplyStock?: SupplyStock;
    quantity: number;
    type: 'IN' | 'OUT';
    used_for?: string | null;
}

export interface SupplyDistribution {
    pid?: string;
    supply_stock_pid: string;
    supplyStock?: SupplyStock;
    station_pid: string;
    station?: Station;
    quantity: number;
    distributedBy?: User | null;
    distributed_at?: string;
}

export interface Building {
    pid?: string;
    code: string;
    name: string;
    description?: string | null;
}

export interface Floor {
    pid?: string;
    building_pid: string;
    building?: Building;
    floor_number: string;
    name?: string | null;
    description?: string | null;
}

export interface Ward {
    pid?: string;
    floor_pid: string;
    floor?: Floor;
    code: string;
    name: string;
}

export interface Room {
    pid?: string;
    ward_pid: string;
    ward?: Ward;
    room_number: string;
    room_type?: string | null;
}

export interface Bed {
    pid?: string;
    room_pid: string;
    room?: Room;
    bed_number: string;
    status?: 'available' | 'occupied' | 'cleaning' | 'maintenance';
}

export interface Station {
    pid?: string;
    ward_pid: string;
    ward?: Ward;
    name: string;
    description?: string | null;
}

export interface PatientType {
    pid?: string;
    code: string;
    name: string;
    description?: string | null;
}

export interface PertinentSignsAndSymptomsList {
    pid?: string;
    code: string;
    name: string;
    status?: boolean;
    others?: string | null;
}

export interface PertinentSignsAndSymptoms {
    pid?: string;
    patient_case_pid: string;
    patient_case?: PatientCase;
    user?: User;
    values: string; // selected list codes joined by ";" e.g. "1;4;27;X"
    pain?: string | null;
    others?: string | null;
    remarks?: string | null;
    created_at?: string;
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

export interface DashboardStats {
    total_patients: number;
    total_patients_change: number;
    total_admissions: number;
    total_admissions_change: number;
    beds_total: number;
    beds_occupied: number;
    bed_occupancy_rate: number;
    low_stock_count: number;
}

export interface DashboardWeeklyAdmission {
    day: string;
    date: string;
    count: number;
}

export interface DashboardPatientTypeDistribution {
    name: string;
    count: number;
    percentage: number;
}

export interface DashboardRecentAdmission {
    pid: string;
    case_number: string;
    patient_name: string;
    medical_record_number: string | null;
    chief_complaint: string;
    admission_datetime: string;
}

export interface DashboardRecentUser {
    name: string;
    email: string;
    joined_at: string;
}

export interface DashboardSummary {
    stats: DashboardStats;
    weekly_admissions: DashboardWeeklyAdmission[];
    patient_type_distribution: DashboardPatientTypeDistribution[];
    recent_admissions: DashboardRecentAdmission[];
    recent_users: DashboardRecentUser[];
}