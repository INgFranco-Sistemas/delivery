<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Requerido por JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Requerido por JWTSubject
    public function getJWTCustomClaims()
    {
        return [];
    }
}
