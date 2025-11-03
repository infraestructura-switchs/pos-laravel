<?php

declare(strict_types=1);

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'status',
        'suspended_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'suspended_at' => 'datetime',
    ];

    /**
     * Columnas personalizadas para el tenant.
     *
     * @return array
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'status',
        ];
    }

    /**
     * Verificar si el tenant está activo.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el tenant está suspendido.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Suspender el tenant.
     *
     * @return void
     */
    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    /**
     * Activar el tenant.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
        ]);
    }

    /**
     * Obtener el nombre de la base de datos del tenant.
     *
     * @return string
     */
    public function getDatabaseName(): string
    {
        return 'tenant' . $this->id;
    }
}

