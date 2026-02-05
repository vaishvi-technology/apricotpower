<div class="category-page py-5">
    <div class="container">
        @if($this->category)
            <h1 class="mb-4">{{ $this->category->translateAttribute('name') }}</h1>

            <div class="row">
                @forelse($this->products as $product)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h4>No products in this category</h4>
                        <p class="text-muted">Check back soon for new products.</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $this->products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h4>Category not found</h4>
                <a href="{{ route('store') }}" class="btn btn-warning">Back to Store</a>
            </div>
        @endif
    </div>
</div>
