<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['nisn', 'name', 'email', 'password', 'must_change_password', 'role', 'status', 'phone', 'avatar', 'school_class', 'tahun_masuk', 'slug', 'bio', 'github_url', 'instagram_url', 'linkedin_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function avatarUrl(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email ?: $this->nisn ?: $this->name)).'?s=200&d=mp';
    }

    protected static function booted(): void
    {
        static::saving(function (self $user) {
            if (empty($user->slug)) {
                $user->slug = static::uniqueSlug($user->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'user';
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function contributedPortfolios(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class, 'portfolio_contributors')
            ->withPivot(['status', 'responded_at'])
            ->withTimestamps();
    }

    public function acceptedContributedPortfolios(): BelongsToMany
    {
        return $this->contributedPortfolios()->wherePivot('status', 'accepted');
    }

    public function visiblePortfolios(): Builder
    {
        return Portfolio::query()->where(fn (Builder $query) => $query
            ->where('user_id', $this->id)
            ->orWhereHas('contributors', fn (Builder $contributors) => $contributors
                ->where('users.id', $this->id)
                ->where('portfolio_contributors.status', 'accepted')));
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function portfolioRatings(): HasMany
    {
        return $this->hasMany(PortfolioRating::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'portfolio_skill')
            ->join('portfolios', 'portfolios.id', '=', 'portfolio_skill.portfolio_id')
            ->where('portfolios.user_id', $this->id)
            ->select('skills.*', 'portfolio_skill.level as pivot_level')
            ->withPivot('level');
    }

    /**
     * Return grouped skills with aggregated avg level + usage count.
     */
    public function skillSummary(): Collection
    {
        $portfolioIds = $this->visiblePortfolios()
            ->where('approval_status', 'approved')
            ->pluck('portfolios.id');

        return DB::table('skills')
            ->join('portfolio_skill', 'skills.id', '=', 'portfolio_skill.skill_id')
            ->whereIn('portfolio_skill.portfolio_id', $portfolioIds)
            ->groupBy('skills.id', 'skills.name')
            ->selectRaw('skills.id, skills.name, AVG(portfolio_skill.level) as avg_level, COUNT(DISTINCT portfolio_skill.portfolio_id) as used_in')
            ->orderByDesc('avg_level')
            ->orderByDesc('used_in')
            ->get();
    }
}
