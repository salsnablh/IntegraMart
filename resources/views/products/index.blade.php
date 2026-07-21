@extends('layouts.admin')

@section('title', 'Inventory Produk - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Inventory Produk</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola produk, harga, stok, dan status ketersediaan.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
            Tambah Produk
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-normal text-slate-600">
                    <tr>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($products as $product)
                        <tr class="text-slate-600 hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600">{{ $product->sku }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-md border border-slate-200 object-cover shadow-sm">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-slate-50 text-xs font-semibold text-slate-400 ring-1 ring-slate-200">No Img</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                <div class="mt-1 max-w-md truncate text-xs text-slate-400">{{ $product->description ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $product->category ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                @if ($product->stock <= 0)
                                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/20">Habis</span>
                                @elseif ($product->stock <= 5)
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-600/20">{{ $product->stock }}</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20">{{ $product->stock }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ring-1 {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-rose-600/20' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-md border border-slate-200 bg-white px-3 py-1.5 font-medium text-indigo-600 shadow-sm hover:bg-slate-50">Edit</a>
                                    <button type="button" data-delete-url="{{ route('admin.products.destroy', $product) }}" data-product-name="{{ $product->name }}" class="js-delete-product rounded-md bg-rose-600 px-3 py-1.5 font-medium text-white shadow-sm hover:bg-rose-700">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-slate-900">Hapus produk?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Produk <span id="delete-product-name" class="font-semibold text-slate-900"></span> akan dihapus permanen dari inventory.</p>

            <form id="delete-product-form" method="POST" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" id="delete-cancel" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">Batal</button>
                <button type="submit" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700">Ya, Hapus</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('delete-modal');
        const form = document.getElementById('delete-product-form');
        const productName = document.getElementById('delete-product-name');
        const cancelButton = document.getElementById('delete-cancel');

        document.querySelectorAll('.js-delete-product').forEach((button) => {
            button.addEventListener('click', () => {
                form.action = button.dataset.deleteUrl;
                productName.textContent = button.dataset.productName;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        cancelButton.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    </script>
@endsection
