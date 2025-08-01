<?php

namespace App\Models;

use App\Enums\CashRegisters;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Output extends Model {
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'from' => CashRegisters::class
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function terminal(){
        return $this->belongsTo(Terminal::class);
    }


    public function names(): Attribute{
        return new Attribute(
            get: fn($value) => Str::title($value),
            set: fn($value) => Str::lower($value)
        );
    }

    public function email(): Attribute{
        return new Attribute(
            set: fn($value) => Str::lower($value)
        );
    }

    public function price(): Attribute{
        return new Attribute(
            set: fn($value) => $value ? $value : null
        );
    }

    public function scopeDate($query, $days, $startDate, $endDate){

        switch ($days) {
            case '1'://hoy
                return $query->whereDate('created_at', Carbon::today());
                break;
            case '2'://esta semana
                $startWeek = Carbon::today()->startOfWeek();
                $endWeek = Carbon::today()->endOfWeek()->endOfDay();
                return $query->whereBetween('created_at', [$startWeek, $endWeek]);
                break;

            case '3'://hace 7 dias
                $startDay = Carbon::today()->subDays(7);
                $endDay = Carbon::today()->endOfDay();
                return $query->whereBetween('created_at', [$startDay, $endDay]);
                break;

            case '4'://La semana pasada
                $startWeek = Carbon::now()->subWeek()->startOfWeek();
                $endWeek = Carbon::now()->subWeek()->endOfWeek()->endOfDay();
                return $query->whereBetween('created_at', [$startWeek, $endWeek]);
                break;

            case '5'://hace 5 dias
                $startDay = Carbon::today()->subDays(15);
                $endDay = Carbon::today()->endOfDay();
                return $query->whereBetween('created_at', [$startDay, $endDay]);
                break;

            case '6'://este mes
                $startWeek = Carbon::today()->startOfMonth();
                $endWeek = Carbon::today()->endOfMonth()->endOfDay();
                return $query->whereBetween('created_at', [$startWeek, $endWeek]);
                break;

            case '7'://mes pasado
                $startWeek = Carbon::today()->subMonth()->startOfMonth();
                $endWeek = Carbon::today()->subMonth()->endOfMonth()->endOfDay();
                return $query->whereBetween('created_at', [$startWeek, $endWeek]);
                break;

            case '8'://Rango de fechas
                if ($startDate && $endDate) {
                    $startDate = Carbon::parse($startDate);
                    $endDate = Carbon::parse($endDate)->endOfDay();
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                break;

            default:

                break;
        }
    }

}
