@props(['title', 'subtitle' => null, 'chartId', 'height' => '300px', 'icon' => null, 'iconBg' => 'bg-blue-50', 'iconColor' => 'text-blue-600', 'action' => null])

<div {{ $attributes->merge(['class' => 'glass-card p-6 rounded-[2rem] relative flex flex-col hover:shadow-lg transition-all duration-300 border border-white/60']) }}>
    <div class="flex justify-between items-start mb-4 gap-4">
        <!-- Title & Icon -->
        <div class="flex items-center gap-3">
             @if($icon)
                <div class="p-2 {{ $iconBg }} rounded-lg {{ $iconColor }}">
                    {!! $icon !!}
                </div>
            @endif
            <div>
                <h3 class="font-bold text-[#1F3C88] text-sm uppercase tracking-wider font-outfit">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <!-- Optional Action (Legend, Toggle, Badge) -->
        @if($action)
            <div class="shrink-0">
                {{ $action }}
            </div>
        @endif
    </div>
    
    <!-- Chart Canvas: fixed height wrapper so Chart.js can measure it -->
    <div class="relative w-full" style="height: {{ $height }};">
        <canvas id="{{ $chartId }}" class="absolute inset-0 w-full h-full"></canvas>
    </div>

    <!-- Slot: additional content (legends, etc.) rendered freely below the chart -->
    {{ $slot }}
</div>
