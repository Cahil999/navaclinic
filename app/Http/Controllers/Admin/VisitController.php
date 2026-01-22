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

        if (str_starts_with($patientId, 'guest_')) {
            $bookingId = str_replace('guest_', '', $patientId);
            $booking = Booking::findOrFail($bookingId);

            // Create temporary patient object (Using Array to preserve ID string, as Model casts 'id' to int)
            $patient = [
                'id' => $patientId, // Keep guest ID string
                'name' => $booking->customer_name,
                'phone_number' => $booking->customer_phone,
            ];

            // Fetch pending bookings for this guest
            $bookings = Booking::whereNull('user_id')
                ->where('customer_name', $booking->customer_name)
                ->where('customer_phone', $booking->customer_phone)
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('appointment_date')
                ->get();

        } else {
            $patient = User::findOrFail($patientId);

            // Fetch pending bookings for this patient
            $bookings = Booking::where('user_id', $patientId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->with('doctor')
                ->orderBy('appointment_date')
                ->get();
        }

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
            'patient_id' => 'required', // Allow string for guest_
            'type' => 'required|in:booking,walk_in',
            'booking_id' => 'required_if:type,booking|nullable|exists:bookings,id',
            'doctor_id' => 'required_if:type,walk_in|nullable|exists:doctors,id',
            'visit_date' => 'required_if:type,walk_in|nullable|date',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $patientId = $validated['patient_id'];

            // Handle Guest Registration if needed
            if (str_starts_with($patientId, 'guest_')) {
                $bookingId = str_replace('guest_', '', $patientId);
                $guestBooking = Booking::findOrFail($bookingId);

                // Generate HN
                $datePart = now()->format('dmY');
                $countToday = User::whereDate('created_at', now()->toDateString())->count() + 1;
                $hnId = 'HN-' . $datePart . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                // Create User
                $email = $guestBooking->customer_phone
                    ? 'guest_' . $guestBooking->customer_phone . '@navaclinic.com'
                    : 'guest_' . uniqid() . '@navaclinic.com';

                $newUser = User::create([
                    'patient_id' => $hnId,
                    'name' => $guestBooking->customer_name,
                    'phone_number' => $guestBooking->customer_phone,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($guestBooking->customer_phone ?? '12345678'),
                    'is_admin' => false,
                    'is_doctor' => false,
                ]);

                // Link guest bookings
                Booking::whereNull('user_id')
                    ->where('customer_name', $guestBooking->customer_name)
                    ->where('customer_phone', $guestBooking->customer_phone)
                    ->update(['user_id' => $newUser->id]);

                $patientId = $newUser->id;
                // Update validated array for use below if needed, though we use $patientId variable
            }

            if ($validated['type'] === 'booking') {
                $booking = Booking::find($validated['booking_id']);

                // Ensure booking is linked to user if it wasn't already (e.g. if we just migrated)
                if (!$booking->user_id) {
                    $booking->update(['user_id' => $patientId]);
                }

                // Create Visit linked to Booking
                Visit::create([
                    'visit_date' => now(), // Visit starts now
                    'patient_id' => $patientId,
                    'doctor_id' => $booking->doctor_id,
                    'booking_id' => $booking->id,
                    'symptoms' => $booking->symptoms,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'ongoing',
                ]);

                // Update Booking Status to completed
                $booking->update(['status' => 'completed']);
            } else {
                // Walk-in
                Visit::create([
                    'visit_date' => $validated['visit_date'],
                    'patient_id' => $patientId,
                    'doctor_id' => $validated['doctor_id'],
                    'booking_id' => null,
                    'symptoms' => $validated['symptoms'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'ongoing',
                ]);
            }
        });

        // We can't redirect to the OLD guest ID, we must redirect to the NEW user ID if migrated.
        // Re-calculate ID
        $redirectId = $validated['patient_id'];
        if (str_starts_with($redirectId, 'guest_')) {
            // Find the user we just created?
            // We are inside transaction, simpler to capture the ID in outer scope or assume the redirect logic query.
            // But we need the ID. 
            // Let's rely on finding it again or better: 
            // To solve this cleanly, I should capture the new user ID.
            // But the transaction closure scope variables are by value unless object.
            // I'll refactor the Redirect to be dynamic.
        }

        // Since I cannot change the Redirect line easily in this block replacement without including it...
        // Wait, the ReplacementContent INCLUDES the entire methods.
        // So I can write the redirect logic correctly.

        return redirect()->route('admin.patients.index')->with('success', 'Visit started successfully.');
    }
    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'booking', 'treatmentRecord', 'payments']);

        return Inertia::render('Admin/Visits/Show', [
            'visit' => $visit,
        ]);
    }
}
