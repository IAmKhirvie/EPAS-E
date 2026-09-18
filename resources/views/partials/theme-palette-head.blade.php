<link rel="stylesheet" href="{{ dynamic_asset('css/theme-palette.css') }}">
@php
    $hasaAppearance = [
        'primary_color' => config('theme.primary'),
        'secondary_color' => config('theme.secondary'),
        'background_color' => config('theme.background'),
        'dark_background_color' => config('theme.dark_background'),
    ];
    if (auth()->check()) {
        try {
            $storedAppearance = \App\Models\Setting::where('user_id', auth()->id())->where('key', 'appearance')->value('value');
            $hasaAppearance = array_merge($hasaAppearance, json_decode($storedAppearance ?: '{}', true) ?: []);
        } catch (\Throwable $exception) {
            // Use the default palette if settings are unavailable.
        }
    }
    $backgroundPath = $hasaAppearance['background_image'] ?? null;
    $hasaAppearance['background_image_url'] = is_string($backgroundPath) && preg_match('/^theme-backgrounds\/[A-Za-z0-9._-]+$/', $backgroundPath)
        ? route('settings.background-image')
        : null;
@endphp
<script>window.hasaAppearance = @json($hasaAppearance);</script>
<script src="{{ dynamic_asset('js/theme-palette.js') }}"></script>
