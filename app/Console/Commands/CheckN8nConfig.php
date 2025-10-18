<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckN8nConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'n8n:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la configuración del webhook de N8N y su conectividad';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de N8N...');
        $this->newLine();

        // Mostrar configuración actual
        $webhookUrl = config('services.n8n.whatsapp_webhook_url');
        $timeout = config('services.n8n.timeout');

        $this->info('📋 Configuración actual:');
        $this->line("   URL del webhook: {$webhookUrl}");
        $this->line("   Timeout: {$timeout} segundos");
        $this->newLine();

        // Verificar variable de entorno
        $envValue = env('N8N_WHATSAPP_WEBHOOK_URL');
        if ($envValue) {
            $this->info('🔧 Variable de entorno N8N_WHATSAPP_WEBHOOK_URL:');
            $this->line("   {$envValue}");
            
            if ($envValue !== $webhookUrl) {
                $this->warn('⚠️  La variable de entorno es diferente a la configuración cacheada');
                $this->warn('   Ejecuta: php artisan config:clear && php artisan config:cache');
            }
        } else {
            $this->info('ℹ️  No hay variable N8N_WHATSAPP_WEBHOOK_URL en .env (usando valor por defecto)');
        }
        $this->newLine();

        // Verificar si la URL es la correcta
        $correctUrl = 'https://n8nserver.movete.cloud/webhook/factura';
        $oldUrl = 'https://n8n-vwj1.onrender.com/webhook/factura';

        if ($webhookUrl === $oldUrl) {
            $this->error('❌ PROBLEMA DETECTADO: Estás usando la URL VIEJA de N8N');
            $this->error("   URL actual: {$oldUrl}");
            $this->info("   URL correcta: {$correctUrl}");
            $this->newLine();
            $this->info('📝 Para corregir:');
            $this->line('   1. Abre tu archivo .env');
            $this->line('   2. Cambia o agrega la línea:');
            $this->line("      N8N_WHATSAPP_WEBHOOK_URL={$correctUrl}");
            $this->line('   3. Ejecuta: php artisan config:clear && php artisan config:cache');
            $this->line('   4. Reinicia tu servidor');
            return 1;
        } elseif ($webhookUrl === $correctUrl) {
            $this->info('✅ La URL del webhook es correcta');
        } else {
            $this->warn("⚠️  URL personalizada detectada: {$webhookUrl}");
        }
        $this->newLine();

        // Probar conectividad
        if ($this->confirm('¿Deseas probar la conectividad con el webhook de N8N?', true)) {
            $this->info('🔌 Probando conectividad...');
            
            try {
                $response = Http::timeout($timeout)
                    ->acceptJson()
                    ->asJson()
                    ->post($webhookUrl, [
                        'test' => true,
                        'numberDestino' => 123456789,
                        'fileName' => 'https://example.com/test.pdf'
                    ]);

                $this->newLine();
                $this->info("📊 Respuesta del servidor:");
                $this->line("   Status: {$response->status()}");
                $this->line("   Mensaje: {$response->body()}");
                
                if ($response->successful()) {
                    $this->info('✅ Conexión exitosa con el webhook');
                } elseif ($response->status() === 404) {
                    $this->error('❌ Error 404: El webhook no está registrado en N8N');
                    $this->newLine();
                    $this->info('💡 Posibles causas:');
                    $this->line('   1. El workflow en N8N no está activado (toggle en la esquina superior derecha)');
                    $this->line('   2. El webhook está configurado como "Test URL" en lugar de "Production URL"');
                    $this->line('   3. El endpoint del webhook en N8N no coincide con "/webhook/factura"');
                } else {
                    $this->warn("⚠️  Respuesta inesperada del servidor (status: {$response->status()})");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error de conexión: {$e->getMessage()}");
                $this->newLine();
                $this->info('💡 Verifica:');
                $this->line('   1. Que el servidor N8N esté funcionando');
                $this->line('   2. Que no haya problemas de red o firewall');
                $this->line('   3. Que la URL sea correcta');
            }
        }

        $this->newLine();
        return 0;
    }
}

