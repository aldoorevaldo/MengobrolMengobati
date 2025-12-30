<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GroupMessage extends Model
{
    protected $fillable = ['therapy_group_id','user_id','message'];

    public function getPseudonymAttribute()
    {
        return \App\Models\GroupMember::where('therapy_group_id', $this->therapy_group_id)
                    ->where('user_id', $this->user_id)
                    ->value('pseudonym');
    }
}
