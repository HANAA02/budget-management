@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'label' => null,
    'placeholder' => 'Sélectionnez une option',
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'horizontal' => false,
    'labelClass' => 'col-md-3',
    'fieldClass' => 'col-md-9',
    'multiple' => false,
    'optionValueField' => 'id',
    'optionTextField' => 'name'
])

@php
    $id = $id ?? $name;
    $invalidClass = $errors->has($name) ? 'is-invalid' : '';
    $selectedValue = old($name, $selected);
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
        <select 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
            id="{{ $id }}" 
            class="form-control {{ $invalidClass }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $multiple ? 'multiple' : '' }}
            {{ $attributes }}
        >
            @if($placeholder && !$multiple)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $option)
                @php
                    $optionValue = is_array($option) || is_object($option) ? $option[$optionValueField] : $option;
                    $optionText = is_array($option) || is_object($option) ? $option[$optionTextField] : $option;
                    
                    $isSelected = $multiple 
                        ? (is_array($selectedValue) && in_array($optionValue, $selectedValue)) 
                        : $selectedValue == $optionValue;
                @endphp
                
                <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
                    {{ $optionText }}
                </option>
            @endforeach
        </select>
        
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        
        @if($hint)
            <small class="form-text text-muted">{{ $hint }}</small>
        @endif
    </div>
</div>