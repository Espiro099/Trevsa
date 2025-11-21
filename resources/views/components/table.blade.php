@props([
    'headers' => null,
])

<div class="table-container animate-fade-in">
    <div class="table-responsive-scroll">
        <table {{ $attributes->class(['table-modern', 'responsive-table']) }}>
        @if(isset($headers) && $headers->isNotEmpty())
            <thead>
                <tr>
                    {{ $headers }}
                </tr>
            </thead>
        @endif
        
        <tbody>
            {{ $slot }}
        </tbody>
        </table>
    </div>
</div>
