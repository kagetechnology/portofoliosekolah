<?php

namespace App\Http\Controllers;

use App\Support\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $path = ImageOptimizer::optimizeAndStore(
            $data['upload'],
            'editor',
            1200,
            1200,
            80
        );

        return response()->json([
            'uploaded' => true,
            'url' => asset('storage/'.$path),
        ]);
    }
}
