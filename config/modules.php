<?php

/**
 * Canonical list of RBAC modules. Each key must match the `permission:` middleware
 * argument used in the matching routes/api/*.php file. This is the single source of
 * truth used to seed default role access rows and to render the permissions matrix
 * in the Roles & Permissions admin screen.
 */
return [
    ['key' => 'roles', 'label' => 'Roles & Permissions'],
    ['key' => 'users', 'label' => 'Users'],
    ['key' => 'patient', 'label' => 'Patient Registration'],
    ['key' => 'patient-cases', 'label' => 'Patient Cases'],
    ['key' => 'patient-types', 'label' => 'Patient Types'],
    ['key' => 'vital-signs', 'label' => 'Vital Signs'],
    ['key' => 'doctors-order', 'label' => "Doctor's Orders"],
    ['key' => 'nurses-notes', 'label' => "Nurses' Notes"],
    ['key' => 'soaps', 'label' => 'SOAP Notes'],
    ['key' => 'icds', 'label' => 'ICD Codes'],
    ['key' => 'pertinent-signs-and-symptoms', 'label' => 'Pertinent Signs & Symptoms'],
    ['key' => 'pertinent-signs-and-symptoms-lists', 'label' => 'Signs & Symptoms List'],
    ['key' => 'history-and-physical-examination-form-one', 'label' => 'H&P Examination Form 1'],
    ['key' => 'history-and-physical-examination-form-two', 'label' => 'H&P Examination Form 2'],
    ['key' => 'prescriptions', 'label' => 'Prescriptions'],
    ['key' => 'prescription-items', 'label' => 'Prescription Items'],
    ['key' => 'diets', 'label' => 'Diets'],
    ['key' => 'patient-case-diets', 'label' => 'Patient Case Diets'],
    ['key' => 'medicine', 'label' => 'Medicine Items'],
    ['key' => 'medicine-stocks', 'label' => 'Medicine Stocks'],
    ['key' => 'medicine-stock-movements', 'label' => 'Medicine Stock Movements'],
    ['key' => 'medicine-distributions', 'label' => 'Medicine Distributions'],
    ['key' => 'supplies', 'label' => 'Supply Items'],
    ['key' => 'supply-stocks', 'label' => 'Supply Stocks'],
    ['key' => 'supply-movements', 'label' => 'Supply Movements'],
    ['key' => 'supply-distributions', 'label' => 'Supply Distributions'],
    ['key' => 'buildings', 'label' => 'Buildings'],
    ['key' => 'floors', 'label' => 'Floors'],
    ['key' => 'wards', 'label' => 'Wards'],
    ['key' => 'rooms', 'label' => 'Rooms'],
    ['key' => 'beds', 'label' => 'Beds'],
    ['key' => 'stations', 'label' => 'Stations'],
];
