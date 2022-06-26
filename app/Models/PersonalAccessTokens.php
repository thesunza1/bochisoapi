<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessTokens extends Model
{
    use HasFactory;
    // protected $connection = 'oracle';
    protected $table = 'bcs.personal_access_tokens';
    protected $guarded = [];
}
