<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AirNav</title>
    <script src="{{ asset('js/libs/tailwindcss.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Poppins', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        /* Glassmorphism */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .glass-nav {
            background: rgba(31, 60, 136, 0.9); /* #1F3C88 with opacity */
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* Ambient Light Animation */
        .ambient-light {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: -1;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: 0.5;
            animation: float 20s infinite ease-in-out;
        }
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(20px, 40px) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        
        /* Form & Table Styling */
        .form-input-glass {
            @apply bg-white/50 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-lg transition-all duration-300;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 relative min-h-screen overflow-x-hidden">
    
    <!-- Ambient Background -->
    <div class="ambient-light overflow-hidden">
        <div class="orb bg-blue-500/20 w-[800px] h-[800px] top-[-300px] left-[-200px] animate-float-slow"></div>
        <div class="orb bg-indigo-500/20 w-[600px] h-[600px] bottom-[-100px] right-[-100px] animate-float-delayed"></div>
        <div class="orb bg-purple-500/10 w-[400px] h-[400px] top-[40%] left-[30%] animate-pulse-slow"></div>
    </div>
    
    <style>
        @keyframes float-slow {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, 50px) rotate(10deg); }
        }
        @keyframes float-delayed {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, -50px) rotate(-10deg); }
        }
        .animate-float-slow { animation: float-slow 20s infinite ease-in-out; }
        .animate-float-delayed { animation: float-delayed 25s infinite ease-in-out reverse; }
        .animate-pulse-slow { animation: pulse 10s infinite ease-in-out; }
    </style>

    <div class="min-h-screen flex flex-col">
        <!-- Top Navigation -->
        <nav class="glass-nav text-white px-6 py-4 sticky top-0 z-50 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo_airnav.png') }}" alt="Logo AirNav" class="h-9 brightness-0 invert opacity-90 hover:opacity-100 transition">
                <div class="h-6 w-px bg-white/20 hidden md:block"></div>
                <span class="font-outfit font-bold text-lg tracking-wide hidden md:inline text-blue-50">Admin Panel</span>
            </div>
            <div class="flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="text-blue-100 hover:text-white transition flex items-center gap-2 group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Ke Dashboard Utama
                </a>
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-white/10 rounded-full hover:bg-white/20 transition backdrop-blur-sm border border-white/10 shadow-sm">
                    Event Manager
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-6 py-10 relative z-10">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-6 text-center text-xs text-slate-400">
            <div class="flex justify-center items-center gap-2 mb-2">
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>Administrator Mode</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            </div>
            &copy; {{ date('Y') }} AirNav Indonesia. All rights reserved.
        </footer>
    </div>
</body>
</html>
