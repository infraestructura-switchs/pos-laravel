<?php

namespace Database\Factories;

use App\Models\InventoryRemission;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryRemissionFactory extends Factory
{
    protected $model = InventoryRemission::class;

    public function definition()
    {
        return [
            'folio' => $this->faker->bothify('FOL-####'),
            'remission_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'concept' => $this->faker->randomElement(['Entrada', 'Salida', 'Ajuste']),
            'note' => $this->faker->optional()->text(100),
        ];
    }
}


