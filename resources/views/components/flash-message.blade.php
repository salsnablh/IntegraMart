@php
    $flashType = session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : null);
    $flashMessage = session('success') ?? session('error') ?? $errors->first();
@endphp

@if ($flashType && $flashMessage)
    <div class="fixed right-4 top-4 z-50 w-[calc(100vw-2rem)] max-w-sm rounded-lg bg-white p-4 shadow-lg ring-1 {{ $flashType === 'success' ? 'ring-emerald-600/20' : 'ring-rose-600/20' }}" role="status">
        <div class="flex gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full ring-1 {{ $flashType === 'success' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-rose-600/20' }}">
                <span class="text-sm font-bold">{{ $flashType === 'success' ? 'OK' : '!' }}</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ $flashType === 'success' ? 'Berhasil' : 'Perlu Dicek' }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $flashMessage }}</p>
            </div>
        </div>
    </div>
@endif
