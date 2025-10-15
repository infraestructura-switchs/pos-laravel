<?php

namespace App\Console\Commands;

use App\Services\Factus\HttpService;
use Illuminate\Console\Command;

class DeletePendingBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factus:delete-pending {number? : El número de la factura a eliminar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina una factura pendiente en Factus';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $billNumber = $this->argument('number');

        if (!$billNumber) {
            $this->info('🔍 Buscando facturas pendientes...');
            
            try {
                $response = HttpService::apiHttp()->get('bills/pending');
                $data = $response->json();

                if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
                    $pendingBills = $data['data'];
                    
                    $this->warn("⚠️  Se encontraron " . count($pendingBills) . " factura(s) pendiente(s)");
                    $this->newLine();

                    $options = [];
                    foreach ($pendingBills as $index => $bill) {
                        $options[] = $bill['number'] . ' - ' . ($bill['created_at'] ?? 'N/A');
                    }

                    $choice = $this->choice(
                        '¿Cuál factura deseas eliminar?',
                        $options,
                        0
                    );

                    $billNumber = explode(' - ', $choice)[0];
                } else {
                    $this->info('✅ No hay facturas pendientes');
                    return Command::SUCCESS;
                }
            } catch (\Exception $e) {
                $this->error('❌ Error al consultar pendientes: ' . $e->getMessage());
                $billNumber = $this->ask('Ingresa el número de la factura a eliminar manualmente');
                
                if (!$billNumber) {
                    return Command::FAILURE;
                }
            }
        }

        if (!$this->confirm("¿Estás seguro de eliminar la factura {$billNumber}?", false)) {
            $this->info('Operación cancelada');
            return Command::SUCCESS;
        }

        try {
            $this->info("🗑️  Eliminando factura {$billNumber}...");
            
            $response = HttpService::apiHttp()
                ->delete("bills/{$billNumber}");

            if ($response->successful()) {
                $this->info('✅ Factura eliminada exitosamente');
                return Command::SUCCESS;
            } else {
                $data = $response->json();
                $this->error('❌ Error al eliminar factura');
                $this->line('Respuesta: ' . json_encode($data, JSON_PRETTY_PRINT));
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Puede que necesites contactar con soporte de Factus');
            return Command::FAILURE;
        }
    }
}

