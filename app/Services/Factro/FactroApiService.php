<?php
namespace App\Services\Factro;

use App\Models\NumberingRange;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FactroApiService
{
    public static function numberingRangesBorrar()
    {
        $cacheKey = 'factro_numbering_ranges';
        $ranges = Cache::get($cacheKey);
        if (!$ranges) {
            try {
                $ranges = NumberingRange::where('status', '1')->where('terminal_id', auth()->user()->terminal_id)
                    //->where('prefix', 'SETT')
                    ->where('expire', '>=', now())
                    ->orderBy('id')
                    ->get()
                    ->toArray();
                Log::info('Rangos de numeración cargados', ['count' => count($ranges), 'ranges' => $ranges]);
            } catch (Exception $e) {
                Log::error('Error cargando numbering_ranges', ['error' => $e->getMessage()]);
                throw $e;
            }
            Cache::put($cacheKey, $ranges, 3600);
        }

        if (empty($ranges)) {
            Log::warning('No rangos con expire, cargando todos activos');
            $allActive = NumberingRange::where('status', '1')
                ->where('prefix', 'SETT')
                ->orderBy('id')
                ->get()
                ->toArray();
            Log::info('Fallback rangos activos', ['count' => count($allActive), 'ranges' => $allActive]);
            if (empty($allActive)) {
                throw new Exception('No hay rangos de numeración activos para facturación electrónica');
            }
            return $allActive;
        }
        return $ranges;
    }

    public static function getNumberingRangeForBill($terminalId = null)
    {
        Log::info('Buscando numberingRange para facturación electrónica', ['terminal_id' => $terminalId]);
        $range = null;

        if ($terminalId) {
            $terminal = auth()->user()->terminals->where('id', $terminalId)->first();
            Log::info('Buscando terminal para numbering', ['terminal_id' => $terminalId, 'found' => $terminal ? true : false,
            'terminal' => $terminal ? $terminal->toArray() : null,
            'numberingRange' => $terminal->numberingRange ? $terminal->numberingRange->toArray() : null
        ]);

        Log::info('Buscando terminal para numberingRange', ['numberingRange' => $terminal->numberingRange ? $terminal->numberingRange->toArray() : null
        ]);

            $numberingRange = $terminal->numberingRange;
            ///$numberingRange = $terminal->numberingRange->findFirstWhere('expire', '>=', now());
            Log::info('Rango por terminal', ['numberingRange_id' => $numberingRange->id, 'numberingRange' => $numberingRange ? $numberingRange->toArray() : null]);
        }

        if (!$numberingRange) {
            throw new Exception('No se encontró un rango de numeración válido para Terminal');
        }

        if (!$numberingRange || $numberingRange->status !== '0') {
            throw new Exception('Rango de numeración no disponible o desactivado');
        }

        if (isset($numberingRange->expire) && $numberingRange->expire < now()) {
            throw new Exception('Rango de numeración vencido');
        }

        if (($numberingRange->current ?? 0) >= $numberingRange->to) {
            throw new Exception('Rango de numeración agotado');
        }

        Log::info('Rango de numeración obtenido', ['prefix' => $numberingRange->prefix ?? 'N/A', 'current' => $numberingRange->current ?? 0, 'expire' => $numberingRange->expire ?? 'N/A', 'status' => $numberingRange->status]);
        return $numberingRange;
    }

}
