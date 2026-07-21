@extends('layouts.app')

@section('title', 'Katalog Produk - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Katalog Produk</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Belanja Produk IntegraMart</h1>
            <p class="mt-1 text-sm text-slate-400">Pilih produk aktif dan tambahkan ke keranjang belanja.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Lihat Keranjang</a>
    </div>

    <form method="GET" action="{{ route('shop.index') }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-[1fr_220px_180px_auto]">
            <input name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama produk" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            <select name="category" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="stock" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                <option value="">Semua stok</option>
                <option value="available" @selected(request('stock') === 'available')>Tersedia</option>
            </select>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Filter</button>
        </div>
    </form>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <a href="{{ route('shop.show', $product) }}" class="block">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-48 w-full object-cover">
                    @else
                        <div class="flex h-48 w-full items-center justify-center bg-slate-100 text-sm font-semibold text-slate-400">No Image</div>
                    @endif
                </a>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-indigo-600">{{ $product->category ?? 'Produk' }}</p>
                            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $product->name }}</h2>
                        </div>
                        @if ($product->stock > 0)
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20">{{ $product->stock }} stok</span>
                        @else
                            <span class="rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/20">Habis</span>
                        @endif
                    </div>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                    <p class="mt-4 text-xl font-semibold text-slate-900">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('shop.show', $product) }}" class="inline-flex flex-1 items-center justify-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">View Detail</a>
                    </div>

                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="mt-2 flex gap-2">
                        @csrf
                        <input name="qty" type="number" min="1" max="{{ max($product->stock, 1) }}" value="1" class="w-20 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" @disabled($product->stock <= 0)>
                        <button type="submit" class="flex-1 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300" @disabled($product->stock <= 0)>{{ $product->stock <= 0 ? 'Stok Habis' : 'Add to Cart' }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-slate-200 bg-white p-8 text-center text-sm text-slate-400 shadow-sm sm:col-span-2 lg:col-span-3">Produk tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection
