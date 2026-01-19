<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Basic Stats
        $stats = [
            'total_patients' => User::where('is_admin', false)->count() + Booking::whereNull('user_id')->whereNotNull('customer_phone')->distinct()->count('customer_phone'),
            'total_doctors' => Doctor::count(),
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('appointment_date', Carbon::today())->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        // 2. Chart Data Logic
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', null); // If null, show yearly view (All months)

        $chartData = [
            'labels' => [],
            'data' => [],
            'title' => ''
        ];

        if ($month) {
            // Monthly View: Show days of the specific month
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $chartData['title'] = "Bookings in " . $startDate->format('F Y');

            // Fetch all bookings for the range and group by day (PHP-side grouping for SQLite compatibility)
            $bookings = Booking::whereBetween('appointment_date', [$startDate, $endDate])
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->appointment_date)->format('Y-m-d');
                });

            $daysInMonth = $startDate->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day);
                $dateString = $date->toDateString();

                $chartData['labels'][] = $day;
                $chartData['data'][] = isset($bookings[$dateString]) ? $bookings[$dateString]->count() : 0;
            }

        } else {
            // Yearly View: Show all 12 months
            $chartData['title'] = "Bookings in $year";

            // Fetch all bookings for the year and group by month (PHP-side grouping for SQLite compatibility)
            $bookings = Booking::whereYear('appointment_date', $year)
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->appointment_date)->format('n'); // 1-12
                });

            for ($m = 1; $m <= 12; $m++) {
                $monthName = Carbon::createFromDate($year, $m, 1)->format('M');
                $chartData['labels'][] = $monthName;
                $chartData['data'][] = isset($bookings[$m]) ? $bookings[$m]->count() : 0;
            }
        }

        // 3. Latest Bookings
        $bookings = Booking::with(['user', 'doctor'])
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'bookings' => $bookings,
            'stats' => $stats,
            'chartData' => $chartData,
            'filters' => [
                'year' => (int) $year,
                'month' => $month ? (int) $month : null
            ]
        ]);
    }
}
