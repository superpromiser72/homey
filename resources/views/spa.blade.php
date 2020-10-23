@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">
  <link rel="stylesheet" href="{{ mix('dist/css/all.css')}}">
  <script src="{{ asset('asset/js/jquery-3.5.1.min.js') }}"></script>
  <script type="text/javascript">
    let base_url = "{{ URL::to('/') }}"
    window.base_url = "{{ URL::to('/') }}"
    console.log(base_url)
  </script>

</head>
<body>
  <div id="app"></div>

  {{-- Global configuration object --}}
  <script>
    window.config = @json($config);
  </script>

  {{-- Load the application scripts --}}
  <script src="{{ mix('dist/js/app.js') }}"></script>
  {{-- <script src="{{ asset('asset/js/custom.js') }}"></script> --}}
  {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmoGr8OWrJb6AmKHeOrmS2U9z9gx7WBeQ&callback=myMap"></script> --}}
</body>
</html>