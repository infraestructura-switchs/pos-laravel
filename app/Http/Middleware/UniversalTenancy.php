<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

class UniversalTenancy
{
    protected $tenancy;
    protected $resolver;

    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Lista de dominios centrales desde config
        $centralDomains = config('tenancy.central_domains', []);
        $currentDomain = $request->getHost();

        // Si es un dominio central, NO inicializar tenancy
        if (in_array($currentDomain, $centralDomains)) {
            return $next($request);
        }

        // Intentar resolver el tenant por dominio
        try {
            $tenant = $this->resolver->resolve($currentDomain);
            
            if ($tenant) {
                // Inicializar tenancy
                $this->tenancy->initialize($tenant);
                
                // Asegurar que existan directorios de framework para el storage del tenant
                // Esto evita errores como: Failed to open stream en storage/.../framework/cache
                try {
                    $tenantStoragePath = storage_path(); // Sufijado por FilesystemTenancyBootstrapper
                    $directoriesToEnsure = [
                        $tenantStoragePath . DIRECTORY_SEPARATOR . 'framework',
                        $tenantStoragePath . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
                        $tenantStoragePath . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
                        $tenantStoragePath . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
                        $tenantStoragePath . DIRECTORY_SEPARATOR . 'logs',
                    ];

                    foreach ($directoriesToEnsure as $dir) {
                        if (!File::exists($dir)) {
                            File::ensureDirectoryExists($dir, 0755, true);
                        }
                    }
                } catch (\Throwable $e) {
                    // No bloquear la petición por fallo al crear carpetas; registrar para diagnóstico
                    \Log::warning('No se pudo asegurar directorios de storage para el tenant: ' . $e->getMessage());
                }
                
                // Verificar estado del tenant
                if ($tenant->status === 'suspended') {
                    abort(403, 'Esta empresa ha sido suspendida temporalmente.');
                }
                
                if ($tenant->status === 'inactive') {
                    abort(403, 'Esta empresa está inactiva.');
                }
            }
        } catch (\Exception $e) {
            // Si no se encuentra el tenant, simplemente continuar
            // (esto permite que el dominio central funcione sin tenant)
            \Log::debug("No tenant found for domain: {$currentDomain}");
        }

        return $next($request);
    }
}
