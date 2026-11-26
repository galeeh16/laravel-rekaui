<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The applicant's NIK and name and the template name are copied in, so the history still reads
     * correctly after the resident or the template is changed or deleted.
     */
    public function up(): void
    {
        Schema::create('document_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduk')->nullOnDelete();
            $table->string('applicant_nik', 16);
            $table->string('applicant_name', 100);
            $table->foreignId('document_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('template_name');
            $table->text('purpose')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('applicant_nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_applications');
    }
};
