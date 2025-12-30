<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyGroup extends Model
{
    protected $fillable = ['slug','title','description','created_by'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class)->with('member')->latest();
    }
}
