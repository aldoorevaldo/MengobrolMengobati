<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'psikiater_id',
        'psikolog_id',
        'service',
        'scheduled_at',
        'status',
        'notes',
        'email_token',
        'type'
    ];

    protected $dates = [
        'scheduled_at',
        'created_at',
        'updated_at'
    ];

    // relasi pemesan (user)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // relasi ke psikiater
    public function psikiater()
    {
        return $this->belongsTo(\App\Models\Psikiater::class, 'psikiater_id');
    }

    // relasi ke psikolog
    public function psikolog()
    {
        return $this->belongsTo(\App\Models\Psikolog::class, 'psikolog_id');
    }

    // **relasi messages** (satu booking punya banyak message)
    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class, 'booking_id');
    }
}
