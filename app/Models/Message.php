<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'booking_id',
        'sender_type',
        'sender_id',
        'content',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    public $timestamps = true;
}
