@props(['title', 'value', 'subValue' => null, 'icon' => null, 'iconColor' => 'text-indigo-600', 'iconBg' => 'bg-indigo-50', 'growth' => null, 'growthText' => null])

<!-- Neo-Glass KPI Card -->
<div {{ $attributes->merge(['class' => 'glass-card rounded-[2rem] p-6 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg']) }}>
    <!-- Ambient Glow -->
    <div class="absolute -right-6 -top-6 w-24 h-24 {{ str_replace('bg-', 'bg-', $iconBg) }} rounded-full blur-3xl opacity-40 group-hover:opacity-60 transition-opacity"></div>
    
    <div class="relative z-10 flex flex-col h-full justify-between">
        <div>
            <!-- Header -->
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2.5 {{ $iconBg }} rounded-xl backdrop-blur-sm {{ $iconColor }} shadow-sm group-hover:scale-110 transition-transform duration-300">
                    @if($icon)
                        {!! $icon !!}
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    @endif
                </div>
                <h3 class="text-slate-500 text-xs font-bold uppercase tracking-widest font-outfit">{{ $title }}</h3>
            </div>
            
            <!-- Value -->
            <div class="text-4xl font-extrabold text-[#1F3C88] mt-1 tracking-tight drop-shadow-sm font-outfit leading-tight">
                {{ $value }}
            </div>
        </div>

        <!-- Footer / Growth -->
        @if($growth !== null || $subValue)
            <div class="mt-4 flex items-center gap-2">
                @if($growth !== null)
                    @php 
                        $isPositive = $growth >= 0; 
                        $color = $isPositive ? 'emerald' : 'rose';
                    @endphp
                    <span class="bg-{{ $color }}-50/80 backdrop-blur text-{{ $color }}-600 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm border border-{{ $color }}-100 flex items-center gap-1 group-hover:bg-{{ $color }}-100 transition-colors">
                        {{ $isPositive ? '▲' : '▼' }} {{ abs($growth) }}%
                    </span>
                    @if($growthText)
                        <span class="text-[10px] text-slate-400 font-medium">{{ $growthText }}</span>
                    @endif
                @elseif($subValue)
                     <span class="text-xs font-semibold text-slate-500 bg-slate-100/80 px-2 py-1 rounded-lg">
                        {{ $subValue }}
                     </span>
                @endif
            </div>
        @endif
        
        <!-- Slot for extra content (like progress bars) -->
        {{ $slot }}
    </div>
</div>
