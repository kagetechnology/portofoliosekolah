<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Skill extends Model
{
    public static function findOrCreateMany(array $names): array
    {
        $names = array_filter(array_map('trim', $names));
        $result = [];
        foreach ($names as $name) {
            $skill = static::firstOrCreate(['name' => $name]);
            $result[$skill->id] = $skill;
        }
        return $result;
    }

    public function portfolios(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class, 'portfolio_skill')
            ->withPivot('level')
            ->withTimestamps();
    }
}
