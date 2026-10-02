<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ 
    selectedJob: null, 
    applyModalOpen: false,
    openApplyModal(job) {
        this.selectedJob = job;
        this.applyModalOpen = true;
    }
}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>Explore & Search Jobs — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Modern Clean Typography (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-100/70 font-sans antialiased text-slate-900 min-h-full selection:bg-slate-900 selection:text-white flex flex-col justify-between">

        <div>
            <!-- Header Navigation -->
            <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
                    
                    <!-- Brand Logo -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-9 h-9 object-contain shrink-0" />
                        <div>
                            <span class="text-slate-900 font-bold text-base tracking-tight block">Skill Link NG</span>
                            <span class="text-[11px] text-slate-500 font-medium tracking-wide">Jobs & Opportunities</span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600">
                        <a href="{{ url('/') }}" class="hover:text-slate-900 transition-colors">Home</a>
                        <a href="{{ url('/jobs') }}" class="text-[#0F172B] font-bold">Find Jobs</a>
                        <a href="{{ url('/talent') }}" class="hover:text-slate-900 transition-colors">Find Talent</a>
                        <a href="{{ url('/dashboard') }}" class="hover:text-slate-900 transition-colors">Dashboard</a>
                    </nav>

                    <!-- Auth Quick Action -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard/jobs') }}" class="inline-flex items-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                <span>My Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 px-3 py-2">Log In</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-4 rounded-xl shadow-xs transition-colors">
                                Sign Up Free
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Hero Search Engine Banner -->
            <section class="bg-[#0F172B] text-white py-10 sm:py-14 px-4 sm:px-6 lg:px-8 shadow-sm">
                <div class="max-w-4xl mx-auto text-center space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-medium">
                        <span class="w-2 h-2 rounded-full bg-[#E8472A]"></span>
                        <span>Active Verified Job Listings</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white">
                        Find Jobs, Projects & Quick Tasks Near You
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-base max-w-2xl mx-auto font-normal leading-relaxed">
                        Explore opportunities for skilled labour workers (electricians, plumbers, carpenters), academic tutors, and certified professionals across Nigeria.
                    </p>

                    <!-- Search Filter Form -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="pt-2 space-y-3">
                        <div class="bg-white p-3 rounded-2xl shadow-xl flex flex-col lg:flex-row items-center gap-2.5 border border-slate-200">
                            <!-- Keyword Input -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input 
                                    type="text" 
                                    name="query"
                                    value="{{ $query }}"
                                    placeholder="Search job title, skills, or keywords (e.g. Electrician, Maths Tutor)..." 
                                    class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm rounded-xl pl-10 pr-3 py-2.5 outline-none transition-all"
                                />
                            </div>

                            <!-- Location Input -->
                            <div class="w-full lg:w-48 shrink-0">
                                <input 
                                    type="text" 
                                    name="location"
                                    value="{{ $location }}"
                                    placeholder="Location (e.g. Lagos)" 
                                    class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 text-xs rounded-xl px-3 py-2.5 outline-none font-medium"
                                />
                            </div>

                            <!-- Category Dropdown -->
                            <div class="w-full lg:w-48 shrink-0">
                                <select name="category_id" class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 text-xs rounded-xl px-3 py-2.5 outline-none font-medium">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Button -->
                            <button type="submit" class="w-full lg:w-auto bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-md transition-all shrink-0 cursor-pointer">
                                Search Jobs →
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Main Content Container -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
                
                <div class="flex items-center justify-between gap-4 flex-wrap border-b border-slate-200/80 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Latest Openings</h2>
                        <p class="text-xs text-slate-500">Discover projects and tasks posted by verified clients</p>
                    </div>

                    <div class="text-xs font-semibold text-slate-500 shrink-0">
                        Showing <strong class="text-slate-900">{{ $opportunities->total() }}</strong> open job{{ $opportunities->total() === 1 ? '' : 's' }}
                    </div>
                </div>

                <!-- Jobs Grid Cards -->
                @if($opportunities->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($opportunities as $opp)
                            <div class="bg-white border border-slate-200/80 hover:border-blue-400 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between space-y-4 group">
                                <div class="space-y-3">
                                    <!-- Top Row: Category + Posted Date -->
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-[#2563EB] border border-blue-100 truncate max-w-[200px]">
                                            {{ $opp->category->name ?? 'General Task' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-medium shrink-0">
                                            {{ $opp->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-[#2563EB] transition-colors">
                                        {{ $opp->title }}
                                    </h3>

                                    <!-- Location & Type -->
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $opp->location }}
                                        </span>
                                        <span>•</span>
                                        <span class="capitalize px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                            {{ str_replace('_', ' ', $opp->opportunity_type) }}
                                        </span>
                                    </div>

                                    <!-- Budget -->
                                    <div class="text-xs font-bold text-slate-900 bg-slate-50 p-2 rounded-xl border border-slate-200/60 inline-block">
                                        💰 Budget: 
                                        @if($opp->budget_min || $opp->budget_max)
                                            ₦{{ number_format($opp->budget_min ?? 0) }} {{ $opp->budget_max ? '- ₦' . number_format($opp->budget_max) : '+' }}
                                        @else
                                            Flexible / Negotiable
                                        @endif
                                    </div>

                                    <!-- Description snippet -->
                                    <p class="text-xs text-slate-600 font-normal leading-relaxed line-clamp-3">
                                        {{ $opp->description }}
                                    </p>

                                    <!-- Education/Tutoring extra details if available -->
                                    @if($opp->educationDetails && $opp->educationDetails->subject)
                                        <div class="pt-1">
                                            <span class="px-2 py-1 rounded-md text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                                🎓 {{ $opp->educationDetails->subject->name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Footer -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-7 h-7 rounded-full bg-[#0F172B] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($opp->user->name ?? 'C', 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-slate-600 font-medium truncate">
                                            {{ $opp->user->name ?? 'Verified Employer' }}
                                        </span>
                                    </div>

                                    <button 
                                        @click="openApplyModal({
                                            id: {{ $opp->id }},
                                            title: '{{ addslashes($opp->title) }}',
                                            poster_name: '{{ addslashes($opp->user->name ?? 'Employer') }}',
                                            location: '{{ addslashes($opp->location) }}'
                                        })" 
                                        class="inline-flex items-center justify-center bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors shrink-0 cursor-pointer"
                                    >
                                        Apply Now →
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="pt-4">
                        {{ $opportunities->links() }}
                    </div>
                @else
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center space-y-3 max-w-lg mx-auto my-8 shadow-xs">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No open jobs found</h3>
                        <p class="text-xs text-slate-500">Try loosening your search terms or clearing your location filter.</p>
                        <a href="{{ route('jobs.index') }}" class="inline-block text-xs font-semibold text-[#2563EB] hover:underline pt-2">
                            Reset Search →
                        </a>
                    </div>
                @endif

            </main>
        </div>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-slate-400 font-normal border-t border-slate-200/80 bg-white">
            &copy; {{ date('Y') }} {{ config('app.name', 'Skill Link NG') }}. All rights reserved.
        </footer>

        <!-- Application Modal Popup -->
        <div 
            x-show="applyModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="applyModalOpen = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.stop
                x-show="applyModalOpen"
                x-transition:enter="transition transform ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 border border-slate-200 space-y-5"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></div>
                        <span class="text-sm font-bold text-slate-900">Apply for Job Opportunity</span>
                    </div>
                    <button @click="applyModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Selected Job Summary -->
                <template x-if="selectedJob">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-1">
                        <h3 class="text-sm font-bold text-slate-900" x-text="selectedJob.title"></h3>
                        <div class="text-[11px] text-slate-500 font-medium flex items-center gap-2">
                            <span>Posted by: <strong class="text-slate-700" x-text="selectedJob.poster_name"></strong></span>
                            <span>•</span>
                            <span x-text="selectedJob.location"></span>
                        </div>
                    </div>
                </template>

                <!-- Request Form -->
                @auth
                    <form x-bind:action="'/opportunities/' + (selectedJob ? selectedJob.id : '') + '/apply'" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Application Pitch / Note to Client</label>
                            <textarea 
                                name="note"
                                rows="3" 
                                placeholder="Explain why you are the best fit for this job, your relevant experience, and availability..."
                                class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs text-slate-900 font-normal outline-none transition-all"
                            ></textarea>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-md transition-colors cursor-pointer"
                        >
                            Send Application →
                        </button>
                    </form>
                @else
                    <div class="space-y-3">
                        <p class="text-xs text-slate-600 font-normal">
                            Please log in or register to submit your application for this job.
                        </p>
                        <a 
                            href="{{ route('login') }}"
                            class="w-full inline-flex items-center justify-center bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-md transition-colors cursor-pointer"
                        >
                            Log In to Apply →
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        @livewireScripts
    </body>
</html>
