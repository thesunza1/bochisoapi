<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends  Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // protected $connection = 'oracle1';
    protected $primaryKey = 'username';
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function createdTopics() {
        return $this->hasMany( BscTopics::class, 'username_created','username' );
    }
    public function updatedTopics() {
        return $this->hasMany( BscTopics::class, 'username_updated','username' );
    }

    public function createdTargets() {
        return $this->hasMany( BscTargets::class, 'username_created','username' );
    }

    public function updatedTargets() {
        return $this->hasMany( BscTargets::class, 'username_updated','username' );
    }

    public function createdUnits() {
        return $this->hasMany( BscUnits::class, 'username_created','username' );
    }

    public function updatedUnits() {
        return $this->hasMany( BscUnits::class, 'username_updated','username' );
    }

    public function updatedSetIndicators() {
        return $this->hasMany( BscSetIndicators::class, 'username_updated','username' );
    }

    public function createdSetIndicators() {
        return $this->hasMany( BscSetIndicators::class, 'username_created','username' );
    }
    public function userUnits() {
        return $this->hasMany( BscUserUnits::class, 'username');
    }

}
