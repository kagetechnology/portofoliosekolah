<?php

namespace Tests\Unit;

use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConvertImagesToWebpTest extends TestCase
{
    public function test_converts_existing_disk_image_to_webp(): void
    {
        Storage::fake('public');

        // Create fake JPG
        $uploaded = UploadedFile::fake()->image('test-cover.jpg', 800, 600);
        $originalPath = $uploaded->store('portfolios', 'public');

        $this->assertTrue(Storage::disk('public')->exists($originalPath));
        $this->assertTrue(str_ends_with($originalPath, '.jpg'));

        // Convert to WebP
        $webpPath = ImageOptimizer::convertFile($originalPath, 800, 600, 80, true);

        $this->assertNotNull($webpPath);
        $this->assertTrue(str_ends_with($webpPath, '.webp'));
        $this->assertTrue(Storage::disk('public')->exists($webpPath));
        $this->assertFalse(Storage::disk('public')->exists($originalPath));
    }

    public function test_artisan_command_converts_all_images(): void
    {
        Storage::fake('public');

        $fakeJpg = UploadedFile::fake()->image('manual.jpg', 600, 400);
        $path = $fakeJpg->store('portfolios', 'public');

        $this->artisan('images:convert-to-webp', ['--quality' => 80])
            ->assertSuccessful();

        $webpPath = preg_replace('/\.jpg$/i', '.webp', $path);
        $this->assertTrue(Storage::disk('public')->exists($webpPath));
        $this->assertFalse(Storage::disk('public')->exists($path));
    }
}
