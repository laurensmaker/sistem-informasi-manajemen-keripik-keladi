<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Helper untuk cek role
    public function isPenjual()
    {
        return $this->role === 'penjual';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    // Accessor untuk nama role dengan format
    public function getRoleLabelAttribute()
    {
        $roles = [
            'penjual' => 'Penjual',
            'owner' => 'Owner',
        ];
        return $roles[$this->role] ?? $this->role;
    }

    // Accessor untuk badge role
    public function getRoleBadgeAttribute()
    {
        $badges = [
            'penjual' => 'badge bg-info',
            'owner' => 'badge bg-danger',
        ];
        return $badges[$this->role] ?? 'badge bg-secondary';
    }

    // Scope untuk filter role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Scope untuk search
    public function scopeSearch($query, $search)
    {
        return $query->where('nama', 'LIKE', "%{$search}%")
                     ->orWhere('username', 'LIKE', "%{$search}%")
                     ->orWhere('no_hp', 'LIKE', "%{$search}%");
    }
}