<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'visit_date',
        'patient_id',
        'doctor_id',
        'booking_id',
        'symptoms',
        'notes',
        'status',
        'price',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function treatmentRecord()
    {
        return $this->hasOne(TreatmentRecord::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
