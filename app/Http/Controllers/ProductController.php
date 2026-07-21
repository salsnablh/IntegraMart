<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request, $product);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk($this->imageDisk())->delete($product->image_path);
            }

            $data['image_path'] = $this->storeImage($request);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk($this->imageDisk())->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Product $product = null): array
    {
        $productId = $product?->id ?? 'NULL';

        return $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,'.$productId],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    private function storeImage(Request $request): string
    {
        $image = $request->file('image');
        $filename = Str::slug($request->string('name')->toString())
            .'-'.Str::random(12)
            .'.'.$image->getClientOriginalExtension();

        return $image->storeAs('products', $filename, $this->imageDisk());
    }

    private function imageDisk(): string
    {
        return config('filesystems.product_images_disk', 'public');
    }
}
