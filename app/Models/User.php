<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    | Two roles:
    |   - admin: full access — manages colleges/offices/staff structure,
    |     devices, reports, user accounts, and can view the activity log.
    |   - custodian: a restricted "basic user" account. Can manage devices
    |     and issue/return them to staff, and browse the college/office/staff
    |     directory (read-only). Cannot: create user accounts, delete any
    |     record, use the bulk-add ("auto-form") feature, or view activity
    |     logs — per the client's specified restrictions.
    |
    | Label is intentionally centralized here — if the client wants a
    | different display name later, only the ROLES array below changes.
    */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTODIAN = 'custodian';

    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_CUSTODIAN => 'Custodian',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCustodian(): bool
    {
        return $this->role === self::ROLE_CUSTODIAN;
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst((string) $this->role);
    }
}