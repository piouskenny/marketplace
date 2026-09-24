<x-dashboard-layout 
    title="Edit Profile — {{ config('app.name', 'Skill Link NG') }}"
    active="settings"
>

            <!-- Main Form Viewport -->
            <main class="flex-1 min-w-0 space-y-6 max-w-4xl">
                
                <!-- Top Header Bar -->
                <header class="bg-white border border-slate-200/80 rounded-2xl p-3.5 sm:px-6 shadow-xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <!-- Desktop Sidebar Collapse Toggle Button -->
                        <button 
                            @click="toggleSidebar()" 
                            class="hidden lg:flex items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shrink-0 cursor-pointer transition-colors"
                            :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                        >
                            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </button>

                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer" aria-label="Open navigation menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Profile & Service Settings</h1>
                            <p class="text-xs text-slate-500 hidden sm:block">Manage your public listing, profile photo, and tutoring preferences.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Right Header Icons: Notifications Dropdown & Profile Avatar Trigger -->
                        <x-header-notifications :userNotifications="$userNotifications ?? []" :unreadCount="$unreadCount ?? 0" />

                        <!-- Header Profile Button Trigger -->
                        <button @click="profileModalOpen = true" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-xs font-semibold text-slate-800 hidden sm:inline">{{ $user->name ?? 'User' }}</span>
                        </button>
                    </div>
                </header>


                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="truncate sm:whitespace-normal">{{ session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 hover:bg-emerald-100 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3" title="Cancel notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-medium rounded-2xl p-4 space-y-1">
                        <div class="font-bold text-rose-950">Please fix the following issues:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-8">
                    <form action="{{ url('/profile/edit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Basic Account Information & Photo -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">1. Basic Information & Photo</h3>
                            
                            <!-- Profile Photo Upload Card -->
                            <div x-data="{ photoPreview: null }" class="flex flex-col sm:flex-row items-center gap-5 p-4 bg-slate-50 border border-slate-200/80 rounded-xl">
                                <div class="relative shrink-0">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="w-16 h-16 rounded-xl object-cover border border-slate-300 shadow-xs" />
                                    </template>
                                    <template x-if="!photoPreview">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-300 shadow-xs" />
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-xl shadow-xs">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </template>
                                </div>

                                <div class="space-y-1 flex-1 text-center sm:text-left">
                                    <label class="block text-xs font-semibold text-slate-900">Profile Photo</label>
                                    <p class="text-xs text-slate-500 font-normal">Upload a clear photo (JPG, PNG or WEBP, max 3MB). This will display on your profile and header.</p>
                                    
                                    <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                        <label class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-[#0F172B] text-white font-semibold text-xs hover:bg-slate-800 cursor-pointer shadow-xs transition-colors">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            <span>Upload Photo</span>
                                            <input 
                                                type="file" 
                                                name="avatar" 
                                                accept="image/*" 
                                                class="hidden" 
                                                @change="
                                                    const file = $event.target.files[0];
                                                    if (file) {
                                                        const reader = new FileReader();
                                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                                        reader.readAsDataURL(file);
                                                    }
                                                "
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Location / City</label>
                                <input type="text" name="location" value="{{ old('location', $user->location) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                            </div>
                        </div>

                        <!-- Section 2: Professional Profile Details -->
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Service & Professional Listing</h3>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Primary Category</label>
                                <select name="category_id" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ optional($user->professionalProfile)->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Professional Title / Headline</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', optional($user->professionalProfile)->display_name ?? $user->name) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Years of Experience</label>
                                    <input type="number" name="years_of_experience" value="{{ old('years_of_experience', optional($user->professionalProfile)->years_of_experience ?? 1) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Service Biography / Summary</label>
                                <textarea name="bio" rows="3" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-sm font-medium outline-none transition-all">{{ old('bio', optional($user->professionalProfile)->bio) }}</textarea>
                            </div>
                        </div>

                        <!-- Section 3: Academic Tutoring Specialization -->
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">3. Tutoring & Academic Options</h3>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Subjects Taught</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                                    @php
                                        $eduProfile = $user->professionalProfile?->educationProfile;
                                        $selectedSubjects = $eduProfile && $eduProfile->subjects ? $eduProfile->subjects->pluck('id')->toArray() : [];
                                    @endphp
                                    @foreach($subjects as $sub)
                                        <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" {{ in_array($sub->id, $selectedSubjects) ? 'checked' : '' }} class="accent-slate-900 rounded" />
                                            <span>{{ $sub->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Target Education Levels</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                                    @php
                                        $selectedLevels = $eduProfile && $eduProfile->educationLevels ? $eduProfile->educationLevels->pluck('id')->toArray() : [];
                                    @endphp
                                    @foreach($levels as $lvl)
                                        <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="level_ids[]" value="{{ $lvl->id }}" {{ in_array($lvl->id, $selectedLevels) ? 'checked' : '' }} class="accent-slate-900 rounded" />
                                            <span>{{ $lvl->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Email Notification Preferences -->
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">4. Email Notification Preferences</h3>
                            
                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <label for="job_alerts_enabled" class="block text-xs font-bold text-slate-900 cursor-pointer">
                                        Opportunity & Job Match Email Alerts
                                    </label>
                                    <p class="text-xs text-slate-500 font-normal">
                                        Receive automated email alerts whenever a new job matching your profile skills, location, or tutoring subjects is published.
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center">
                                    <input type="hidden" name="job_alerts_enabled" value="0" />
                                    <input 
                                        type="checkbox" 
                                        id="job_alerts_enabled"
                                        name="job_alerts_enabled" 
                                        value="1" 
                                        {{ old('job_alerts_enabled', $user->job_alerts_enabled ?? true) ? 'checked' : '' }}
                                        class="w-4 h-4 accent-[#0F172B] rounded cursor-pointer"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-6 rounded-xl shadow-xs transition-colors cursor-pointer">
                                Save Profile Changes →
                            </button>
                        </div>
                    </form>
                </div>

            </main>

            <!-- Profile Slide-Over Drawer Modal -->
            <x-profile-drawer :user="$user" />
</x-dashboard-layout>

