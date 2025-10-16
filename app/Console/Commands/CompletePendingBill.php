<?php

namespace App\Console\Commands;

use App\Services\Factus\HttpService;
use Illuminate\Console\Command;

class CompletePendingBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factus:complete-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Intenta completar el envío de facturas pendientes en Factus';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Buscando facturas pendientes en Factus...');
        $this->newLine();

        try {
            // Intentar obtener las facturas pendientes
            $response = HttpService::apiHttp()->get('bills/pending');
            $data = $response->json();

            if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
                $pendingBills = $data['data'];
                
                $this->warn("⚠️  Se encontraron " . count($pendingBills) . " factura(s) pendiente(s)");
                $this->newLine();

                foreach ($pendingBills as $index => $bill) {
                    $this->line("Factura #" . ($index + 1) . ":");
                    $this->line("  Número: " . ($bill['number'] ?? 'N/A'));
                    $this->line("  Estado: " . ($bill['status'] ?? 'N/A'));
                    $this->line("  Fecha: " . ($bill['created_at'] ?? 'N/A'));
                    
                    if ($this->confirm('¿Deseas intentar completar esta factura?', true)) {
                        $this->info('📤 Intentando completar factura...');
                        
                        try {
                            // Intentar enviar/completar la factura
                            $completeResponse = HttpService::apiHttp()
                                ->post("bills/{$bill['number']}/send");
                            
                            $completeData = $completeResponse->json();
                            
                            if ($completeResponse->successful()) {
                                $this->info('✅ Factura completada exitosamente');
                            } else {
                                $this->error('❌ Error al completar factura');
                                $this->line('Respuesta: ' . json_encode($completeData, JSON_PRETTY_PRINT));
                            }
                        } catch (\Exception $e) {
                            $this->error('❌ Error: ' . $e->getMessage());
                        }
                    }
                    
                    $this->newLine();
                }
            } else {
                $this->info('✅ No hay facturas pendientes en Factus');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al consultar Factus: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Esto puede significar que:');
            $this->warn('   - No hay endpoint para consultar pendientes');
            $this->warn('   - Necesitas contactar directamente con Factus');
            
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

