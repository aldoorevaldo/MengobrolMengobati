<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = ['therapy_group_id','user_id','pseudonym'];

    public function group()
    {
        return $this->belongsTo(TherapyGroup::class,'therapy_group_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class,'user_id','user_id');
    }
}
