<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestWhatsappWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {phone?} {--pdf-url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío al webhook de WhatsApp de N8N con datos de ejemplo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Iniciando prueba de webhook de WhatsApp...');
        $this->newLine();

        // Obtener configuración
        $endpoint = config('services.n8n.whatsapp_webhook_url');
        $timeout = config('services.n8n.timeout');

        $this->info('📋 Configuración:');
        $this->line("   Endpoint: {$endpoint}");
        $this->line("   Timeout: {$timeout}s");
        $this->newLine();

        // Obtener número de teléfono
        $phone = $this->argument('phone') ?? $this->ask('Ingresa el número de teléfono de destino (solo dígitos)', '573001234567');
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) < 10) {
            $this->error('❌ El número de teléfono debe tener al menos 10 dígitos');
            return 1;
        }

        // Obtener URL del PDF (usa una de prueba si no se proporciona)
        $pdfUrl = $this->option('pdf-url') ?? $this->ask(
            'Ingresa la URL del PDF (deja vacío para usar URL de prueba)',
            'https://res.cloudinary.com/demo/raw/upload/sample.pdf'
        );

        $this->newLine();
        $this->info('📤 Preparando envío...');
        
        // Construir payload igual que en el servicio
        $payload = [
            'numberDestino' => (int) $digits,
            'fileName' => $pdfUrl,
        ];

        $this->line('   Payload:');
        $this->line('   ' . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->newLine();

        // Registrar en el log de WhatsApp
        Log::channel('whatsapp')->info('🧪 TEST - Enviando desde comando CLI', [
            'endpoint' => $endpoint,
            'payload' => $payload,
            'phone' => $digits
        ]);

        try {
            $this->info('⏳ Enviando request a N8N...');
            
            $startTime = microtime(true);
            
            $response = Http::timeout((int) $timeout)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            $this->newLine();
            $this->info("✅ Respuesta recibida en {$duration}ms");
            $this->line('   Status Code: ' . $response->status());
            $this->line('   Successful: ' . ($response->successful() ? 'Sí' : 'No'));
            $this->newLine();

            // Mostrar headers de respuesta
            $this->info('📋 Headers de respuesta:');
            foreach ($response->headers() as $key => $values) {
                $this->line("   {$key}: " . implode(', ', $values));
            }
            $this->newLine();

            // Mostrar body de respuesta
            $this->info('📄 Body de respuesta:');
            $body = $response->body();
            if (empty($body)) {
                $this->warn('   (vacío)');
            } else {
                $bodyJson = $response->json();
                if ($bodyJson) {
                    $this->line('   ' . json_encode($bodyJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } else {
                    $this->line('   ' . $body);
                }
            }
            $this->newLine();

            // Registrar respuesta en log
            Log::channel('whatsapp')->info('🧪 TEST - Respuesta de N8N', [
                'status' => $response->status(),
                'duration_ms' => $duration,
                'body' => $response->body(),
                'successful' => $response->successful(),
                'headers' => $response->headers()
            ]);

            // Análisis de la respuesta
            if ($response->successful()) {
                $this->info('✅ ÉXITO: El webhook respondió correctamente');
                
                if ($response->status() === 200) {
                    $this->info('   El mensaje debería llegar a WhatsApp en unos momentos.');
                }
            } else {
                $this->error('❌ ERROR: El webhook no respondió exitosamente');
                
                if ($response->status() === 404) {
                    $this->warn('   Código 404: El webhook no existe o no está registrado en N8N');
                    $this->line('   Verifica que:');
                    $this->line('   1. El workflow esté activado en N8N');
                    $this->line('   2. La URL del webhook sea correcta');
                    $this->line('   3. El endpoint "/webhook/factura" esté configurado en N8N');
                } elseif ($response->status() === 500) {
                    $this->warn('   Código 500: Error interno en el servidor N8N');
                    $this->line('   Revisa los logs de N8N para más detalles');
                } elseif ($response->status() >= 400) {
                    $this->warn("   Código {$response->status()}: Error del cliente o servidor");
                }
            }

            return $response->successful() ? 0 : 1;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error('❌ Error de conexión: No se pudo conectar con el servidor N8N');
            $this->line('   Mensaje: ' . $e->getMessage());
            $this->newLine();
            $this->warn('   Posibles causas:');
            $this->line('   1. El servidor N8N está caído o no responde');
            $this->line('   2. Problemas de red o DNS');
            $this->line('   3. La URL del webhook es incorrecta');

            Log::channel('whatsapp')->error('🧪 TEST - Error de conexión', [
                'exception' => $e->getMessage(),
                'endpoint' => $endpoint
            ]);

            return 1;
        } catch (\Throwable $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            $this->line('   Trace: ' . $e->getTraceAsString());

            Log::channel('whatsapp')->error('🧪 TEST - Error inesperado', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}

