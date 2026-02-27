<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">Common</span> <span class="bold-font">Questions</span></h1>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="faq-sec">
        <div class="container">
            <p class="faq-lead">Browse our most frequently asked questions below. Can't find what you're looking for? <a href="{{ route('contact') }}">Contact us</a> and we'll be happy to help.</p>

            @php $renderedCount = 0; @endphp
            @foreach($faqCategories as $category)
                @if($category->faqs->count())
                    {{-- Section Title --}}
                    <h3 class="faq-section-title">{{ $category->name }}</h3>

                    @php
                        $faqs = $category->faqs;
                        $half = ceil($faqs->count() / 2);
                        $col1Faqs = $faqs->slice(0, $half);
                        $col2Faqs = $faqs->slice($half);
                    @endphp

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="accordion faq-accordians faq-accordians-col-1" id="faqCat{{ $category->id }}Col1">
                                @foreach($col1Faqs as $index => $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                                <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span> {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqCat{{ $category->id }}Col1">
                                            <div class="accordion-body">
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($col2Faqs->count())
                            <div class="col-lg-6">
                                <div class="accordion faq-accordians faq-accordians-col-2" id="faqCat{{ $category->id }}Col2">
                                    @foreach($col2Faqs as $index => $faq)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                                    <span>{{ str_pad($half + $index + 1, 2, '0', STR_PAD_LEFT) }}</span> {{ $faq->question }}
                                                </button>
                                            </h2>
                                            <div id="faq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqCat{{ $category->id }}Col2">
                                                <div class="accordion-body">
                                                    {!! $faq->answer !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Static Browse Products after first category --}}
                    @if($renderedCount === 0)
                        <div class="faq-browse-products my-5">
                            <div class="row justify-content-center">
                                <div class="col-md-5 col-sm-6 mb-4">
                                    <div class="faq-product-card text-center">
                                        <div class="faq-product-img">
                                            <img src="{{ asset('images/home/b-17.png') }}" alt="Vitamin B17 Amygdalin" class="img-fluid">
                                        </div>
                                        <a href="{{ route('store') }}" class="btn faq-browse-btn mt-3">Browse Vitamin B17</a>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-6 mb-4">
                                    <div class="faq-product-card text-center">
                                        <div class="faq-product-img">
                                            <img src="{{ asset('images/home/apricot-seeds.png') }}" alt="Apricot Seeds" class="img-fluid">
                                        </div>
                                        <a href="{{ route('store') }}" class="btn faq-browse-btn mt-3">Browse Apricot Seeds</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php $renderedCount++; @endphp
                @endif
            @endforeach

            @if($faqCategories->isEmpty() || $faqCategories->sum(fn($c) => $c->faqs->count()) === 0)
                <div class="text-center py-5">
                    <p style="font-family: 'Jost', sans-serif; font-size: 18px; color: #888;">No FAQs available at the moment. Please check back later.</p>
                </div>
            @endif
        </div>
    </section>
</div>
