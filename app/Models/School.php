<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'logo', 'address', 'phone', 'email', 'website', 'description', 'vision', 'mission', 'kepala_sekolah', 'npsn', 'student_default_password'])]
class School extends Model
{
    public const FALLBACK_STUDENT_PASSWORD = 'SMKN1MAS';

    protected function casts(): array
    {
        return [
            'student_default_password' => 'encrypted',
        ];
    }

    public static function current(): ?self
    {
        return once(fn () => static::query()->first());
    }

    public static function studentDefaultPassword(): string
    {
        return static::current()?->student_default_password ?: self::FALLBACK_STUDENT_PASSWORD;
    }

    public static function defaultAccountPassword(): string
    {
        return static::studentDefaultPassword();
    }

    public function logoUrl(): ?string
    {
        return $this->logo && Storage::disk('public')->exists($this->logo)
            ? asset('storage/'.$this->logo)
            : null;
    }
}
