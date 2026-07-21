<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="max-w-3xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="sku" class="block text-sm font-semibold text-slate-900">SKU</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" required>
            @error('sku') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-semibold text-slate-900">Kategori</label>
            <input id="category" name="category" type="text" value="{{ old('category', $product->category) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            @error('category') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="name" class="block text-sm font-semibold text-slate-900">Nama Produk</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" required>
            @error('name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-semibold text-slate-900">Harga</label>
            <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" required>
            @error('price') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="stock" class="block text-sm font-semibold text-slate-900">Stok</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" required>
            @error('stock') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="image" class="block text-sm font-semibold text-slate-900">Gambar Produk</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            <p class="mt-1 text-xs text-slate-400">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
            @error('image') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror

            @if ($product->image_url)
                <div class="mt-3 flex items-center gap-3 rounded-md bg-slate-50 p-3 ring-1 ring-slate-200">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-md object-cover">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Gambar saat ini</p>
                        <p class="mt-1 break-all text-xs text-slate-400">{{ $product->image_path }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="sm:col-span-2">
            <label for="description" class="block text-sm font-semibold text-slate-900">Deskripsi</label>
            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">{{ old('description', $product->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600" @checked(old('is_active', $product->exists ? $product->is_active : true))>
                Produk aktif
            </label>
            @error('is_active') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
        <a href="{{ route('admin.products.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">Batal</a>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ $submitLabel }}</button>
    </div>
</form>
