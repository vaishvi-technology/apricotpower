<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">Email</span> <span class="bold-font">Signup</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
        .email-signup-form .form-control {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .email-signup-form .form-control:focus {
            border-color: #d68910;
            box-shadow: 0 0 0 3px rgba(214, 137, 16, 0.15);
        }
        .btn-subscribe {
            background: #28a745;
            border: none;
            border-radius: 4px;
            padding: 0.65rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-subscribe:hover {
            background: #218838;
            color: #fff;
        }
        .auth-link {
            color: #d68910;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-link:hover {
            color: #b5740d;
            text-decoration: underline;
        }
    </style>

    <section class="account-section">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3 mb-4">
                    <div class="account-sidebar">
                        <h6 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:1rem;">MY ACCOUNT</h6>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('basic-info') }}" wire:navigate>Basic Info</a>
                            <a class="nav-link" href="{{ route('account-details') }}" wire:navigate>Account Details</a>
                            <a class="nav-link" href="{{ route('order-history.view') }}" wire:navigate>Order History</a>
                            <a class="nav-link active" href="{{ route('email-preferences') }}" wire:navigate>Email Preferences</a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">Logout</button>
                            </form>
                        </nav>
                    </div>
                </div>

                {{-- Content --}}
                <div class="col-lg-9">
                    <div class="account-card">

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($is_subscribed)
                            {{-- Already Subscribed View --}}
                            <div class="text-center mb-4">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: #e8f5e9; border-radius: 50%; margin-bottom: 1rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#28a745" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </div>
                                <h5 style="font-weight: 700; color: #333; margin-bottom: 0.5rem;">You're Subscribed!</h5>
                                <p style="color: #555; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    You are currently subscribed to the Apricot Power deals list for exclusive discounts and health tips.
                                </p>
                                <p style="color: #555; font-size: 0.95rem;">
                                    Emails sent approximately once per week. (<a href="{{ route('privacy') }}" class="auth-link" target="_blank">read our privacy policy</a>)
                                </p>
                            </div>

                            <div class="text-center">
                                <button type="button" wire:click="unsubscribe" class="btn btn-outline-danger" wire:loading.attr="disabled" wire:confirm="Are you sure you want to unsubscribe from our email list?">
                                    <span wire:loading wire:target="unsubscribe">Unsubscribing...</span>
                                    <span wire:loading.remove wire:target="unsubscribe">Unsubscribe from Email List</span>
                                </button>
                            </div>
                        @else
                            {{-- Subscribe View --}}
                            <div class="text-center mb-4">
                                <p style="color: #333; font-size: 1rem; margin-bottom: 0.25rem;">
                                    <strong>Complete the form below to subscribe to the Apricot Power deals list for exclusive discounts and health tips!</strong>
                                </p>
                                <p style="color: #555; font-size: 0.95rem;">
                                    Emails sent approximately once per week, unsubscribe at any time (<a href="{{ route('privacy') }}" class="auth-link" target="_blank">read our privacy policy</a>)
                                </p>
                            </div>

                            <h5 class="text-center mb-3" style="font-weight: 700; color: #333;">Subscribe to our Email List</h5>

                            <form wire:submit.prevent="subscribe" class="email-signup-form">
                                <div class="row align-items-end g-2">
                                    <div class="col-md-3">
                                        <input type="text"
                                               class="form-control @error('first_name') border-danger @enderror"
                                               wire:model="first_name"
                                               placeholder="First Name">
                                        @error('first_name')
                                            <div class="text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text"
                                               class="form-control @error('last_name') border-danger @enderror"
                                               wire:model="last_name"
                                               placeholder="Last Name">
                                        @error('last_name')
                                            <div class="text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email"
                                               class="form-control @error('email') border-danger @enderror"
                                               wire:model="email"
                                               placeholder="Email">
                                        @error('email')
                                            <div class="text-danger" style="font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-subscribe w-100" wire:loading.attr="disabled">
                                            <span wire:loading wire:target="subscribe">Subscribing...</span>
                                            <span wire:loading.remove wire:target="subscribe">Subscribe</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
