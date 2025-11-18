<!-- Section Card -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
    @if(isset($description))
    <p class="text-gray-600 mb-4">{{ $description }}</p>
    @endif
    <div class="{{ $containerClass ?? 'space-y-4' }}">
        {!! $content ?? '' !!}
    </div>
</div>

