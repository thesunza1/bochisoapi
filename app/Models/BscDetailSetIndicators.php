<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscDetailSetIndicators extends Model
{
    use HasFactory;
    // protected $connection = 'oracle';
    protected $fillable =['username_created'];
    protected $table = 'bsc_detail_set_indicators';
    protected $guarded = [];

    public function setIndicator() {
        return $this->belongsTo(BscSetIndicators::class, 'set_indicator_id');
    }

    public function userUpdated() {
        return $this->belongsTo(User::class, 'username_updated');
    }
}
