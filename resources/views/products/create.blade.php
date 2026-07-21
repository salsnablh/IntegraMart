@extends('layouts.admin')

@section('title', 'Tambah Produk - IntegraMart')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Kembali ke inventory</a>
        <h1 class="mt-3 text-2xl font-semibold text-slate-900">Tambah Produk</h1>
    </div>

    @include('products.form', [
        'action' => route('admin.products.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Produk',
    ])
@endsection
