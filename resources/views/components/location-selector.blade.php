@props(['searchLocation' => [], 'action' => ''])

<div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-2.5">
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Set Search Location</span>
        </div>
        @if(!empty($searchLocation['state']) || !empty($searchLocation['city']) || !empty($searchLocation['neighbourhood']))
            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                📍 {{ implode(', ', array_filter([$searchLocation['neighbourhood'] ?? null, $searchLocation['city'] ?? null, $searchLocation['state'] ?? null])) }}
            </span>
        @else
            <span class="text-[11px] text-slate-500 font-medium">No location set (showing all regions)</span>
        @endif
    </div>

    <form action="{{ $action }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2">
        <input 
            type="text" 
            name="location_state" 
            value="{{ $searchLocation['state'] ?? request('location_state', request('state')) }}" 
            placeholder="State (e.g. Lagos)" 
            class="bg-slate-50 border border-slate-200 text-xs text-slate-900 font-medium rounded-xl px-3 py-2 outline-none focus:border-slate-800 focus:bg-white transition-all"
        />

        <input 
            type="text" 
            name="location_city" 
            value="{{ $searchLocation['city'] ?? request('location_city', request('city')) }}" 
            placeholder="City / Area (e.g. Ikeja)" 
            class="bg-slate-50 border border-slate-200 text-xs text-slate-900 font-medium rounded-xl px-3 py-2 outline-none focus:border-slate-800 focus:bg-white transition-all"
        />

        <input 
            type="text" 
            name="location_neighbourhood" 
            value="{{ $searchLocation['neighbourhood'] ?? request('location_neighbourhood', request('neighbourhood')) }}" 
            placeholder="Neighbourhood (e.g. Opebi)" 
            class="bg-slate-50 border border-slate-200 text-xs text-slate-900 font-medium rounded-xl px-3 py-2 outline-none focus:border-slate-800 focus:bg-white transition-all"
        />

        <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-4 rounded-xl shadow-xs transition-colors cursor-pointer shrink-0">
            Search Nearby →
        </button>
    </form>
</div>
