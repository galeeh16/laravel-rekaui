<?php

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentTemplate>
 */
class DocumentTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * The file at `path` is not created; tests that read it store a real .docx first.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'original_filename' => fake()->slug(2).'.docx',
            'path' => DocumentTemplate::STORAGE_DIRECTORY.'/'.fake()->uuid().'.docx',
            'size' => fake()->numberBetween(10_000, 200_000),
            'variables' => ['nama', 'nik', 'alamat_lengkap'],
        ];
    }
}
