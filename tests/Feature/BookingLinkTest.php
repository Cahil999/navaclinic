<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ClinicHoliday;
use App\Models\ClinicSchedule;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_booking_links_to_existing_user()
    {
        // 1. Setup Data
        $doctor = Doctor::create(['name' => 'Dr. Test', 'specialty' => 'General', 'bio' => 'Test']);
        $user = User::factory()->create([
            'name' => 'Existing Patient',
            'phone_number' => '0812345678',
            'is_admin' => false,
        ]);

        // Ensure clinic is open
        ClinicSchedule::create(['day_of_week' => now()->dayOfWeek, 'open_time' => '08:00', 'close_time' => '20:00', 'is_open' => true]);

        // 2. Make Guest Booking
        $response = $this->post(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00',
            'duration_minutes' => 30,
            'symptoms' => 'Checkup',
            'customer_name' => 'Existing Patient',
            'customer_phone' => '0812345678',
        ]);

        // 3. Assertions
        $response->assertRedirect(route('welcome'));

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Existing Patient',
            'user_id' => $user->id, // Should link to the existing user
        ]);
    }

    public function test_guest_booking_does_not_link_if_no_match()
    {
        // 1. Setup Data
        $doctor = Doctor::create(['name' => 'Dr. Test', 'specialty' => 'General', 'bio' => 'Test']);

        // Ensure clinic is open
        ClinicSchedule::create(['day_of_week' => now()->dayOfWeek, 'open_time' => '08:00', 'close_time' => '20:00', 'is_open' => true]);

        // 2. Make Guest Booking
        $response = $this->post(route('booking.store'), [
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00',
            'duration_minutes' => 30,
            'symptoms' => 'Checkup',
            'customer_name' => 'New Patient',
            'customer_phone' => '0999999999',
        ]);

        // 3. Assertions
        $response->assertRedirect(route('welcome'));

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'New Patient',
            'user_id' => null,
        ]);
    }
}
