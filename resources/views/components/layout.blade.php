@props([
    'title',
    'description'
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> {{ $title }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $description }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    @include('layouts.navigation')

    <main>
        {{ $slot }}
    </main>
</body>
</html>