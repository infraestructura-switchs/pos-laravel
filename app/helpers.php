<?php

use App\Models\Terminal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('rounded')) {
    function rounded($value, $decimals = 0): string
    {
        $value = round($value, 0, PHP_ROUND_HALF_EVEN);

        return bcdiv($value, 1, $decimals);
    }
}

if (! function_exists('formatToCop')) {
    function formatToCop($value)
    {
        // Asegurar que el valor sea numérico antes de formatear
        if (!is_numeric($value)) {
            $value = 0;
        }

        // Convertir explícitamente a float para evitar problemas de tipo
        $value = (float) $value;

        // Usar number_format con configuración explícita
        // Punto (.) para separador decimal, coma (,) para separador de miles
        return '$ ' . number_format($value, 0, '.', ',');
    }
}

if (! function_exists('getUrlLogo')) {
    function getUrlLogo()
    {
        if (Storage::exists('public/images/logos/logo.png')) {
            return Storage::url('images/logos/logo.png');
        } else {
            return Storage::url('images/system/logo-default.png');
        }
    }
}

if (! function_exists('getTerminal')) {
    function getTerminal(): Terminal
    {
        $user = auth()->user();

        // Si no hay usuario autenticado (como en comandos artisan)
        if (!$user) {
            return new Terminal();
        }

        // Consulta directa más eficiente
        $terminal = Terminal::with('users')
            ->where('status', Terminal::ACTIVE)
            ->whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        return $terminal ?: new Terminal();
    }
}

if (! function_exists('getTerminalOpen')) {
    function getTerminalOpen(): bool
    {
        $user = auth()->user();

        // Si no hay usuario autenticado (como en comandos artisan)
        if (!$user) {
            return false;
        }

        // Consulta directa más eficiente
        $terminal = Terminal::with('users')
            ->where('status', Terminal::ACTIVE)
            ->whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        return $terminal->cashOpenings()->where('is_active', true)->first() ? true : false;
    }
}

if (! function_exists('hasTerminal')) {
    function hasTerminal(): bool
    {
        $terminal = getTerminal();

        return $terminal->id ? true : false;
    }
}

if (! function_exists('hasTerminalOpen')) {
    function hasTerminalOpen(): bool
    {
        return getTerminalOpen() ? true : false;
    }
}

if (! function_exists('getDays')) {
    function getDays(Carbon $from, $to, bool $text = true)
    {
        if ($to) {

            $days = $from->lte($to) ? $from->diffInDays($to) + 1 : 0;

            return $text ? $days.' días' : $days;
        }

        return 'No aplica';
    }
}

if (! function_exists('isRoot')) {
    function isRoot()
    {
        return session()->exists('root');
    }
}

if (! function_exists('calculateDiscountPercentage')) {
    function calculateDiscountPercentage(int $price, $amount, int $discount)
    {
        $total = $price * $amount;

        if ($discount === 0) {
            return 0;
        }

        if ($total <= 0 || $discount < 0) {
            throw new InvalidArgumentException('El valor de precio y el descuento deben igual o mayor a cero');
        }

        $percentage = ($discount / $total) * 100;

        return $percentage;
    }
}

if(!function_exists('factusIsEnabled')){
    function factusIsEnabled(): bool
    {
        return config('services.factus.enabled', false);
    }
}

if(!function_exists('centralDomain')){
    /**
     * Obtiene el dominio central de la aplicación
     * 
     * @param bool $withWww Si se debe incluir el subdominio www
     * @param bool $withProtocol Si se debe incluir el protocolo (http/https)
     * @return string El dominio central
     */
    function centralDomain(bool $withWww = false, bool $withProtocol = false): string
    {
        $domain = config('app.central_domain', env('CENTRAL_DOMAIN', 'dokploy.movete.cloud'));
        
        if ($withWww) {
            $domain = 'www.' . $domain;
        }
        
        if ($withProtocol) {
            $protocol = app()->environment('production') ? 'https' : 'http';
            $domain = $protocol . '://' . $domain;
        }
        
        return $domain;
    }
}

if(!function_exists('isTenantDomain')){
    /**
     * Verifica si el dominio actual es un subdominio de tenant
     * 
     * @param string|null $host El host a verificar (por defecto el host actual)
     * @return bool True si es un tenant, false si es el dominio central
     */
    function isTenantDomain(?string $host = null): bool
    {
        $host = $host ?? request()->getHost();
        $centralDomain = centralDomain();
        $centralDomainWww = centralDomain(withWww: true);
        
        // Si es el dominio central exacto o con www, no es tenant
        if ($host === $centralDomain || $host === $centralDomainWww) {
            return false;
        }
        
        // Si contiene el dominio central como sufijo, es un subdominio (tenant)
        return str_contains($host, '.' . $centralDomain);
    }
}

if(!function_exists('tenantSubdomain')){
    /**
     * Extrae el subdominio del tenant desde el host actual
     * 
     * @param string|null $host El host a analizar (por defecto el host actual)
     * @return string|null El subdominio del tenant o null si no es un tenant
     */
    function tenantSubdomain(?string $host = null): ?string
    {
        $host = $host ?? request()->getHost();
        
        if (!isTenantDomain($host)) {
            return null;
        }
        
        $centralDomain = centralDomain();
        
        // Extraer el subdominio removiendo el dominio central
        return str_replace('.' . $centralDomain, '', $host);
    }
}