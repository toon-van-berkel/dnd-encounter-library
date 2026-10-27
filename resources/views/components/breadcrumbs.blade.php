@props(['items' => []])

@if (count($items) > 0)
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ol class="breadcrumbs__list">
            @foreach ($items as $item)
                <li class="breadcrumbs__item">
                    @unless ($loop->first)
                        <span class="breadcrumbs__separator" aria-hidden="true">
                            /
                        </span>
                    @endunless

                    @if ($item['url'] ?? null)
                        <a
                            class="breadcrumbs__link"
                            href="{{ $item['url'] }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="breadcrumbs__current" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif