<?php

namespace App\Console\Commands;

use App\Services\Factus\HttpService;
use Illuminate\Console\Command;

class ListFactusBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factus:list-bills {--pending : Solo mostrar facturas pendientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista las facturas en Factus';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pendingOnly = $this->option('pending');

        $this->info('🔍 Consultando facturas en Factus...');
        $this->newLine();

        try {
            if ($pendingOnly) {
                // Consultar solo pendientes
                $response = HttpService::apiHttp()->get('bills/pending');
            } else {
                // Consultar todas las facturas
                $response = HttpService::apiHttp()->get('bills', ['per_page' => 20]);
            }

            $data = $response->json();

            if (isset($data['data'])) {
                $bills = $data['data'];

                if (empty($bills)) {
                    $this->info($pendingOnly ? '✅ No hay facturas pendientes' : 'ℹ️  No hay facturas');
                    return Command::SUCCESS;
                }

                $headers = ['Número', 'Estado', 'CUFE', 'Fecha'];
                $rows = [];

                foreach ($bills as $bill) {
                    $status = 'Desconocido';
                    
                    if (isset($bill['is_validated'])) {
                        $status = $bill['is_validated'] ? '✅ Validada' : '⚠️ Pendiente';
                    } elseif (isset($bill['status'])) {
                        $status = $bill['status'];
                    }

                    $rows[] = [
                        $bill['number'] ?? 'N/A',
                        $status,
                        isset($bill['cufe']) ? substr($bill['cufe'], 0, 30) . '...' : 'N/A',
                        $bill['created_at'] ?? 'N/A'
                    ];
                }

                $this->table($headers, $rows);
                $this->newLine();
                $this->info('Total: ' . count($bills) . ' facturas');

            } else {
                $this->warn('⚠️  Respuesta inesperada de Factus');
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al consultar Factus: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Verifica que:');
            $this->warn('   - Las credenciales sean válidas');
            $this->warn('   - La conexión a internet funcione');
            $this->warn('   - La API de Factus esté disponible');
            
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

