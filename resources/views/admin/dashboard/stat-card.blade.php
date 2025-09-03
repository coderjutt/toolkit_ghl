@props(['type', 'current', 'goal', 'progress', 'isCount' => false])

@php
    $formattedCurrent = $isCount ? $current : '$' . formatMonetaryValue($current);
    $formattedGoal = $isCount ? $goal : '$' . formatMonetaryValue($goal);
    $progress = min($progress, 100);
@endphp

@if($type === 'total-sale')
    <div class="stat-card bg-white p-5 shadow-md rounded-2xl flex flex-col space-y-2 h-[130px]">
        <div class="flex items-center">
            <div class="stat-icon w-14 h-14 bg-gold rounded-xl flex items-center justify-center mr-4">
                <i class="fa-solid fa-dollar-sign text-white text-2xl"></i>
            </div>
            <div class="stat-content">
                <h3 class="text-gray-500 text-sm font-medium">TOTAL SALE</h3>
                <h2 class="text-navy text-2xl font-bold my-1">
                    <span class="text-green-600">{{ $formattedCurrent }}</span>
                    <span class="text-gray-400 font-medium text-base">/ {{ $formattedGoal }}</span>
                </h2>
            </div>
        </div>
        <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
            <div class="bg-gold h-full text-xs font-semibold text-navy text-center leading-4"
                style="width: {{ $progress }}%">
                {{ number_format($progress, 2) }}%
            </div>
        </div>
    </div>
@elseif($type === 'won-opportunities')
    <div class="stat-card bg-white p-5 shadow-md rounded-2xl flex flex-col space-y-2 h-[130px]">
        <div class="flex items-center">
            <div class="stat-icon w-14 h-14 bg-navy rounded-xl flex items-center justify-center mr-4">
                <i class="fa-solid fa-lightbulb text-white text-2xl"></i>
            </div>
            <div class="stat-content">
                <h3 class="text-gray-500 text-sm font-medium">TOTAL WON OPPORTUNITIES</h3>
                <h2 class="text-navy text-2xl font-bold my-1">{{ $formattedCurrent }}</h2>
            </div>
        </div>
        <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
            <div class="bg-navy h-full text-xs font-semibold text-white text-center"
                style="width: {{ $progress }}%">
                {{ number_format($progress, 2) }}%
            </div>
        </div>
    </div>
@endif
