<!-- Info section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($sections as $section)
        <div>
            <h3 class="font-medium text-gray-900 mb-2">{{ $section['title'] }}</h3>
            @if(isset($section['content']))
                {!! $section['content'] !!}
            @elseif(isset($section['items']))
                <ul class="text-sm text-gray-600 space-y-1">
                    @foreach($section['items'] as $item)
                    <li>{!! $item !!}</li>
                    @endforeach
                </ul>
            @elseif(isset($section['text']))
                <p class="text-sm text-gray-600">{{ $section['text'] }}</p>
            @endif
        </div>
        @endforeach
    </div>
</div>

