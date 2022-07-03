<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class BscUnits extends Model
{
    use HasFactory;

    // protected $connection = 'oracle';
    protected $guarded= [];

    public function createdUser() {
        return $this->belongsTo(User::class, 'username_created');
    }

    public function updatedUser() {
        return $this->belongsTo(User::class, 'username_updated');
    }

    public function unit() {
        return $this->belongsTo(BscUnits::class, 'unit_id');
    }

    public function units() {
        return $this->hasMany(BscUnits::class, 'unit_id');
    }

    public function targets() {
        return $this->hasMany(BscTargets::class, 'unit_id');
    }

    public function userUnits() {
        return $this->hasMany(BscTargetUpdates::class, 'username');
    }
}
