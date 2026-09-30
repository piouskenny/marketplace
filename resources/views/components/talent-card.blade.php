@props(['pro', 'type' => 'general'])

<div class="bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between space-y-4 group">
    <div class="space-y-3.5">
        
        <!-- Top Row: Avatar + Name + Rating -->
        <div class="flex items-start justify-between gap-3">
            <a href="{{ url('/profile/' . $pro->user_id) }}" class="flex items-center gap-3 min-w-0 group/link hover:opacity-80 transition-opacity">
                <div class="relative shrink-0">
                    @if($pro->user && $pro->user->avatar_url)
                        <img src="{{ $pro->user->avatar_url }}" alt="{{ $pro->user->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200" />
                    @else
                        <div class="w-11 h-11 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-sm shadow-xs">
                            {{ strtoupper(substr($pro->user->name ?? 'P', 0, 1)) }}
                        </div>
                    @endif
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0" title="Available"></span>
                </div>

                <div class="min-w-0">
                    <h3 class="text-sm font-bold text-slate-900 truncate group-hover/link:underline">
                        {{ $pro->user->name ?? 'Service Provider' }}
                    </h3>
                    <div class="flex flex-wrap gap-1 mt-0.5">
                        @if($pro->user && $pro->user->relationLoaded('talentTypes'))
                            @foreach($pro->user->talentTypes as $tt)
                                @if($tt->pivot->completed_at)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border 
                                        {{ $tt->slug === 'teacher' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($tt->slug === 'skilled_labour' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-sky-50 text-sky-700 border-sky-200') }}">
                                        {{ $tt->label }}
                                    </span>
                                @endif
                            @endforeach
                        @else
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                ✓ Verified Expert
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Rating Badge -->
            <div class="flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-lg shrink-0">
                <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span>{{ number_format($pro->average_rating ?? 5.0, 1) }}</span>
            </div>
        </div>

        <!-- Headline / Display Title -->
        <div>
            <a href="{{ url('/profile/' . $pro->user_id) }}" class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug hover:underline block">
                {{ $pro->display_name }}
            </a>
            @if($type === 'skilled_labour' && $pro->skilledLabourProfile && $pro->skilledLabourProfile->tradeCategory)
                <span class="text-[11px] font-semibold text-amber-800 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded inline-block mt-1">
                    🛠️ {{ $pro->skilledLabourProfile->tradeCategory->name }}
                </span>
            @endif
        </div>

        <!-- Location & Experience Metadata (NO phone, NO email, NO landmark) -->
        <div class="flex flex-wrap items-center gap-2.5 text-xs text-slate-500 font-medium pt-0.5">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $pro->location_city && $pro->location_state ? $pro->location_city . ', ' . $pro->location_state : $pro->location }}
            </span>
            <span>•</span>
            <span>{{ $pro->years_of_experience }} yrs exp</span>
            @if($pro->hourly_rate)
                <span>•</span>
                <span class="font-bold text-slate-900">₦{{ number_format($pro->hourly_rate) }}/hr</span>
            @endif
        </div>

        <!-- Bio Snippet -->
        <p class="text-xs text-slate-600 font-normal leading-relaxed line-clamp-2">
            {{ $pro->bio }}
        </p>

        <!-- Specialized Pills -->
        <div class="flex flex-wrap items-center gap-1.5 pt-1">
            @if($type === 'teacher' && $pro->educationProfile && $pro->educationProfile->subjects)
                @foreach($pro->educationProfile->subjects->take(3) as $sb)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        🎓 {{ $sb->name }}
                    </span>
                @endforeach
            @else
                @foreach($pro->skills->take(3) as $sk)
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                        {{ $sk->name }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Action Row -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <a href="{{ url('/profile/' . $pro->user_id) }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition-colors shrink-0">
            View Profile →
        </a>

        @auth
            <button 
                @click="openHireModal({
                    id: {{ $pro->id }},
                    user_id: {{ $pro->user_id }},
                    name: '{{ addslashes($pro->user->name ?? 'Provider') }}',
                    display_name: '{{ addslashes($pro->display_name) }}',
                    location: '{{ addslashes($pro->location_city && $pro->location_state ? $pro->location_city . ', ' . $pro->location_state : $pro->location) }}',
                    category: '{{ addslashes($pro->category->name ?? 'Talent') }}',
                    hourly_rate: '{{ $pro->hourly_rate ? '₦' . number_format($pro->hourly_rate) . '/hr' : 'Flexible Rate' }}'
                })" 
                class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors cursor-pointer shrink-0"
            >
                Connect & Hire →
            </button>
        @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors shrink-0">
                Log In to Connect →
            </a>
        @endauth
    </div>
</div>
