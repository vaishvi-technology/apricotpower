<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>{{ $title ?? 'Apricot Power - Quality Apricot Seeds and B17 Products' }}</title>
    <meta
        name="description"
        content="Apricot Power has been providing apricot seeds since 1999. Your reliable source for quality apricot seeds and B17 products."
    >
    <link
        href="{{ asset('css/app.css') }}"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">

    <link
        rel="icon"
        href="{{ asset('favicon.svg') }}"
    >
    <!-- Google Translate -->
    <script type="text/javascript">
        var _googleTranslateInitialized = false;

        function googleTranslateElementInit() {
            var el = document.getElementById('google_translate_element');
            if (!el || _googleTranslateInitialized) return;
            _googleTranslateInitialized = true;
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                autoDisplay: false,
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    @livewireStyles
    @stack('styles')
    <style>
        /* Google Translate Widget Styling */
        #google_translate_element {
            display: inline-block;
            vertical-align: middle;
        }
        #google_translate_element .goog-te-gadget {
            font-size: 0;
            color: transparent;
            display: inline-flex !important;
            align-items: center;
            vertical-align: middle;
        }
        #google_translate_element .goog-te-gadget > span {
            display: inline-flex !important;
            align-items: center;
        }
        #google_translate_element .goog-te-gadget .goog-te-combo {
            font-size: 13px;
            background-color: #d68910;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 3px;
            padding: 2px 5px;
            cursor: pointer;
            outline: none;
            vertical-align: middle;
        }
        #google_translate_element .goog-te-gadget .goog-te-combo option {
            background-color: #fff;
            color: #333;
        }
        #google_translate_element .goog-te-gadget img {
            vertical-align: middle;
            margin: 0 4px 0 0;
        }
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }
        body {
            top: 0px !important;
        }
        /* Top Header Styles */
        .topheader {
            background-color: #d68910;
            font-size: 14px;
        }
        .topheader .contact-info strong {
            margin-right: 5px;
        }
        .topheader .language-selector {
            cursor: pointer;
        }
        .topheader .translate-label {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
        }

        /* Dropdown Styles */
        .main-header .dropdown-menu {
            background-color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 0;
            padding: 0;
            min-width: 200px;
        }
        .main-header .dropdown-item {
            padding: 10px 20px;
            color: #333;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .main-header .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #d68910;
        }
        .main-header .nav-link.dropdown-toggle::after {
            margin-left: 5px;
        }

        /* User Section */
        .user-section2 .header-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 5px;
        }
        .user-section2 .icon-text {
            font-size: 12px;
            font-weight: 600;
        }
        .user-section2 .login-icon,
        .user-section2 img {
            width: 20px;
            height: 20px;
        }

        /* Icon Wrapper & Cart Badge */
        .icon-wrapper {
            position: relative;
            display: inline-block;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #d68910;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile Cart Link */
        .cart-link-mobile {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            margin-right: 10px;
        }
        .cart-link-mobile .icon-text {
            font-size: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 575px) {
            .topheader .contact-info {
                font-size: 12px;
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>

<body class="antialiased text-gray-900 home-page">
    <x-impersonation-banner />

    @livewire('components.navigation')

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <x-app-dialog />
    @stack('scripts')
</body>

</html>
