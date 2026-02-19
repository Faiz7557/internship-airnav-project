@props(['title', 'subtitle' => null, 'chartId', 'height' => '300px', 'icon' => null, 'iconBg' => 'bg-blue-50', 'iconColor' => 'text-blue-600', 'action' => null])

<div {{ $attributes->merge(['class' => 'glass-card p-6 rounded-[2rem] relative flex flex-col h-full hover:shadow-lg transition-all duration-300 border border-white/60']) }}>
    <div class="flex justify-between items-start mb-6 gap-4">
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
            <div>
                {{ $action }}
            </div>
        @endif
    </div>
    
    <!-- Chart Canvas Container -->
    <div class="flex-grow w-full relative" style="min-height: {{ $height }}; height: {{ $height }};">
        <canvas id="{{ $chartId }}" class="w-full" style="height: 100%;"></canvas>
        {{ $slot }}
    </div>
</div>
