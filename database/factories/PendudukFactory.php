<?php

namespace Database\Factories;

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penduduk>
 */
class PendudukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-80 years', '-17 years')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(Gender::cases()),
            'alamat_lengkap' => fake()->address(),
            'agama' => fake()->randomElement(Religion::cases()),
            'status_perkawinan' => fake()->randomElement(MaritalStatus::cases()),
            'pekerjaan' => fake()->jobTitle(),
            'kewarganegaraan' => Citizenship::Wni,
        ];
    }
}
