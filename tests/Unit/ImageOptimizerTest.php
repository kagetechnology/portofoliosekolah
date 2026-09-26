<?php

namespace Tests\Unit;

use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    public function test_optimizes_and_resizes_large_uploaded_image_to_webp(): void
    {
        Storage::fake('public');

        $largeImage = UploadedFile::fake()->image('photo.jpg', 3000, 2000);
        $storedPath = ImageOptimizer::optimizeAndStore($largeImage, 'portfolios', 1600, 1000, 80);

        $this->assertTrue(str_ends_with($storedPath, '.webp'));
        $this->assertTrue(Storage::disk('public')->exists($storedPath));

        $savedContent = Storage::disk('public')->get($storedPath);
        $image = imagecreatefromstring($savedContent);

        $this->assertLessThanOrEqual(1600, imagesx($image));
        $this->assertLessThanOrEqual(1000, imagesy($image));
        $this->assertLessThan(50000, strlen($savedContent));
    }

    public function test_stores_non_image_file_without_gd_processing(): void
    {
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        $storedPath = ImageOptimizer::optimizeAndStore($pdf, 'certificates');

        $this->assertTrue(str_ends_with($storedPath, '.pdf'));
        $this->assertTrue(Storage::disk('public')->exists($storedPath));
    }
}
