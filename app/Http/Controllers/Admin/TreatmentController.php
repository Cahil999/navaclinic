<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TreatmentRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TreatmentController extends Controller
{
    public function create(Booking $booking)
    {
        return Inertia::render('Admin/Treatment/Create', [
            'booking' => $booking->load(['user', 'doctor']),
            'previousRecord' => $booking->treatmentRecord,
        ]);
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:255',
            'id_card_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'race' => 'nullable|string',
            'nationality' => 'nullable|string',
            'religion' => 'nullable|string',
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'underlying_disease' => 'nullable|string',
            'surgery_history' => 'nullable|string',
            'drug_allergy' => 'nullable|string',
            'accident_history' => 'nullable|string',
            'weight' => 'nullable|numeric|between:0,500',
            'height' => 'nullable|numeric|between:0,300',
            'temperature' => 'nullable|numeric|between:0,100',
            'pulse_rate' => 'nullable|integer|between:0,300',
            'respiratory_rate' => 'nullable|integer|between:0,100',
            'blood_pressure' => 'nullable|string|max:20',
            'chief_complaint' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'massage_weight' => 'nullable|string|in:light,medium,heavy',
            'pain_level_before' => 'nullable|integer|between:0,10',
            'pain_level_after' => 'nullable|integer|between:0,10',
            'pain_areas' => 'nullable|array',
            'diagnosis' => 'required|string',
            'treatment_details' => 'required|string',
            'notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Update the booking price
        if ($request->has('price')) {
            $booking->update(['price' => $request->price]);
        }

        $booking->treatmentRecord()->updateOrCreate(
            ['booking_id' => $booking->id],
            collect($validated)->except('price')->toArray()
        );

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Treatment details saved successfully.');
    }
    public function createForVisit(\App\Models\Visit $visit)
    {
        return Inertia::render('Admin/Treatment/Create', [
            'visit' => $visit->load(['patient', 'doctor']),
            'previousRecord' => $visit->treatmentRecord,
            'isVisit' => true,
        ]);
    }

    public function storeForVisit(Request $request, \App\Models\Visit $visit)
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:255',
            'id_card_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'race' => 'nullable|string',
            'nationality' => 'nullable|string',
            'religion' => 'nullable|string',
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'underlying_disease' => 'nullable|string',
            'surgery_history' => 'nullable|string',
            'drug_allergy' => 'nullable|string',
            'accident_history' => 'nullable|string',
            'weight' => 'nullable|numeric|between:0,500',
            'height' => 'nullable|numeric|between:0,300',
            'temperature' => 'nullable|numeric|between:0,100',
            'pulse_rate' => 'nullable|integer|between:0,300',
            'respiratory_rate' => 'nullable|integer|between:0,100',
            'blood_pressure' => 'nullable|string|max:20',
            'chief_complaint' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'massage_weight' => 'nullable|string|in:light,medium,heavy',
            'pain_level_before' => 'nullable|integer|between:0,10',
            'pain_level_after' => 'nullable|integer|between:0,10',
            'pain_areas' => 'nullable|array',
            'diagnosis' => 'required|string',
            'treatment_details' => 'required|string',
            'notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Update the visit price
        if ($request->has('price')) {
            $visit->update(['price' => $request->price]);
        }

        $visit->treatmentRecord()->updateOrCreate(
            ['visit_id' => $visit->id],
            collect($validated)->except('price')->toArray()
        );

        return redirect()->route('admin.visits.show', $visit->id)
            ->with('success', 'Treatment details saved successfully.');
    }
}
