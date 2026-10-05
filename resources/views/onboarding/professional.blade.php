<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Professional Profile Setup — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full flex flex-col justify-between selection:bg-sky-500 selection:text-white">

        <!-- Abstract Light Net Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>
        <div class="fixed inset-0 pointer-events-none opacity-[0.03] [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px] z-0"></div>

        <!-- Header -->
        <header class="relative z-10 w-full px-6 lg:px-12 py-5 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-10 h-10 object-contain rounded-xl shrink-0" />
                    <span class="font-extrabold text-base text-slate-900">Skill Link NG</span>
                </a>
                <a href="{{ route('onboarding') }}" class="text-xs font-bold text-slate-600 hover:text-sky-700">
                    ← Back to step 1
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">
                
                <div class="space-y-2">
                    <span class="text-xs font-extrabold text-sky-700 uppercase tracking-wider bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                        Step 2: Service Profile Details
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Set up your service listing
                    </h1>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        List your primary trade category, experience, and bio so clients looking for your skills can find you easily.
                    </p>
                </div>

                @php
                    $hasTeacher = $user->isTeacher();
                    $hasSkilledLabour = $user->isSkilledLabour();
                    $requiresStructuredLocation = $hasTeacher || $hasSkilledLabour;
                    $prof = $user->professionalProfile;
                @endphp

                @if(session('status'))
                    <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 text-emerald-800 text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 text-rose-700 text-sm font-medium space-y-1">
                        <p class="font-bold">Please correct the errors below before continuing:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ url('/onboarding/professional') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Category Selection -->
                    <div>
                        <label for="category_id" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Primary Category <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            required 
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all cursor-pointer"
                        >
                            <option value="" disabled {{ old('category_id', $prof?->category_id) ? '' : 'selected' }}>Select your trade or service area...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $prof?->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->parent ? $cat->parent->name . ' → ' : '' }}{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Public Display Name / Business Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="display_name" 
                            name="display_name" 
                            required
                            value="{{ old('display_name', $prof?->display_name ?? $user->name) }}"
                            placeholder="e.g. Master Plumber John"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                        />
                        @error('display_name')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experience & Location -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="years_of_experience" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Years of Experience <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="years_of_experience" 
                                name="years_of_experience" 
                                required
                                min="0" 
                                max="50"
                                value="{{ old('years_of_experience', $prof?->years_of_experience ?? 3) }}"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                            @error('years_of_experience')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                General Location <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="location" 
                                name="location" 
                                required
                                value="{{ old('location', $prof?->location ?? $user->location ?? 'Nigeria') }}"
                                placeholder="e.g. Ikeja, Lagos"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                            @error('location')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Structured Location -->
                    <fieldset class="border-2 border-slate-200 rounded-2xl p-4 space-y-4">
                        <legend class="text-xs font-extrabold text-slate-700 px-1 uppercase tracking-wide">
                            Structured Location
                            @if($requiresStructuredLocation)
                                <span class="text-rose-500">* (Required for local client search)</span>
                            @else
                                <span class="text-slate-400 font-normal">(Helps clients find you nearby)</span>
                            @endif
                        </legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="location_state" class="block text-xs font-bold text-slate-700 mb-1">
                                    State @if($requiresStructuredLocation)<span class="text-rose-500">*</span>@endif
                                </label>
                                <input type="text" id="location_state" name="location_state"
                                       @if($requiresStructuredLocation) required @endif
                                       value="{{ old('location_state', $prof?->location_state) }}"
                                       placeholder="e.g. Lagos"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                                @error('location_state')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="location_city" class="block text-xs font-bold text-slate-700 mb-1">
                                    City / LGA @if($requiresStructuredLocation)<span class="text-rose-500">*</span>@endif
                                </label>
                                <input type="text" id="location_city" name="location_city"
                                       @if($requiresStructuredLocation) required @endif
                                       value="{{ old('location_city', $prof?->location_city) }}"
                                       placeholder="e.g. Ikeja"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                                @error('location_city')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="location_neighbourhood" class="block text-xs font-bold text-slate-700 mb-1">
                                    Neighbourhood @if($requiresStructuredLocation)<span class="text-rose-500">*</span>@endif
                                </label>
                                <input type="text" id="location_neighbourhood" name="location_neighbourhood"
                                       @if($requiresStructuredLocation) required @endif
                                       value="{{ old('location_neighbourhood', $prof?->location_neighbourhood) }}"
                                       placeholder="e.g. Opebi"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                                @error('location_neighbourhood')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="location_landmark" class="block text-xs font-bold text-slate-700 mb-1">
                                    Nearest Landmark <span class="text-slate-400 font-normal">(optional — private)</span>
                                </label>
                                <input type="text" id="location_landmark" name="location_landmark"
                                       value="{{ old('location_landmark', $prof?->location_landmark) }}"
                                       placeholder="e.g. Near Allen Avenue junction"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                                @error('location_landmark')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Contact Phone Number <span class="text-rose-500">*</span> <span class="text-slate-500 text-xs font-normal">(Hidden until connection active)</span>
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            required
                            value="{{ old('phone', $prof?->phone ?? $user->phone) }}"
                            placeholder="e.g. +234 802 345 6789"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                        />
                        @error('phone')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Service Summary & Biography <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            id="bio" 
                            name="bio" 
                            rows="4" 
                            required
                            placeholder="Describe your services, trade background, equipment, or teaching approach..."
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl p-4 outline-none transition-all"
                        >{{ old('bio', $prof?->bio) }}</textarea>
                        @error('bio')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Skills Selection & Custom Skill Input -->
                    <div>
                        <label class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Specialized Skills <span class="text-slate-500 text-xs font-normal">(Select existing or add your own below)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 max-h-48 overflow-y-auto mb-3">
                            @foreach($skills as $sk)
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 hover:text-slate-900 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="skills[]" 
                                        value="{{ $sk->id }}"
                                        {{ in_array($sk->id, old('skills', $prof?->skills->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500"
                                    />
                                    <span class="truncate">{{ $sk->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Custom Skills Free Text Input -->
                        <div>
                            <label for="custom_skills" class="block text-xs font-bold text-slate-700 mb-1">
                                + Add Custom Skill(s) <span class="text-slate-400 font-normal">(Comma separated if not listed above)</span>
                            </label>
                            <input 
                                type="text" 
                                id="custom_skills" 
                                name="custom_skills" 
                                value="{{ old('custom_skills') }}"
                                placeholder="e.g. Solar Inverter Setup, SAT Prep, Data Science"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-md transition-all active:scale-98 cursor-pointer"
                        >
                            Save & Continue →
                        </button>
                    </div>
                </form>

            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200">
            &copy; {{ date('Y') }} Skill Link NG® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
