@extends('layouts.app')

@section('title', $product->name.' - IntegraMart')

@section('content')
    <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Kembali ke katalog</a>

    <section class="mt-4 grid gap-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-2">
        <div>
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-square w-full rounded-lg object-cover">
            @else
                <div class="flex aspect-square w-full items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-400">No Image</div>
            @endif
        </div>
        <div>
            <p class="text-sm font-semibold text-indigo-600">{{ $product->category ?? 'Produk' }}</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ $product->name }}</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
            <p class="mt-5 text-3xl font-semibold text-slate-900">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

            <div class="mt-4">
                @if ($product->stock > 5)
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-600/20">Stok tersedia: {{ $product->stock }}</span>
                @elseif ($product->stock > 0)
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700 ring-1 ring-amber-600/20">Stok menipis: {{ $product->stock }}</span>
                @else
                    <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-700 ring-1 ring-rose-600/20">Stok habis</span>
                @endif
            </div>

            <form method="POST" action="{{ route('cart.add', $product->id) }}" class="mt-6 flex max-w-sm gap-3">
                @csrf
                <input name="qty" type="number" min="1" max="{{ max($product->stock, 1) }}" value="{{ old('qty', 1) }}" class="w-24 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20" @disabled($product->stock <= 0)>
                <button type="submit" class="flex-1 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300" @disabled($product->stock <= 0)>
                    {{ $product->stock <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                </button>
            </form>
            @error('qty') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </section>
@endsection
