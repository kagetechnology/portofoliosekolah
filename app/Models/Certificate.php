<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'title', 'certificate_number', 'slug', 'issuer', 'issue_date', 'file', 'approval_status'])]
class Certificate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $c) {
            if (empty($c->slug)) {
                $base = Str::slug($c->title) ?: 'sertifikat';
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $c->id ?? 0)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $c->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fileUrl(): ?string
    {
        return $this->file && Storage::disk('public')->exists($this->file)
            ? asset('storage/'.$this->file)
            : null;
    }

    public function isImage(): bool
    {
        if (! $this->file) {
            return false;
        }

        return (bool) preg_match('/\.(jpe?g|png|webp|gif)$/i', $this->file);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }
}
