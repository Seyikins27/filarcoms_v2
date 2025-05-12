<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use SolutionForest\FilamentAccessManagement\Concerns\FilamentUserHelpers;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, FilamentUserHelpers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'can_publish'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function can_publish():bool
    {
        if($this->can_publish==true || $this->can_publish==1)
        {
            return true;
        }
        return false;
    }

    public function pages()
    {
        return $this->hasMany(Page::class,'created_by');
    }

    public function page_updated()
    {
        return $this->hasMany(Page::class,'updated_by');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
