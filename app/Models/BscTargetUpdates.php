<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscTargetUpdates extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user() {
        return $this->hasOne(User::class, 'username'  , 'username');
    }

    public function target() {
        return $this->belongsTo(BscTargets::class, 'target_id' , 'id');
    }

}
