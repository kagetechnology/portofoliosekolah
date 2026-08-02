<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'logo', 'address', 'phone', 'email', 'website', 'description', 'vision', 'mission', 'kepala_sekolah', 'npsn'])]
class School extends Model
{
    public static function current(): ?self
    {
        return static::query()->first();
    }
}
