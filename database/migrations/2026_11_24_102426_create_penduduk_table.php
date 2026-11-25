
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penduduk', function (Blueprint $table) {
            $table->id();

            $table->string('nik', 16)->unique();
            $table->string('nama', 100);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 20);
            $table->text('alamat_lengkap')->nullable();
            $table->string('agama', 30)->nullable();
            $table->string('status_perkawinan', 30)->nullable();
            $table->string('pekerjaan', 100)->nullable();

            $table->string('kewarganegaraan', 10)->default('WNI');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduk');
    }
};
