<?php

namespace App\Models;

use Database\Factories\DocumentApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A resident's (penduduk) application for a document, recorded by an officer.
 *
 * `applicant_nik`, `applicant_name` and `template_name` are copied in when the application is made,
 * so the history keeps reading correctly after the resident or the template changes or is deleted.
 */
#[Fillable(['penduduk_id', 'applicant_nik', 'applicant_name', 'document_template_id', 'template_name', 'purpose', 'user_id'])]
class DocumentApplication extends Model
{
    /** @use HasFactory<DocumentApplicationFactory> */
    use HasFactory;

    /**
     * The resident who applied, null once that resident is deleted.
     *
     * @return BelongsTo<Penduduk, $this>
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    /**
     * The requested document's template, null once that template is deleted.
     *
     * @return BelongsTo<DocumentTemplate, $this>
     */
    public function documentTemplate(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class);
    }

    /**
     * The officer who recorded the application.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
