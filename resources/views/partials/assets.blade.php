@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);
    $entry = $manifest['resources/js/app.ts'] ?? null;
@endphp
@if($entry)
    @if(!empty($entry['css']))
        @foreach($entry['css'] as $css)
            <link rel="stylesheet" href="{{ asset('build/'.$css) }}" />
        @endforeach
    @endif
    <script type="module" src="{{ asset('build/'.$entry['file']) }}" defer></script>
@endif