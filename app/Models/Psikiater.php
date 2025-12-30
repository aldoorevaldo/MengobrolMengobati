<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Psikiater extends Authenticatable
{
    use Notifiable;

    protected $table = 'psikiaters';

    protected $fillable = [
        'user_id','name','email','password','spesialis','phone','hospital','work_start','work_end','description','photo'
    ];

    protected $hidden = ['password','remember_token'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'psikiater_id');
    }

    public function deletePhotoFile()
    {
        if (!empty($this->photo)) {
            try {
                if (Storage::disk('public')->exists($this->photo)) {
                    Storage::disk('public')->delete($this->photo);
                }
            } catch (\Throwable $e) {
                \Log::warning("deletePhotoFile warning for psikiater_id={$this->id}: " . $e->getMessage());
            }
        }
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }
        return null;
    }
}
