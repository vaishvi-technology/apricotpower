<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">ACCOUNT</span> <span class="bold-font">DETAILS</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; margin-bottom: 1.5rem; }
        .account-card .form-label { font-size: 0.85rem; font-weight: 600; color: #444; }
        .account-card .form-control { border: 2px solid #e8e8e8; border-radius: 10px; padding: 0.6rem 1rem; transition: border-color 0.3s, box-shadow 0.3s; }
        .account-card .form-control:focus { border-color: #d68910; box-shadow: 0 0 0 3px rgba(214,137,16,0.15); }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .error-text { color: #dc3545; font-size: 0.8rem; margin-top: 0.25rem; }
        .card-section-title { font-family: 'lemonMilk', serif; font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f0; }
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
                            <a class="nav-link active" href="{{ route('account-details') }}" wire:navigate>Account Details</a>
                            <a class="nav-link" href="{{ route('order-history.view') }}" wire:navigate>Order History</a>
                            <a class="nav-link" href="{{ route('email-preferences') }}" wire:navigate>Email Preferences</a>
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
                    {{-- Change Password --}}
                    <div class="account-card">
                        <h5 class="card-section-title">Change Password</h5>

                        @if (session()->has('password_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('password_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="updatePassword">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') border-danger @enderror" id="current_password" wire:model="current_password">
                                @error('current_password') <div class="error-text">{{ $message }}</div> @enderror
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') border-danger @enderror" id="password" wire:model="password">
                                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-brand" wire:loading.attr="disabled">
                                <span wire:loading wire:target="updatePassword">Updating...</span>
                                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                            </button>
                        </form>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="account-card">
                        <h5 class="card-section-title">Default Shipping Address</h5>

                        @if (session()->has('address_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('address_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="saveAddress">
                            <div class="mb-3">
                                <label for="shipping_line_one" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control @error('shipping_line_one') border-danger @enderror" id="shipping_line_one" wire:model="shipping_line_one" placeholder="Street address">
                                @error('shipping_line_one') <div class="error-text">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="shipping_line_two" class="form-label">Address Line 2 <span style="font-size:0.75rem;color:#999;">(Optional)</span></label>
                                <input type="text" class="form-control" id="shipping_line_two" wire:model="shipping_line_two" placeholder="Apt, suite, unit, etc.">
                            </div>
                            <div class="row">
                                <div class="col-sm-5 mb-3">
                                    <label for="shipping_city" class="form-label">City</label>
                                    <input type="text" class="form-control @error('shipping_city') border-danger @enderror" id="shipping_city" wire:model="shipping_city">
                                    @error('shipping_city') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label for="shipping_state" class="form-label">State</label>
                                    <input type="text" class="form-control @error('shipping_state') border-danger @enderror" id="shipping_state" wire:model="shipping_state">
                                    @error('shipping_state') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <label for="shipping_postcode" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control @error('shipping_postcode') border-danger @enderror" id="shipping_postcode" wire:model="shipping_postcode">
                                    @error('shipping_postcode') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-brand" wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveAddress">Saving...</span>
                                <span wire:loading.remove wire:target="saveAddress">Save Address</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
