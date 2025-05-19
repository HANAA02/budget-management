@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => 'bg-white',
    'bodyClass' => '',
    'footerClass' => 'bg-white',
    'withHeader' => true,
    'withFooter' => true,
    'noPadding' => false
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($withHeader && ($title || isset($header)))
        <div class="card-header {{ $headerClass }}">
            @if(isset($header))
                {{ $header }}
            @else
                <h5 class="card-title mb-0">{{ $title }}</h5>
                @if($subtitle)
                    <h6 class="card-subtitle text-muted">{{ $subtitle }}</h6>
                @endif
            @endif
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass }} {{ $noPadding ? 'p-0' : '' }}">
        {{ $slot }}
    </div>
    
    @if($withFooter && isset($footer))
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
