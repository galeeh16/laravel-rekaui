<?php

namespace App\Models;

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use Database\Factories\PendudukFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table('penduduk')]
#[Guarded(['id'])]
class Penduduk extends Model
{
    /** @use HasFactory<PendudukFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'jenis_kelamin' => Gender::class,
            'agama' => Religion::class,
            'status_perkawinan' => MaritalStatus::class,
            'kewarganegaraan' => Citizenship::class,
        ];
    }
}
