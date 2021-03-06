<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Jenssegers\Agent\Agent;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $connection = 'mysql';

    protected $casts = [
        'saved_fields' => 'array',
    ];

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

    private $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Attributes
     *
     */

    public function getIsDesktopAttribute(): bool
    {
        return $this->agent->isDesktop();
    }

    public function getIsMobileAttribute(): bool
    {
        return ! $this->agent->isDesktop();
    }

    public function getIsTechnikAttribute(): bool
    {
        return $this->technik_id;
    }

    public function getShortNameAttribute(): string
    {
        return explode(' ', $this->name)[1];
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        $index = count($words);
        while($index) {
            $initials .= $words[--$index][0];
        }
        return $initials;
    }

    /**
     * Methods
     *
     */
    public function getSavedField($field_name)
    {
        if (empty($this->saved_fields[$field_name])) {
            return false;
        }
        $data = $this->saved_fields[$field_name];
        if (gettype($data) == 'string') {
            return trim($data);
         }
        return $data;
    }

    public function setSaveField($field_name, $field_value, $save = true)
    {
        $saved_field[$field_name] = $field_value;
        $this->saved_fields = array_merge($this->saved_fields, $saved_field);
        if ($save) {
            $this->save();
        }
    }

    /**
     * Relations
     *
     */

    public function pracownik()
    {
        return $this->hasOne('App\Models\SMS\Pracownik', 'LOGIN', 'email');
    }

    public function technik()
    {
        return $this->hasOne('App\Models\SMS\Technik', 'id_technika', 'technik_id');
    }
}
