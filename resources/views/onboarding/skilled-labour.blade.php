<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Skilled Trade Profile — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full flex flex-col justify-between selection:bg-amber-500 selection:text-white">

        <div class="fixed inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>

        <!-- Header -->
        <header class="relative z-10 w-full px-6 lg:px-12 py-5 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-10 h-10 object-contain rounded-xl shrink-0" />
                    <span class="font-extrabold text-base text-slate-900">Skill Link NG</span>
                </a>
                <a href="{{ route('onboarding.classification') }}" class="text-xs font-bold text-slate-600 hover:text-amber-700">
                    ← Back to classification
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl bg-white border-2 border-amber-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">

                <!-- Badge + Heading -->
                <div class="space-y-2">
                    <span class="text-xs font-extrabold text-amber-700 uppercase tracking-wider bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                        Skilled Labour Worker Profile
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Tell us about your trade
                    </h1>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        Your contact details and rating are private until a client pays the connection fee.
                        Fill in your trade information so local clients can find and hire you easily.
                    </p>
                </div>

                @if($errors->any())
                    <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 text-rose-700 text-sm font-medium space-y-1">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                @if(session('status'))
                    <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-4 text-emerald-800 text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ url('/onboarding/skilled-labour') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Trade Category -->
                    <div>
                        <label for="trade_category_id" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Your Trade / Specialisation
                        </label>
                        <select
                            id="trade_category_id"
                            name="trade_category_id"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all cursor-pointer"
                        >
                            <option value="">Select your trade (optional)...</option>
                            @foreach($tradeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ old('trade_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">e.g. Electrician, Plumber, Carpenter. Leave blank if your trade is not listed.</p>
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Public Display Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="display_name"
                            name="display_name"
                            value="{{ old('display_name', $user->name) }}"
                            placeholder="e.g. Master Electrician James"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                        />
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Service Summary <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="4"
                            required
                            placeholder="Describe your trade skills, tools, certifications, and years of experience. Clients see this first."
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl p-4 outline-none transition-all"
                        >{{ old('bio') }}</textarea>
                    </div>

                    <!-- Experience + Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="years_of_experience" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Years of Experience
                            </label>
                            <input
                                type="number"
                                id="years_of_experience"
                                name="years_of_experience"
                                min="0" max="50"
                                value="{{ old('years_of_experience', 3) }}"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Contact Phone <span class="text-rose-500">*</span>
                                <span class="text-slate-500 text-xs font-normal">(private until connected)</span>
                            </label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                required
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="+234 802 345 6789"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Location (free-text + structured) -->
                    <fieldset class="border-2 border-slate-200 rounded-2xl p-4 space-y-4">
                        <legend class="text-sm font-extrabold text-slate-900 px-1">Service Location <span class="text-rose-500">*</span></legend>

                        <div>
                            <label for="location" class="block text-xs font-bold text-slate-700 mb-1">
                                General Location <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="location"
                                name="location"
                                required
                                value="{{ old('location', $user->location) }}"
                                placeholder="e.g. Ikeja, Lagos"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="location_state" class="block text-xs font-bold text-slate-700 mb-1">State</label>
                                <input type="text" id="location_state" name="location_state"
                                       value="{{ old('location_state') }}"
                                       placeholder="e.g. Lagos"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                            </div>
                            <div>
                                <label for="location_city" class="block text-xs font-bold text-slate-700 mb-1">City / LGA</label>
                                <input type="text" id="location_city" name="location_city"
                                       value="{{ old('location_city') }}"
                                       placeholder="e.g. Ikeja"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                            </div>
                            <div>
                                <label for="location_neighbourhood" class="block text-xs font-bold text-slate-700 mb-1">Neighbourhood</label>
                                <input type="text" id="location_neighbourhood" name="location_neighbourhood"
                                       value="{{ old('location_neighbourhood') }}"
                                       placeholder="e.g. Opebi"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                            </div>
                            <div>
                                <label for="location_landmark" class="block text-xs font-bold text-slate-700 mb-1">
                                    Nearest Landmark <span class="text-slate-400 font-normal">(private)</span>
                                </label>
                                <input type="text" id="location_landmark" name="location_landmark"
                                       value="{{ old('location_landmark') }}"
                                       placeholder="e.g. Near Allen Avenue"
                                       class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all" />
                            </div>
                        </div>
                    </fieldset>

                    <!-- Skills -->
                    @if($skills->count())
                    <div>
                        <label class="block text-sm font-extrabold text-slate-900 mb-2">
                            Trade Skills &amp; Tools
                            <span class="text-slate-500 text-xs font-normal">(select all that apply)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($skills->take(30) as $skill)
                                <label class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2 cursor-pointer hover:bg-amber-50 hover:border-amber-300 transition-all has-[:checked]:bg-amber-50 has-[:checked]:border-amber-400">
                                    <input type="checkbox"
                                           name="skills[]"
                                           value="{{ $skill->id }}"
                                           class="rounded accent-amber-600"
                                           {{ in_array($skill->id, old('skills', [])) ? 'checked' : '' }}>
                                    <span class="text-xs font-medium text-slate-700">{{ $skill->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Certification -->
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_certified" value="0">
                            <input type="checkbox"
                                   id="is_certified"
                                   name="is_certified"
                                   value="1"
                                   class="w-5 h-5 rounded accent-amber-600 cursor-pointer"
                                   {{ old('is_certified') ? 'checked' : '' }}>
                            <span class="text-sm font-extrabold text-slate-900">I hold a formal certification, licence, or trade qualification</span>
                        </label>
                        <div id="cert_notes_wrap" class="{{ old('is_certified') ? '' : 'hidden' }}">
                            <label for="certification_notes" class="block text-xs font-bold text-slate-700 mb-1">Certification details</label>
                            <textarea id="certification_notes" name="certification_notes" rows="2"
                                      placeholder="e.g. COREN registered, NAFDAC certified, City & Guilds Level 3..."
                                      class="w-full bg-slate-50 border-2 border-slate-300 focus:border-amber-500 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl p-3 outline-none transition-all">{{ old('certification_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-md transition-all active:scale-98 cursor-pointer">
                            Complete Trade Profile →
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <footer class="relative z-10 w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200">
            &copy; {{ date('Y') }} Skill Link NG® Global LLC. All rights reserved.
        </footer>

        <script>
            // Toggle certification notes field
            const certCheckbox = document.getElementById('is_certified');
            const certNotes    = document.getElementById('cert_notes_wrap');
            certCheckbox.addEventListener('change', () => {
                certNotes.classList.toggle('hidden', !certCheckbox.checked);
            });
        </script>

        @livewireScripts
    </body>
</html>
