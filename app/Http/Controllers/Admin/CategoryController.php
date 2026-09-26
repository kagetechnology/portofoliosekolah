<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $categories = Category::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $usage = Portfolio::whereNotNull('category')
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.categories.index', compact('categories', 'q', 'usage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['name' => trim($request->string('name')->toString())]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        Category::create($data);

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->merge(['name' => trim($request->string('name')->toString())]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category)],
        ]);

        DB::transaction(function () use ($category, $data): void {
            Portfolio::where('category', $category->name)->update(['category' => $data['name']]);
            $category->update($data);
        });

        return back()->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if (Portfolio::where('category', $category->name)->exists()) {
            return back()->withErrors(['category' => 'Kategori masih digunakan portfolio dan tidak dapat dihapus.']);
        }

        $category->delete();

        return back()->with('status', 'Kategori dihapus.');
    }
}
