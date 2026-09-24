<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard — ' . config('app.name', 'Skill Link NG') }}</title>

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
        {{ $head ?? '' }}
    </head>
    <body 
        class="bg-slate-100/70 font-sans antialiased text-slate-900 min-h-full selection:bg-slate-900 selection:text-white"
        x-data="{{ $xData ?? '{ pageLoading: true, sidebarOpen: false, sidebarCollapsed: localStorage.getItem(\'sidebar_collapsed\') === \'true\', toggleSidebar() { this.sidebarCollapsed = !this.sidebarCollapsed; localStorage.setItem(\'sidebar_collapsed\', this.sidebarCollapsed); }, profileModalOpen: false, notificationsOpen: false }' }}"
        x-init="{{ $xInit ?? 'setTimeout(() => pageLoading = false, 350)' }}"
    >

        <!-- Skeleton Preloader Overlay -->
        <div 
            x-show="pageLoading" 
            x-transition:leave="transition ease-out duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-98 pointer-events-none"
            class="fixed inset-0 z-50 bg-slate-100/90 backdrop-blur-md flex p-4 sm:p-6 gap-6 overflow-hidden"
        >
            <!-- Left Sidebar Skeleton -->
            <div class="hidden lg:flex shrink-0 bg-white border border-slate-200/80 rounded-2xl p-5 flex-col justify-between h-[calc(100vh-3rem)] space-y-6" :class="sidebarCollapsed ? 'w-20' : 'w-64 xl:w-72'">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 animate-pulse shrink-0"></div>
                        <div class="space-y-1.5 flex-1" x-show="!sidebarCollapsed">
                            <div class="h-4 w-28 bg-slate-200 rounded-md animate-pulse"></div>
                            <div class="h-3 w-20 bg-slate-100 rounded-md animate-pulse"></div>
                        </div>
                    </div>
                    <div class="h-10 w-full bg-slate-200 rounded-xl animate-pulse"></div>
                    <div class="space-y-2">
                        <div class="h-8 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="h-8 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="h-8 bg-slate-100 rounded-xl animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Main Content Skeleton -->
            <div class="flex-1 bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 overflow-hidden">
                <div class="h-14 bg-slate-100 rounded-xl animate-pulse"></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="h-32 bg-slate-100 rounded-2xl animate-pulse"></div>
                    <div class="h-32 bg-slate-100 rounded-2xl animate-pulse"></div>
                    <div class="h-32 bg-slate-100 rounded-2xl animate-pulse"></div>
                </div>
                <div class="h-64 bg-slate-100 rounded-2xl animate-pulse"></div>
            </div>
        </div>

        <!-- Main Dashboard Layout Container -->
        <div class="w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">
            <!-- Unified Sidebar -->
            <x-dashboard-sidebar :active="$active ?? null" />

            <!-- Main Content Viewport -->
            {{ $slot ?? '' }}
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
