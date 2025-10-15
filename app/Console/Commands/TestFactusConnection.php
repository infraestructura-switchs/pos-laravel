<?php

namespace App\Console\Commands;

use App\Models\AccessToken;
use App\Services\FactusConfigurationService;
use App\Traits\TokenTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestFactusConnection extends Command
{
    use TokenTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factus:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la conexión con la API de Factus y validar las credenciales';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Probando conexión con Factus...');
        $this->newLine();

        try {
            // 1. Verificar si está habilitada la API
            $this->info('1. Verificando si la facturación electrónica está habilitada...');
            $isEnabled = FactusConfigurationService::isApiEnabled();
            
            if (!$isEnabled) {
                $this->warn('⚠️  La facturación electrónica NO está habilitada');
                $this->info('   Para habilitarla, actualiza la configuración en la base de datos.');
                return Command::FAILURE;
            }
            
            $this->info('✅ La facturación electrónica está HABILITADA');
            $this->newLine();

            // 2. Obtener configuración
            $this->info('2. Obteniendo configuración de Factus...');
            $config = FactusConfigurationService::apiConfiguration();
            
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['URL', $config['url']],
                    ['Client ID', substr($config['client_id'], 0, 20).'...'],
                    ['Client Secret', substr($config['client_secret'], 0, 20).'...'],
                    ['Email', $config['email']],
                    ['Password', str_repeat('*', strlen($config['password']))],
                ]
            );
            $this->newLine();

            // 3. Verificar token existente
            $this->info('3. Verificando token existente...');
            $existingToken = AccessToken::first();
            
            if ($existingToken) {
                $this->info('   Token encontrado:');
                $this->info('   - Creado: ' . $existingToken->created_at);
                $this->info('   - Expira: ' . $existingToken->expires_at);
                
                if ($existingToken->expires_at <= now()) {
                    $this->warn('   ⚠️  El token ha EXPIRADO');
                } else {
                    $this->info('   ✅ El token está VIGENTE');
                }
            } else {
                $this->warn('   ⚠️  No existe token en la base de datos');
            }
            $this->newLine();

            // 4. Probar autenticación
            $this->info('4. Probando autenticación con Factus...');
            
            $response = Http::acceptJson()->post($config['url'].'oauth/token', [
                'grant_type' => 'password',
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'username' => $config['email'],
                'password' => $config['password'],
            ]);

            $responseData = $response->json();
            $this->info('   Status Code: ' . $response->status());
            
            if ($response->status() === 200) {
                $this->info('   ✅ AUTENTICACIÓN EXITOSA');
                $this->info('   - Token recibido: ' . substr($responseData['access_token'] ?? '', 0, 50).'...');
                $this->info('   - Expira en: ' . ($responseData['expires_in'] ?? 'N/A') . ' segundos');
                
                // Preguntar si desea actualizar el token
                if ($this->confirm('¿Deseas actualizar el token en la base de datos?', true)) {
                    AccessToken::whereNotNull('id')->delete();
                    AccessToken::create([
                        'access_token' => $responseData['access_token'],
                        'refresh_token' => $responseData['refresh_token'],
                        'expires_at' => now()->addSecond($responseData['expires_in']),
                    ]);
                    $this->info('✅ Token actualizado correctamente');
                }
            } else {
                $this->error('   ❌ ERROR EN LA AUTENTICACIÓN');
                $this->error('   Mensaje: ' . ($responseData['message'] ?? 'N/A'));
                $this->error('   Error: ' . ($responseData['error'] ?? 'N/A'));
                $this->error('   Descripción: ' . ($responseData['error_description'] ?? 'N/A'));
                
                if (isset($responseData['error'])) {
                    $this->newLine();
                    $this->warn('💡 Sugerencias:');
                    
                    if ($responseData['error'] === 'invalid_client') {
                        $this->warn('   - Verifica el client_id y client_secret');
                        $this->warn('   - Asegúrate de que estás usando las credenciales correctas');
                        $this->warn('   - Verifica que la URL de la API sea correcta');
                    } elseif ($responseData['error'] === 'invalid_grant') {
                        $this->warn('   - Verifica el email y contraseña');
                        $this->warn('   - Asegúrate de que la cuenta esté activa');
                    }
                }
                
                return Command::FAILURE;
            }
            
            $this->newLine();
            $this->info('🎉 ¡Prueba completada exitosamente!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            $this->error('Línea: ' . $e->getLine());
            $this->error('Archivo: ' . $e->getFile());
            return Command::FAILURE;
        }
    }
}

