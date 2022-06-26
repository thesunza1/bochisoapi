<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscTopics extends Model
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

    public function targets() {
        return $this->hasMany(BscTargets::class, 'topic_id' , 'id')->orderBy('order');
    }
}
