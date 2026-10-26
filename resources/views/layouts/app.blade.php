@props(['title' => config('app.name'), 'description' => ''])

<x-layout :title="$title" :description="$description">
    {{ $slot }}
</x-layout>
