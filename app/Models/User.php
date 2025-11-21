<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use MongoDB\Laravel\Auth\User as MongoAuthUser;

class User extends MongoAuthUser
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
        'password',
        'role',
        'roles',
        'permissions',
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
            'roles' => 'array',
            'permissions' => 'array',
        ];
    }

    /**
     * Obtener todos los roles asignados (incluido el legacy "role").
     */
    public function assignedRoles(): array
    {
        $legacyRole = $this->role ? [$this->role] : [];
        $roles = $this->roles ?? [];

        if (empty($roles) && empty($legacyRole)) {
            $default = config('permissions.defaults.role');
            return $default ? [$default] : [];
        }

        return array_values(array_unique(array_merge($legacyRole, $roles)));
    }

    /**
     * Verifica si el usuario tiene alguno de los roles indicados.
     */
    public function hasRole(string ...$roles): bool
    {
        if (empty($roles)) {
            return false;
        }

        $assigned = $this->assignedRoles();

        return collect($roles)->contains(function ($role) use ($assigned) {
            return in_array($role, $assigned, true);
        });
    }

    /**
     * Retorna todos los permisos calculados para el usuario.
     */
    public function resolvePermissions(): array
    {
        $configRoles = config('permissions.roles', []);

        $fromRoles = collect($this->assignedRoles())
            ->flatMap(function ($role) use ($configRoles) {
                return Arr::get($configRoles, $role, []);
            })
            ->all();

        $customPermissions = $this->permissions ?? [];

        return array_values(array_unique(array_merge($fromRoles, $customPermissions)));
    }

    /**
     * Verifica si el usuario cuenta con el permiso indicado.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->resolvePermissions(), true);
    }

    /**
     * Asigna uno o varios roles al usuario.
     */
    public function syncRoles(array $roles): void
    {
        $this->roles = array_values(array_unique($roles));
        $this->save();
    }

    /**
     * Asigna permisos personalizados al usuario.
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions = array_values(array_unique($permissions));
        $this->save();
    }

    /**
     * Relación con TransporteProveedor (Alta Proveedor)
     */
    public function transporteProveedor()
    {
        return $this->hasOne(\App\Models\TransporteProveedor::class, 'user_id');
    }

    /**
     * Relación con Unidades Disponibles
     */
    public function unidadesDisponibles()
    {
        return $this->hasMany(\App\Models\UnidadDisponible::class, 'user_id');
    }
}
