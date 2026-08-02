<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'phone', 'avatar', 'school_class', 'tahun_masuk', 'slug', 'bio', 'github_url', 'instagram_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'?s=200&d=mp';
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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
     *
     * @return \Illuminate\Support\Collection
     */
    public function skillSummary(): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\DB::table('skills')
            ->join('portfolio_skill', 'skills.id', '=', 'portfolio_skill.skill_id')
            ->join('portfolios', 'portfolios.id', '=', 'portfolio_skill.portfolio_id')
            ->where('portfolios.user_id', $this->id)
            ->where('portfolios.approval_status', 'approved')
            ->groupBy('skills.id', 'skills.name')
            ->selectRaw('skills.id, skills.name, AVG(portfolio_skill.level) as avg_level, COUNT(DISTINCT portfolios.id) as used_in')
            ->orderByDesc('avg_level')
            ->orderByDesc('used_in')
            ->get();
    }
}
