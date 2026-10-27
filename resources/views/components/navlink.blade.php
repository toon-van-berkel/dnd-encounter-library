@props(['active' => false])

<!-- attributes gives the link the href classes ect -->
    <a {{ $attributes->class([
        'nav__list-item',
        'nav__list-item--active' => $active,
    ]) }}>
        {{ $slot }}
    </a>
