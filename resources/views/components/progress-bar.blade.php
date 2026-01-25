@props([
    'current' => 0,
    'total' => 100,
    'label' => '',
    'showPercentage' => true,
    'showLabel' => true,
    'height' => 'h-6',
    'animated' => false,
    'striped' => false,
])

@php
    $percentage = $total > 0 ? min(100, round(($current / $total) * 100, 1)) : 0;
    
    // Determine color class based on threshold
    $colorClass = match(true) {
        $percentage >= 90 => 'bg-danger',
        $percentage >= 70 => 'bg-warning',
        default => 'bg-success',
    };
    
    // Combine classes
    $progressBarClasses = $colorClass;
    if ($animated) {
        $progressBarClasses .= ' progress-bar-animated';
    }
    if ($striped) {
        $progressBarClasses .= ' progress-bar-striped';
    }
    
    // Format label
    $displayLabel = $label ?: "$current / $total";
@endphp

<div class="progress {{ $height }}" style="min-height: 20px;">
    <div class="progress-bar {{ $progressBarClasses }}" 
         role="progressbar" 
         style="width: {{ $percentage }}%"
         aria-valuenow="{{ $current }}" 
         aria-valuemin="0" 
         aria-valuemax="{{ $total }}">
        @if($showLabel || $showPercentage)
            <span class="d-flex align-items-center justify-content-center h-100 text-white fw-bold" style="font-size: 0.85rem;">
                @if($showLabel)
                    {{ $displayLabel }}
                @endif
                @if($showPercentage && $showLabel)
                    &nbsp;({{ $percentage }}%)
                @elseif($showPercentage)
                    {{ $percentage }}%
                @endif
            </span>
        @endif
    </div>
</div>
