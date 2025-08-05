<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:test {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la API REST';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = $this->option('url') ?: 'http://127.0.0.1:8000/api';
        
        $this->info('🧪 Probando API REST...');
        $this->info("URL Base: {$baseUrl}");
        $this->newLine();

        // Test 1: Health Check
        $this->info('1️⃣ Probando Health Check...');
        try {
            $response = Http::get("{$baseUrl}/health");
            if ($response->successful()) {
                $this->info('✅ Health Check exitoso');
                $this->line('Respuesta: ' . $response->body());
            } else {
                $this->error('❌ Health Check falló');
                $this->line('Código: ' . $response->status());
                $this->line('Respuesta: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Error en Health Check: ' . $e->getMessage());
        }
        $this->newLine();

        // Test 2: Login
        $this->info('2️⃣ Probando Login...');
        try {
            $response = Http::post("{$baseUrl}/auth/login", [
                'email' => 'test@mail.com',
                'password' => '12345678'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    $this->info('✅ Login exitoso');
                    $token = $data['data']['token'];
                    $this->line('Token obtenido: ' . substr($token, 0, 20) . '...');
                    
                    // Test 3: Obtener productos con token
                    $this->info('3️⃣ Probando obtener productos con token...');
                    $productsResponse = Http::withHeaders([
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json'
                    ])->get("{$baseUrl}/products");

                    if ($productsResponse->successful()) {
                        $this->info('✅ Obtener productos exitoso');
                        $productsData = $productsResponse->json();
                        $this->line('Productos encontrados: ' . ($productsData['total'] ?? 'N/A'));
                    } else {
                        $this->error('❌ Obtener productos falló');
                        $this->line('Código: ' . $productsResponse->status());
                        $this->line('Respuesta: ' . $productsResponse->body());
                    }

                    // Test 5: Logout
                    $this->info('5️⃣ Probando Logout...');
                    $logoutResponse = Http::withHeaders([
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json'
                    ])->post("{$baseUrl}/auth/logout");

                    if ($logoutResponse->successful()) {
                        $this->info('✅ Logout exitoso');
                        $this->info("message: {$logoutResponse}");
                    } else {
                        $this->error('❌ Logout falló');
                        $this->line('Código: ' . $logoutResponse->status());
                        $this->line('Respuesta: ' . $logoutResponse->body());
                    }

                } else {
                    $this->error('❌ Login falló');
                    $this->line('Respuesta: ' . $response->body());
                }
            } else {
                $this->error('❌ Login falló');
                $this->line('Código: ' . $response->status());
                $this->line('Respuesta: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Error en Login: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Pruebas completadas!');
        $this->info('📖 Revisa la documentación en API_DOCUMENTATION.md');
        $this->info('🧪 Usa el archivo API_TEST_EXAMPLES.html para más pruebas');
    }
} 