@props(['product'])

@php
    $thumbnail = $product->getFirstMedia('images');
@endphp

<a class="block group"
   href="{{ route('product.view', $product->defaultUrl->slug) }}"
   wire:navigate
>
    <div class="overflow-hidden rounded-lg aspect-w-1 aspect-h-1 bg-gray-100">
        @if ($thumbnail)
            <img class="object-cover transition-transform duration-300 group-hover:scale-105"
                 src="{{ $thumbnail->getUrl('medium') }}"
                 alt="{{ $product->translateAttribute('name') }}" />
        @else
            <div class="flex items-center justify-center h-full text-gray-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    <strong class="mt-2 text-sm font-medium">
        {{ $product->translateAttribute('name') }}
    </strong>

    <p class="mt-1 text-sm text-gray-600">
        <span class="sr-only">
            Price
        </span>

        <x-product-price :product="$product" />
    </p>
</a>
