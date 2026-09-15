<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    const SUPER_ADMIN = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'email',
        'password',
        'firstname',
        'lastname',
        'middlename',
        'suffix',
        'gender',
        'date_of_birth',
        'license_no',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'deleted_at',
        'created_at',
        'updated_at',
        'email_verified_at'
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
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->pid = $user->pid ?? Str::uuid()->toString();
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Whether this user's role grants the given ability (view|create|update|delete)
     * on the given module key (see config/modules.php).
     */
    public function hasPermission(string $module, string $ability): bool
    {
        if (!$this->role_id) {
            return false;
        }

        $column = "can_{$ability}";
        $access = $this->relationLoaded('role') && $this->role
            ? $this->role->roleAccesses->firstWhere('module', $module)
            : RoleAccess::where('role_id', $this->role_id)->where('module', $module)->first();

        return (bool) ($access?->{$column} ?? false);
    }

    /**
     * Flattened map of module => [view, create, update, delete] booleans for the
     * frontend to gate navigation and buttons with.
     */
    public function permissionsMap(): array
    {
        $modules = collect(require config_path('modules.php'));
        $accesses = $this->role
            ? $this->role->roleAccesses->keyBy('module')
            : collect();

        return $modules->mapWithKeys(function ($module) use ($accesses) {
            $access = $accesses->get($module['key']);
            return [
                $module['key'] => [
                    'view' => (bool) ($access->can_view ?? false),
                    'create' => (bool) ($access->can_create ?? false),
                    'update' => (bool) ($access->can_update ?? false),
                    'delete' => (bool) ($access->can_delete ?? false),
                ],
            ];
        })->all();
    }
}
