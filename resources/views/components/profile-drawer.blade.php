@props(['user' => null, 'completionPercentage' => null])

@php
    $user = $user ?? Auth::user();
    if ($user) {
        $user->loadMissing(['professionalProfile.category', 'professionalProfile.skills', 'professionalProfile.educationProfile.subjects', 'professionalProfile.educationProfile.educationLevels']);
    }
    
    if ($completionPercentage === null && $user) {
        $calc = 35;
        if (!empty($user->phone)) {
            $calc += 15;
        }
        if (!empty($user->location)) {
            $calc += 15;
        }
        if ($user->professionalProfile) {
            $calc += 20;
            if ($user->professionalProfile->educationProfile || $user->professionalProfile->skills->isNotEmpty()) {
                $calc += 15;
            }
        }
        if ($user->onboarding_completed) {
            $calc = 100;
        }
        $completionPercentage = $calc;
    }
@endphp

<!-- Profile Details Slide-Over Drawer Popup -->
<div 
    x-show="profileModalOpen" 
    x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="profileModalOpen = false" 
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex justify-end"
    style="display: none;"
>
    <!-- Drawer Body -->
    <div 
        @click.stop
        x-show="profileModalOpen"
        x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="w-full max-w-md bg-white h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto no-scrollbar border-l border-slate-200/80"
    >
        <div class="space-y-6">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <span class="text-sm font-bold text-slate-900">My Profile Card</span>
                </div>
                <button @click="profileModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Profile Avatar Card -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center space-y-3">
                <div class="relative inline-block">
                    @if($user && $user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border border-slate-300 shadow-xs mx-auto" />
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-2xl shadow-xs mx-auto">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0" title="Online & Active"></span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ $user->name ?? 'User' }}</h3>
                    <p class="text-xs text-slate-500 font-medium">{{ $user->email ?? '' }}</p>
                </div>

                <div class="pt-1 flex justify-center">
                    @if($completionPercentage == 100)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            ✓ Verified Profile (100%)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-800 border border-slate-300">
                            Profile {{ $completionPercentage }}% Complete
                        </span>
                    @endif
                </div>
            </div>

            <!-- Details Table -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Overview</h4>
                
                <div class="bg-white border border-slate-200/80 rounded-xl divide-y divide-slate-100 text-xs">
                    <div class="p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Full Name</span>
                        <span class="font-semibold text-slate-900">{{ $user->name ?? 'User' }}</span>
                    </div>
                    <div class="p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Email Address</span>
                        <span class="font-semibold text-slate-900 truncate max-w-[200px]">{{ $user->email ?? '' }}</span>
                    </div>
                    <div class="p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Phone Number</span>
                        <span class="font-semibold text-slate-900">{{ $user->phone ?? 'Not specified' }}</span>
                    </div>
                    <div class="p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Location</span>
                        <span class="font-semibold text-slate-900">{{ $user->location ?? 'Nigeria' }}</span>
                    </div>
                    <div class="p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Primary Category</span>
                        <span class="font-semibold text-slate-900">{{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}</span>
                    </div>
                </div>

                @if($user && $user->professionalProfile && $user->professionalProfile->bio)
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Service Biography</h4>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-xs text-slate-700 leading-relaxed font-normal">
                            {{ $user->professionalProfile->bio }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Action Buttons -->
        <div class="pt-6 border-t border-slate-200/80 space-y-3">
            <a href="{{ url('/profile/edit') }}" @click="profileModalOpen = false" class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                <span>Edit Full Profile & Services →</span>
            </a>
        </div>
    </div>
</div>
