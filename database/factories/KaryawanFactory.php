<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => $this->faker->name(),
            'nik' => $this->faker->unique()->numerify('##########'),
            'jabatan' => $this->faker->jobTitle(),
            'no_hp' => $this->faker->phoneNumber(),
            'sisa_cuti_tahunan' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}