<div>
    {{-- Inner Banner --}}
    <section class="inner-banner" @if($page && $page->banner_image) style="background-image: url('{{ Storage::url($page->banner_image) }}') !important; background-size: cover; background-position: center;" @endif>
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">CONTACT</span> <span class="bold-font">US</span></h1>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section class="contact-us-page">
        <div class="container">
            <div class="row">
                {{-- Contact Form --}}
                <div class="col-lg-7 col-md-12 mb-4">
                    <div class="contact-us-formDiv">
                        @if (session()->has('success'))
                            <div class="alert alert-success mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($submitted)
                            <div class="text-center py-5">
                                <img src="{{ asset('images/home/contact-feefo-image.png') }}" alt="Thank You" class="mb-4" style="max-width: 150px;">
                                <h3 style="font-family: 'lemonMilk', serif;">THANK YOU!</h3>
                                <p>Your message has been sent successfully. We'll get back to you soon.</p>
                                <button wire:click="$set('submitted', false)" class="button-with-icon mt-3">Send Another Message</button>
                            </div>
                        @else
                            <form wire:submit.prevent="submit" class="contact-us-form">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="contact-form-group">
                                            <label for="name" class="form-label">Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Your Name">
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="contact-form-group">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Your Email">
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="contact-form-group">
                                        <label for="subject" class="form-label">Subject *</label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" wire:model="subject" placeholder="Subject">
                                        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="contact-form-group">
                                        <label for="message" class="form-label">Message *</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" wire:model="message" rows="5" placeholder="Your Message"></textarea>
                                        @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="button-with-icon button-with-icon-lg" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submit">Send Message</span>
                                        <span wire:loading wire:target="submit">Sending...</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="col-lg-5 col-md-12">
                    <div class="contact-us-content h-100">
                        <h2>GET IN TOUCH</h2>

                        @if($page && $page->content)
                            <div class="page-content mb-4">
                                {!! $page->content !!}
                            </div>
                        @else
                            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                        @endif

                        <div class="contact-info mt-4">
                            <p><span>Address:</span><br>
                            13501 Ranch Road 12,<br>
                            Ste 103 Wimberley, Tx. 78676</p>
                        </div>

                        <div class="contact-info">
                            <p><span>Toll Free:</span><br>
                            <a href="tel:+18664687487">1-866-468-7487 (866-GOT-PITS)</a></p>
                        </div>

                        <div class="contact-info">
                            <p><span>Local / International:</span><br>
                            <a href="tel:+17072621394">1-707-262-1394</a></p>
                        </div>

                        <div class="contact-info">
                            <p><span>Email:</span><br>
                            <a href="mailto:customerservice@apricotpower.com">customerservice@apricotpower.com</a></p>
                        </div>

                        <div class="contact-info mt-4">
                            <p><span>Business Hours:</span><br>
                            Monday - Friday: 9:00 AM - 5:00 PM CST</p>
                        </div>

                        <div class="mt-4">
                            <img src="{{ asset('images/home/contact-feefo-image.png') }}" alt="Feefo Reviews" style="max-width: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
