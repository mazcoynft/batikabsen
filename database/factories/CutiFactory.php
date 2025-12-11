<?php

namespace Database\Factories;

use App\Models\Cuti;
use Illuminate\Database\Eloquent\Factories\Factory;

class CutiFactory extends Factory
{
    protected $model = Cuti::class;

    public function definition(): array
    {
        return [
            'kode_cuti' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'nama_cuti' => $this->faker->words(2, true),
            'jml_hari' => $this->faker->numberBetween(1, 30),
            'potong_cuti' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function deductible(): static
    {
        return $this->state(fn (array $attributes) => [
            'potong_cuti' => true,
        ]);
    }

    public function nonDeductible(): static
    {
        return $this->state(fn (array $attributes) => [
            'potong_cuti' => false,
        ]);
    }
}