<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'title', 'slug', 'description', 'category', 'project_type', 'cover_image', 'project_url', 'github_url', 'is_featured', 'approval_status', 'rejection_note', 'views'])]
class Portfolio extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            if (empty($p->slug)) {
                $base = Str::slug($p->title) ?: 'portfolio';
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $p->id ?? 0)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $p->slug = $slug;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'portfolio_skill')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'portfolio_contributors')
            ->withPivot(['status', 'responded_at'])
            ->withTimestamps();
    }

    public function acceptedContributors(): BelongsToMany
    {
        return $this->contributors()->wherePivot('status', 'accepted');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(PortfolioRating::class);
    }

    public function coverUrl(): string
    {
        return $this->cover_image && Storage::disk('public')->exists($this->cover_image)
            ? asset('storage/'.$this->cover_image)
            : asset('img/placeholder-portfolio.svg');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }
}
