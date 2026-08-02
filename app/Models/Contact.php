<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sender_name', 'sender_email', 'sender_company', 'sender_phone', 'school_id', 'subject', 'message', 'is_read'])]
class Contact extends Model
{
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
