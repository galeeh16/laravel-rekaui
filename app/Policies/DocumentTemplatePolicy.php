<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;

/**
 * Templates are shared: every signed-in user can view and fill them, only the uploader can rename or delete one.
 */
class DocumentTemplatePolicy
{
    /**
     * Determine whether the user can update (rename) the template.
     */
    public function update(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $documentTemplate->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the template.
     */
    public function delete(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $documentTemplate->user_id === $user->id;
    }
}
