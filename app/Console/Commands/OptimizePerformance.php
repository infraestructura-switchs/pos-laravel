<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-performance {action=optimize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimizar rendimiento de la aplicación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'optimize':
                $this->optimize();
                break;
            case 'clear':
                $this->clearAll();
                break;
            case 'test':
                $this->testCache();
                break;
            default:
                $this->error('Acción no válida. Usa: optimize, clear, o test');
        }
    }

    /**
     * Optimizar la aplicación
     */
    private function optimize()
    {
        $this->info('🚀 Optimizando aplicación...');
        $this->newLine();

        // Clear all caches first
        $this->info('1. Limpiando cachés antiguos...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('   ✓ Cachés limpiados');
        $this->newLine();

        // Optimize
        $this->info('2. Generando cachés optimizados...');
        Artisan::call('config:cache');
        $this->info('   ✓ Config cacheado');
        
        Artisan::call('route:cache');
        $this->info('   ✓ Rutas cacheadas');
        
        Artisan::call('view:cache');
        $this->info('   ✓ Vistas cacheadas');
        
        $this->newLine();

        // Test Redis connection
        $this->info('3. Verificando Redis...');
        try {
            Cache::store('redis')->put('test_optimization', 'working', 10);
            $value = Cache::store('redis')->get('test_optimization');
            
            if ($value === 'working') {
                $this->info('   ✓ Redis funcionando correctamente');
            } else {
                $this->warn('   ⚠ Redis no está guardando datos correctamente');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Redis no disponible: ' . $e->getMessage());
            $this->warn('   → Verifica que CACHE_DRIVER=redis en .env');
        }
        
        $this->newLine();
        $this->info('✅ Optimización completada!');
        $this->newLine();
    }

    /**
     * Limpiar todos los cachés
     */
    private function clearAll()
    {
        $this->info('🧹 Limpiando todos los cachés...');
        $this->newLine();

        Artisan::call('cache:clear');
        $this->info('✓ Cache cleared');
        
        Artisan::call('config:clear');
        $this->info('✓ Config cleared');
        
        Artisan::call('route:clear');
        $this->info('✓ Routes cleared');
        
        Artisan::call('view:clear');
        $this->info('✓ Views cleared');
        
        // Clear application cache
        Cache::flush();
        $this->info('✓ Application cache flushed');
        
        $this->newLine();
        $this->info('✅ Todos los cachés limpiados!');
    }

    /**
     * Probar configuración de caché
     */
    private function testCache()
    {
        $this->info('🧪 Probando configuración de caché...');
        $this->newLine();

        // Show current config
        $this->info('Configuración actual:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Cache Driver', config('cache.default')],
                ['Session Driver', config('session.driver')],
                ['Redis Host', config('database.redis.default.host')],
                ['Redis Port', config('database.redis.default.port')],
            ]
        );
        $this->newLine();

        // Test file cache
        $this->info('Test 1: File Cache');
        $start = microtime(true);
        Cache::store('file')->put('test_file', 'data', 60);
        Cache::store('file')->get('test_file');
        $fileTime = round((microtime(true) - $start) * 1000, 2);
        $this->info("   Tiempo: {$fileTime}ms");
        $this->newLine();

        // Test Redis cache
        $this->info('Test 2: Redis Cache');
        try {
            $start = microtime(true);
            Cache::store('redis')->put('test_redis', 'data', 60);
            Cache::store('redis')->get('test_redis');
            $redisTime = round((microtime(true) - $start) * 1000, 2);
            $this->info("   Tiempo: {$redisTime}ms");
            
            $improvement = round((($fileTime - $redisTime) / $fileTime) * 100, 1);
            $this->newLine();
            $this->info("📈 Redis es {$improvement}% más rápido que File");
        } catch (\Exception $e) {
            $this->error("   ✗ Error: " . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Configura Redis en .env:');
            $this->line('   CACHE_DRIVER=redis');
            $this->line('   SESSION_DRIVER=redis');
            $this->line('   REDIS_HOST=redis');
        }

        $this->newLine();
    }
}

