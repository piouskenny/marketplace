<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Choose Your Talent Classifications — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full flex flex-col justify-between selection:bg-sky-500 selection:text-white">

        <div class="fixed inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>

        <!-- Header -->
        <header class="relative z-10 w-full px-6 lg:px-12 py-5 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-10 h-10 object-contain rounded-xl shrink-0" />
                    <span class="font-extrabold text-base text-slate-900">Skill Link NG</span>
                </a>
                <a href="{{ route('onboarding') }}" class="text-xs font-bold text-slate-600 hover:text-sky-700">
                    ← Back
                </a>
            </div>
        </header>

        <!-- Main -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-8">

                <div class="space-y-2">
                    <span class="text-xs font-extrabold text-sky-700 uppercase tracking-wider bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                        Step 2 of 3 — How do you offer services?
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        How would you like to be listed?
                    </h1>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        Choose one or more categories that describe your work. You can hold multiple classifications
                        — for example, a software developer who also tutors mathematics can choose both
                        <strong>Professional</strong> and <strong>Teacher</strong>.
                    </p>
                </div>

                @if($errors->any())
                    <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-4 text-rose-700 text-sm font-medium space-y-1">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form action="{{ url('/onboarding/classification') }}" method="POST" class="space-y-4">
                    @csrf

                    @php
                        $classificationConfig = [
                            'professional' => [
                                'label'       => 'Professional',
                                'description' => 'Graphic designers, developers, accountants, photographers, consultants, event planners, and other professional service providers.',
                                'color'       => 'blue',
                                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                            ],
                            'teacher' => [
                                'label'       => 'Teacher',
                                'description' => 'Mathematics tutors, English teachers, music instructors, WAEC/JAMB/IELTS exam prep specialists, and academic educators.',
                                'color'       => 'emerald',
                                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>',
                            ],
                            'skilled_labour' => [
                                'label'       => 'Skilled Labour Worker',
                                'description' => 'Carpenters, plumbers, electricians, painters, mechanics, welders, tilers, AC technicians, and other skilled trade workers.',
                                'color'       => 'amber',
                                'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                            ],
                        ];
                        $colors = [
                            'blue'    => ['border' => 'border-blue-300', 'bg'   => 'bg-blue-50',    'check' => 'bg-blue-600',   'icon' => 'bg-blue-100 text-blue-700',   'ring' => 'ring-blue-400'],
                            'emerald' => ['border' => 'border-emerald-300', 'bg' => 'bg-emerald-50', 'check' => 'bg-emerald-600', 'icon' => 'bg-emerald-100 text-emerald-700', 'ring' => 'ring-emerald-400'],
                            'amber'   => ['border' => 'border-amber-300', 'bg' => 'bg-amber-50',    'check' => 'bg-amber-600',   'icon' => 'bg-amber-100 text-amber-700',  'ring' => 'ring-amber-400'],
                        ];
                    @endphp

                    @foreach($classificationConfig as $slug => $cfg)
                        @php $c = $colors[$cfg['color']]; @endphp
                        <label for="cls_{{ $slug }}"
                               class="flex items-start gap-4 border-2 {{ $c['border'] }} {{ $c['bg'] }} rounded-2xl p-5 cursor-pointer transition-all hover:shadow-md has-[:checked]:ring-2 has-[:checked]:{{ $c['ring'] }} has-[:checked]:border-transparent">

                            <!-- Custom Checkbox -->
                            <div class="mt-0.5 flex-shrink-0">
                                <input type="checkbox"
                                       id="cls_{{ $slug }}"
                                       name="classifications[]"
                                       value="{{ $slug }}"
                                       class="sr-only peer"
                                       {{ in_array($slug, old('classifications', [])) ? 'checked' : '' }}>
                                <div class="w-6 h-6 rounded-lg border-2 {{ $c['border'] }} bg-white peer-checked:{{ $c['check'] }} peer-checked:border-transparent flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>

                            <!-- Icon + Content -->
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl {{ $c['icon'] }} flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $cfg['icon'] !!}</svg>
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm sm:text-base">{{ $cfg['label'] }}</p>
                                    <p class="text-xs text-slate-600 font-medium leading-relaxed mt-0.5">{{ $cfg['description'] }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-md transition-all active:scale-98 cursor-pointer">
                            Continue with Selected Classifications →
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <form action="{{ route('onboarding.skip') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-slate-500 hover:text-slate-700 font-medium underline underline-offset-2 cursor-pointer">
                            Skip for now — I'll set this up later
                        </button>
                    </form>
                </div>
            </div>
        </main>

        <footer class="relative z-10 w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200">
            &copy; {{ date('Y') }} Skill Link NG® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
