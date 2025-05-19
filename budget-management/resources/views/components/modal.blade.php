@props([
    'id',
    'title' => null,
    'size' => '',
    'centered' => false,
    'scrollable' => false,
    'staticBackdrop' => false,
    'footerClass' => '',
    'headerClass' => '',
])

@php
    $modalClass = 'modal-dialog';
    
    if ($size) {
        $modalClass .= ' modal-' . $size;
    }
    
    if ($centered) {
        $modalClass .= ' modal-dialog-centered';
    }
    
    if ($scrollable) {
        $modalClass .= ' modal-dialog-scrollable';
    }
    
    $modalOptions = [];
    if ($staticBackdrop) {
        $modalOptions[] = 'data-backdrop="static"';
        $modalOptions[] = 'data-keyboard="false"';
    }
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true" {!! implode(' ', $modalOptions) !!}>
    <div class="{{ $modalClass }}" role="document">
        <div class="modal-content">
            @if($title || isset($header))
                <div class="modal-header {{ $headerClass }}">
                    @if(isset($header))
                        {{ $header }}
                    @else
                        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    @endif
                    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
                <div class="modal-footer {{ $footerClass }}">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
