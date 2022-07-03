<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscUserUnits extends Model
{
    use HasFactory;
    protected $gruaded = [];

    public function unit() {
        return $this->hasOne(BscUnits::class, 'unit_id');
    }
}
