<!doctype html>
<html lang="pt-BR" class="h-100">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrftoken" content="{{ csrf_token() }}">
<title>Projeto teste Carefy</title>
<link rel="icon" type="image/png" href="{{ asset('files/img/favicon.png') }}"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('files/css/base.css') }}">
</head>
<body class="d-flex flex-column h-100">
@include(config('base.admin.private') . 'partials.header')
@yield('content')
@include(config('base.admin.private') . 'partials.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('files/js/base.js') }}"></script>
@stack('scripts')
</body>
</html>