<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Psikolog extends Model
{
    protected $table = 'psikologs';

    // isi sesuai kolom di tabel psikologs; tambahkan sesuai kebutuhan
    protected $fillable = [
        'user_id',
        'name',
        'photo',
        'description',
        'work_start',
        'work_end',
        'hospital',
        'slot_minutes',
        'email',
    ];

    // cast jam / datetimes jika perlu
    protected $casts = [
        'work_start' => 'string',
        'work_end'   => 'string',
        'slot_minutes' => 'integer',
    ];

    // relasi ke user (jika psikolog terhubung ke users table)
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // relasi ke bookings (optional)
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'psikolog_id');
    }
}
