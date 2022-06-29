<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscSetIndicators extends Model
{
    use HasFactory;
    // protected $connection = 'oracle';
    protected $guarded = [];
    protected $casts = [
         'year_set' => 'timestamp',
        'month_set' => 'timestamp'
    ];

    public  function setIndicator() {
        return $this->belongsTo(BscSetIndicators::class,  'set_indicator_id');
    }
    public  function setIndicators() {
        return $this->hasMany(BscSetIndicators::class,  'set_indicator_id');
    }
    public function  detailSetIndicators() {
        return $this->hasMany(BscDetailSetIndicators::class, 'set_indicator_id');
    }

    public function  detailSetIndicator() {
        return $this->hasOne(BscDetailSetIndicators::class, 'set_indicator_id')->orderByDesc('created_at');
    }
    public function createdUser() {
        return $this->belongsTo(User::class, 'username_created');
    }
    public function updatedUser() {
        return $this->belongsTo(User::class, 'username_updated');
    }
    public function unit() {
        return $this->belongsTo(BscUnits::class, 'unit_id');
    }
    public function target() {
        return $this->belongsTo(BscTargets::class, 'target_id');
    }
    public function  topicOrders() {
        return $this->hasOne( BscTopicOrders::class , 'set_indicator_id');
    }
}
