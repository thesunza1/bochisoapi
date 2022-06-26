<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscTopicOrders extends Model
{
    use HasFactory;
    // protected $connection = 'oracle';
    protected $table = 'bsc_topic_orders';
    public $timestamps = false;
    protected $guarded = [];

    public function setIndicator() {
        return $this->belongsTo(BscSetIndicators::class, 'set_indicator_id');
    }

    public function topic() {
        return $this->belongsTo(BscTopics::class, 'set_indicator_id');
    }
}
