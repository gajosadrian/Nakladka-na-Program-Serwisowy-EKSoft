<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Attributes
     *
     */

    public function getIsTechnikAttribute(): bool
    {
        return $this->technik_id;
    }

    public function getShortNameAttribute(): string
    {
        return explode(' ', $this->name)[1];
    }

    /**
    * Relations
    *
    */

    public function pracownik()
    {
        return $this->hasOne('App\Models\SMS\Pracownik', 'LOGIN', 'email');
    }
}
