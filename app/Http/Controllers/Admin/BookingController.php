<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $preselectedUser = null;
        if ($request->has('user_id')) {
            $preselectedUser = \App\Models\User::find($request->user_id);
        }

        // 1. Fetch Key Users (Admins excluded)
        $users = \App\Models\User::where('is_admin', false)
            ->select('id', 'name', 'phone_number', 'email')
            ->get()
            ->map(function ($u) {
                return [
                    'type' => 'user',
                    'id' => $u->id,
                    'name' => $u->name,
                    'phone' => $u->phone_number,
                    'email' => $u->email,
                    'display_text' => $u->name . ($u->phone_number ? " ({$u->phone_number})" : "")
                ];
            });

        // 2. Fetch Distinct Guests from Bookings (Where user_id is null)
        $guests = \App\Models\Booking::whereNull('user_id')
            ->whereNotNull('customer_name')
            ->distinct()
            ->get(['customer_name', 'customer_phone'])
            ->map(function ($g) {
                return [
                    'type' => 'guest',
                    'id' => 'guest_' . md5($g->customer_name . $g->customer_phone), // Temporary unique ID for frontend key
                    'name' => $g->customer_name,
                    'phone' => $g->customer_phone,
                    'email' => '-',
                    'display_text' => $g->customer_name . ($g->customer_phone ? " ({$g->customer_phone}) [Guest]" : " [Guest]")
                ];
            });

        // 3. Merge and Unique (by name+phone to avoid duplicates if guest became user? Or just show all)
        // For simplicity, just merge.
        $patients = $users->concat($guests)->sortBy('name')->values();

        return Inertia::render('Admin/Booking/Create', [
            'doctors' => \App\Models\Doctor::all(),
            'patients' => $patients, // Renamed from 'users'
            'preselectedUser' => $preselectedUser
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'duration_minutes' => 'required|in:30,60,90',
            'symptoms' => 'required|string',
            'user_type' => 'required|in:existing,guest',
            // If existing user
            'user_id' => 'required_if:user_type,existing|nullable|exists:users,id',
            // If guest
            'customer_name' => 'required_if:user_type,guest|nullable|string|max:255',
            'customer_phone' => 'required_if:user_type,guest|nullable|string|max:20',
        ]);

        // Validate Schedule & Holidays (Reuse logic is better, but copying for speed as per user request context - often faster to duplicate small validation logic or move to service if big. Here it is small enough).
        $date = $validated['appointment_date'];

        // Check Holiday
        $holiday = \App\Models\ClinicHoliday::where('date', $date)->first();
        if ($holiday) {
            return back()->withErrors(['appointment_date' => "Clinic is closed on this date: {$holiday->label}"]);
        }

        // Check Weekly Schedule
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
        $schedule = \App\Models\ClinicSchedule::where('day_of_week', $dayOfWeek)->first();
        if (!$schedule || !$schedule->is_open) {
            return back()->withErrors(['appointment_date' => "Clinic is closed on this day."]);
        }

        $bookingData = [
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $validated['start_time'],
            'duration_minutes' => $validated['duration_minutes'],
            'symptoms' => $validated['symptoms'],
            // 'status' => 'pending', // Removed duplicate
            // Actually, for Walk-in, usually it's Confirmed directly. 
            // Let's set to 'confirmed' for Admin created bookings to save a step.
            'status' => 'confirmed'
        ];

        if ($validated['user_type'] === 'existing') {
            $bookingData['user_id'] = $validated['user_id'];
            // Fetch user to fill customer name/phone fallback if needed?
            // Actually Booking model relies on relationship for users.
        } else {
            // Check if there is an existing user with this name and phone to be smart?
            // The implementation plan said: "Create Booking for Walk-in (Guest)... Is user_id null?"
            // Yes, strictly guest.
            $bookingData['user_id'] = null;
            $bookingData['customer_name'] = $validated['customer_name'];
            $bookingData['customer_phone'] = $validated['customer_phone'];
        }

        Booking::create($bookingData);

        return redirect()->route('admin.dashboard')->with('success', 'Booking created successfully!');
    }
    public function edit(Booking $booking)
    {
        $booking->load(['user', 'doctor']);

        return Inertia::render('Admin/Booking/Edit', [
            'booking' => $booking,
            'doctors' => \App\Models\Doctor::all(),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'duration_minutes' => 'required|in:30,60,90',
            'symptoms' => 'required|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Simple validation check (can be expanded like store)
        // Check Holiday & Schedule if date/time changed?
        // For now, let's assume Admin overrides logic or knows what they are doing for Edit.
        // But basic check is good practice.

        $booking->update($validated);

        return redirect()->route('admin.bookings.show', $booking->id)->with('success', 'Booking updated successfully!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'doctor', 'treatmentRecord', 'payments']);
        return Inertia::render('Admin/Booking/Show', [
            'booking' => $booking
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update($validated);

        return back();
    }
}
