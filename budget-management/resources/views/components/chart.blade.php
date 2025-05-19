@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'bar',
    'height' => '300px',
    'data' => null,
    'options' => null
])

<div {{ $attributes }}>
    <canvas id="{{ $id }}" style="height: {{ $height }}"></canvas>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($data);
        const options = @json($options);
        const ctx = document.getElementById('{{ $id }}').getContext('2d');
        
        new Chart(ctx, {
            type: '{{ $type }}',
            data: data,
            options: options || {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush
