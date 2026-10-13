@props(['active' => false])

<!-- attributes gives the link the href classes ect -->
<a 
    {{ $attributes }}
    class="nav-link {{ $active ? 'nav-link--active' : '' }}"
>
    {{ $slot }}
</a>