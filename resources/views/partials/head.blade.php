<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {!! filled($title ?? null) ? e($title.' - '.config('app.name', 'Laravel')) : e(config('app.name', 'Laravel')) !!}
</title>

@include('partials.favicon')

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
