<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscTargets extends Model
{
    use HasFactory;
    // protected $connection = 'oracle';
    protected $guarded = [];

    public function createdUser() {
        return $this->belongsTo(User::class, 'username_created');
    }
    public function updatedUser() {
        return $this->belongsTo(User::class, 'username_updated');
    }
    public function topic() {
        return $this->belongsTo(BscTopics::class, 'topic_id' , 'id');
    }
    public function unit() {
        return $this->belongsTo(bscUnits::class , 'unit_id');
    }
    public function targets() {
        return $this->hasMany(BscTargets::class , 'target_id');
    }
    public function target() {
        return $this->belongsTo(BscTargets::class , 'target_id');
    }
    public function  setIndicators() {
        return $this->hasMany(BscSetIndicators::class, 'target_id');
    }
    public function  TargetUpdates() {
        return $this->hasMany(BscTargetUpdates::class, 'target_id');
    }
}
