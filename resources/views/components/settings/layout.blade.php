@props([
    'heading',
    'subheading',
])

<section>
    <header>
        <h2>{{ $heading }}</h2>
        <p>{{ $subheading }}</p>
    </header>

    <div>
        {{ $slot }}
    </div>
</section>
