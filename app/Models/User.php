<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_path',
        'password',
        'role_id',
        'role',
        'organization_id',
        'is_active',
        'permissions',
        'locale',
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
            'is_active' => 'bool',
            'permissions' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class);
    }

    public function isSuperAdmin(): bool
    {
        return ($this->role?->name ?? $this->role) === 'super_admin';
    }

    public function isManufacturer(): bool
    {
        return ($this->role?->name ?? $this->role) === 'manufacturer';
    }

    public function isOrganizationAdmin(): bool
    {
        return ($this->role?->name ?? $this->role) === 'organization_admin';
    }

    public function isSupplier(): bool
    {
        return ($this->role?->name ?? $this->role) === 'supplier';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($panel->getId() !== 'super-admin') {
            return false;
        }

        return $this->isSuperAdmin()
            || $this->isManufacturer()
            || $this->isOrganizationAdmin()
            || $this->isSupplier();
    }
}
