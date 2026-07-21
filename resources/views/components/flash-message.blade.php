@if (session('success'))
    <div class="fixed right-4 top-4 z-50 w-[calc(100vw-2rem)] max-w-sm rounded-lg bg-white p-4 shadow-lg ring-1 ring-emerald-600/20" role="status">
        <div class="flex gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">
                <span class="text-sm font-bold">OK</span>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-900">Berhasil</p>
                <p class="mt-1 text-sm text-slate-600">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif
