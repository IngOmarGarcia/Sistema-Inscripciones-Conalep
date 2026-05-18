<div class="flex items-center justify-center pt-6">
    @if($getState())
        <a href="{{ \Illuminate\Support\Facades\Storage::url($getState()) }}" 
           target="_blank"
           class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-white bg-primary-600 rounded-lg shadow-sm hover:bg-primary-500">
            <x-heroicon-m-arrow-down-tray class="w-4 h-4"/>
            Ver PDF
        </a>
    @else
        <span class="text-xs text-gray-400 italic">Sin archivo</span>
    @endif
</div>