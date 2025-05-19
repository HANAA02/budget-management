@props([
    'type' => 'button',
    'color' => 'primary',
    'size' => '',
    'outline' => false,
    'block' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false
])

@php
    $btnClass = 'btn';
    $btnClass .= $outline ? ' btn-outline-' . $color : ' btn-' . $color;
    
    if ($size) {
        $btnClass .= ' btn-' . $size;
    }
    
    if ($block) {
        $btnClass .= ' btn-block';
    }
    
    if ($loading) {
        $disabled = true;
    }
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $btnClass]) }}
    {{ $disabled ? 'disabled' : '' }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
    @elseif($icon && $iconPosition === 'left')
        <i class="fa {{ $icon }} mr-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <i class="fa {{ $icon }} ml-2"></i>
    @endif
</button>