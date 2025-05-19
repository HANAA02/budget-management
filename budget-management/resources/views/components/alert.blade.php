@props(['type' => 'info', 'dismissible' => false])

@php
    $alertClass = [
        'info' => 'alert-info',
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        'primary' => 'alert-primary',
        'secondary' => 'alert-secondary',
        'light' => 'alert-light',
        'dark' => 'alert-dark',
    ][$type];

    $iconClass = [
        'info' => 'fa-info-circle',
        'success' => 'fa-check-circle',
        'warning' => 'fa-exclamation-triangle',
        'danger' => 'fa-exclamation-circle',
        'primary' => 'fa-bell',
        'secondary' => 'fa-bell',
        'light' => 'fa-bell',
        'dark' => 'fa-bell',
    ][$type];
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . $alertClass]) }} role="alert">
    <i class="fa {{ $iconClass }} mr-2"></i>
    
    <span>{{ $slot }}</span>
    
    @if($dismissible)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>