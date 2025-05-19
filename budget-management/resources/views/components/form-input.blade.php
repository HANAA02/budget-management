@props([
    'type' => 'text',
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'horizontal' => false,
    'labelClass' => 'col-md-3',
    'fieldClass' => 'col-md-9',
    'prepend' => null,
    'append' => null
])

@php
    $id = $id ?? $name;
    $invalidClass = $errors->has($name) ? 'is-invalid' : '';
@endphp

<div class="form-group {{ $horizontal ? 'row mb-3' : '' }}">
    @if($label)
        <label for="{{ $id }}" class="{{ $horizontal ? $labelClass . ' col-form-label' : '' }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="{{ $horizontal ? $fieldClass : '' }}">
        @if($prepend || $append)
            <div class="input-group">
                @if($prepend)
                    <div class="input-group-prepend">
                        <span class="input-group-text">{!! $prepend !!}</span>
                    </div>
                @endif
                
                <input 
                    type="{{ $type }}" 
                    name="{{ $name }}" 
                    id="{{ $id }}" 
                    class="form-control {{ $invalidClass }}" 
                    placeholder="{{ $placeholder }}" 
                    value="{{ old($name, $value) }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $readonly ? 'readonly' : '' }}
                    {{ $attributes }}
                >
                
                @if($append)
                    <div class="input-group-append">
                        <span class="input-group-text">{!! $append !!}</span>
                    </div>
                @endif
                
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @else
            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $id }}" 
                class="form-control {{ $invalidClass }}" 
                placeholder="{{ $placeholder }}" 
                value="{{ old($name, $value) }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes }}
            >
            
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
        
        @if($hint)
            <small class="form-text text-muted">{{ $hint }}</small>
        @endif
    </div>
</div>
