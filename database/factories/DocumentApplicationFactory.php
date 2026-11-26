<?php

namespace Database\Factories;

use App\Models\DocumentApplication;
use App\Models\DocumentTemplate;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentApplication>
 */
class DocumentApplicationFactory extends Factory
{
    /**
     * Define the model's default state. The applicant and template names are copied from the related records.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'penduduk_id' => Penduduk::factory(),
            'applicant_nik' => fn (array $attributes): string => Penduduk::find($attributes['penduduk_id'])->nik,
            'applicant_name' => fn (array $attributes): string => Penduduk::find($attributes['penduduk_id'])->nama,
            'document_template_id' => DocumentTemplate::factory(),
            'template_name' => fn (array $attributes): string => DocumentTemplate::find($attributes['document_template_id'])->name,
            'purpose' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
