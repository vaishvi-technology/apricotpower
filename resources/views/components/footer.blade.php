<footer class="main-footer">
    <div class="container">
        <div class="row main-footer-row">
            {{-- Left Column: Logo, About, Contact --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="footer-logo">
                    <img src="{{ asset('images/home/footer-logo.png') }}" alt="Apricot Power">
                </div>
                <div class="footer-about">
                    <p>Apricot Power has been providing apricot seeds since 1999.</p>
                </div>
                <div class="footer-information">
                    <div class="text-with-icon">
                        <img src="{{ asset('images/home/map-icon.png') }}" alt="Address" class="text-with-icon-img icon-img">
                        <div class="text-with-icon-text">
                            Address: 13501 Ranch Road 12,<br>Ste 103 Wimberley, Tx. 78676
                        </div>
                    </div>
                    <div class="text-with-icon">
                        <img src="{{ asset('images/home/phone-icon.png') }}" alt="Phone" class="text-with-icon-img icon-img">
                        <div class="text-with-icon-text">
                            Toll Free: <a href="tel:+18664687487">1-866-468-7487 (866-GOT-PITS)</a>
                        </div>
                    </div>
                    <div class="text-with-icon">
                        <img src="{{ asset('images/home/phone-icon.png') }}" alt="Phone" class="text-with-icon-img icon-img">
                        <div class="text-with-icon-text">
                            Local: <a href="tel:+17072621394">1-707-262-1394</a>
                        </div>
                    </div>
                    <div class="text-with-icon">
                        <img src="{{ asset('images/home/email-icon.png') }}" alt="Email" class="text-with-icon-img icon-img">
                        <div class="text-with-icon-text">
                            <a href="mailto:customerservice@apricotpower.com">customerservice@apricotpower.com</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Center Column: Product Line --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="footer-main-links">
                    <h3>APRICOT POWER'S PRODUCT LINE</h3>
                    <ul class="footer-main-links-list">
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Apricot Seeds</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">B17 - Apricot Seed Extract</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Immune Support</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Combo Packs</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">B17 Boosters</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Accessories</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Skin Care</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">AP Fuel Products</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Pet Products</a></li>
                        <li class="footer-main-links-item"><a href="{{ url('/store') }}">Big 3</a></li>
                    </ul>
                </div>
            </div>

            {{-- Right Column: Retailer CTA + Newsletter --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="subscribe-header-top" style="margin-bottom: 30px;">
                    JOIN THE MOVEMENT!<br>BECOME A RETAILER TODAY
                </div>
                <div class="footer-newsletter">
                    <h2>SUBSCRIBE TO OUR EMAIL LIST</h2>
                    <p>Sign up for exclusive offers, original stories, events and more.</p>
                    <form class="footer-newsletter-form">
                        <div class="d-flex gap-2 mb-2">
                            <input type="text" class="form-control" placeholder="First Name">
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                        <div class="mb-2">
                            <input type="email" class="form-control" placeholder="Email">
                        </div>
                        <button type="submit" class="button-with-icon">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Secondary Links --}}
        <div class="footer-secondary-links">
            <ul class="footer-secondary-links-list">
                <li class="footer-main-links-item"><a href="{{ url('/monthly-specials') }}">Monthly Specials</a></li>
                <li class="footer-main-links-item"><a href="{{ url('/contact') }}">Contact us</a></li>
                <li class="footer-main-links-item"><a href="{{ url('/apricot-seed-info') }}">Apricot Seed Info</a></li>
                <li class="footer-main-links-item"><a href="{{ url('/return-policy') }}">Return Policy</a></li>
                <li class="footer-main-links-item"><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                <li class="footer-main-links-item"><a href="{{ url('/shipping-policy') }}">Shipping Policy</a></li>
            </ul>
        </div>

        {{-- Disclaimer / Legal Text --}}
        <div class="footer-para-text-div">
            <p>Information and statements regarding dietary supplements have not been evaluated by the Food and Drug Administration and are not intended to diagnose, treat, cure or prevent any disease or health condition. Content on this website is for reference purposes only and is not intended to substitute for advice given by a physician, pharmacist or other licensed healthcare professional. You should not use this information as self-diagnosis or for treating a health problem or disease. Contact your health-care provider immediately if you suspect that you have a medical problem.</p>
            <p>Section 10786, Title 17, California Admin. Code: Warning apricot kernels may be toxic; very low quantities may cause reaction.</p>
            <p>WARNING: Consuming certain dietary supplements and/or other products offered for sale on this website may expose you to chemicals including lead, which is known to the State of California to cause cancer and birth defects or other reproductive harm. For more information go to www.P65Warnings.ca.gov/Food.</p>
            <p>The Apricot Power Brand works hard to ensure its strict quality standards are upheld when products reach consumers. As such, to ensure consumers receive the highest quality, authentic Apricot Power products when shopping on e-commerce platforms (including, but not limited to Amazon.com, eBay.com, and Walmart.com), Apricot Power will only honor its guarantee/warranty with valid proof of purchase from authorized and verified e-commerce sellers.</p>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="container">
        <div class="copyrightDiv">
            <div class="copyright-left">
                <img src="{{ asset('images/home/copyright-left-img.png') }}" alt="Verified &amp; Secured" style="max-width: 150px;">
            </div>
            <div class="copyright-center">
                <p>&copy; {{ now()->year }} APRICOT POWER, INC. ALL RIGHTS RESERVED.</p>
            </div>
            <div class="copyright-right">
                <span class="copyright-right-text">APRICOT POWER IN SOCIAL:</span>
                <div class="copyright-right-icons">
                    <a href="#"><img src="{{ asset('images/home/facebook-icon.png') }}" alt="Facebook"></a>
                    <a href="#"><img src="{{ asset('images/home/instagram-icon.png') }}" alt="Instagram"></a>
                </div>
            </div>
        </div>
    </div>
</footer>
