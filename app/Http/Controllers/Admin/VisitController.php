<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Booking;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function create(Request $request)
    {
        $patientId = $request->input('user_id');
        $patient = User::findOrFail($patientId);

        // Fetch pending bookings for this patient
        $bookings = Booking::where('user_id', $patientId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('doctor')
            ->orderBy('appointment_date')
            ->get();

        $doctors = Doctor::all();

        return Inertia::render('Admin/Visits/Create', [
            'patient' => $patient,
            'bookings' => $bookings,
            'doctors' => $doctors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'type' => 'required|in:booking,walk_in',
            'booking_id' => 'required_if:type,booking|nullable|exists:bookings,id',
            'doctor_id' => 'required_if:type,walk_in|nullable|exists:doctors,id',
            'visit_date' => 'required_if:type,walk_in|nullable|date',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            if ($validated['type'] === 'booking') {
                $booking = Booking::find($validated['booking_id']);

                // Create Visit linked to Booking
                Visit::create([
                    'visit_date' => now(), // Visit starts now
                    'patient_id' => $validated['patient_id'],
                    'doctor_id' => $booking->doctor_id,
                    'booking_id' => $booking->id,
                    'symptoms' => $booking->symptoms, // Inherit or override?
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'ongoing',
                ]);

                // Update Booking Status to completed
                $booking->update(['status' => 'completed']);
            } else {
                // Walk-in: ONLY create Visit, NO Booking
                Visit::create([
                    'visit_date' => $validated['visit_date'],
                    'patient_id' => $validated['patient_id'],
                    'doctor_id' => $validated['doctor_id'],
                    'booking_id' => null, // No booking for walk-in
                    'symptoms' => $validated['symptoms'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'ongoing',
                ]);
            }
        });

        return redirect()->route('admin.patients.show', $validated['patient_id'])
            ->with('success', 'Visit started successfully.');
    }
    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'booking']);

        return Inertia::render('Admin/Visits/Show', [
            'visit' => $visit,
        ]);
    }
}
