# Fix Summary: Missing Visit Details

## Issue
The user reported that visit details (specifically the medical record section) were not showing up in the visit view (`Admin/Visits/Show.vue`).

## Root Cause
The `VisitController` was conditionally creating the `TreatmentRecord` only if specific inputs (`hasAny(...)`) were present in the request. 
- In some scenarios (e.g., creating a visit from a booking without modifying medical fields, or submitting empty strings which `hasAny` might interpret inconsistently depending on middleware), the `TreatmentRecord` was not being created.
- The Vue component `Show.vue` relies on `v-if="visit.treatment_record"` to display the entire medical section. If the record is missing, the section is hidden.

## Resolution
Modified `app/Http/Controllers/Admin/VisitController.php`:
1.  **Always Create TreatmentRecord**: Removed the `hasAny` check. Now, a `TreatmentRecord` is created for every new visit. This ensures the Medical Record section is always visible in the details view, even if the fields are initially empty (showing `-`).
2.  **Cleaned Up Attributes**: 
    - Removed `patient_id` and `doctor_id` from the `TreatmentRecord::create` call, as these columns do not exist in the `treatment_records` table (they live on the `visits` table).
    - Added `booking_id => $visit->booking_id` to ensure the record is linked to the booking references if applicable.
    - Synced `chief_complaint` directly from `$visit->symptoms`.

## Outcome
All new visits will now have an associated `TreatmentRecord`, ensuring the "Medical Record" section in the Visit Details view is always displayed.
