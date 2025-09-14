<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title','IETI App')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

  {{-- navbar partial --}}
  @include('partials.navbar')

  <div class="container-fluid">
    <div class="row">
      {{-- sidebar partial --}}
      @include('partials.sidebar')

      {{-- main content from pages --}}
      <main class="col" role="main" tabindex="-1">
        @yield('content')
      </main>
    </div>
  </div>

  {{-- footer partial --}}
  @include('partials.footer')

</body>
</html>
