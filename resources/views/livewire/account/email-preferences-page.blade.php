<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">EMAIL</span> <span class="bold-font">PREFERENCES</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .pref-item { padding: 1rem 1.25rem; border: 2px solid #f0f0f0; border-radius: 10px; margin-bottom: 0.75rem; transition: border-color 0.2s; display: flex; align-items: flex-start; gap: 1rem; }
        .pref-item:hover { border-color: #e8d8b8; }
        .pref-item .pref-toggle { flex-shrink: 0; position: relative; width: 48px; height: 26px; margin-top: 2px; }
        .pref-item .pref-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
        .pref-item .pref-toggle .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; border-radius: 26px; transition: 0.3s; }
        .pref-item .pref-toggle .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: #fff; border-radius: 50%; transition: 0.3s; }
        .pref-item .pref-toggle input:checked + .slider { background-color: #d68910; }
        .pref-item .pref-toggle input:checked + .slider:before { transform: translateX(22px); }
        .pref-item .pref-toggle input:focus + .slider { box-shadow: 0 0 0 3px rgba(214,137,16,0.15); }
        .pref-item .pref-text label { font-weight: 600; color: #333; cursor: pointer; display: block; margin-bottom: 0; }
        .pref-item .pref-text small { color: #888; display: block; }
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
                        <h4 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:0.5rem;">Notification Settings</h4>
                        <p style="color:#888; font-size:0.92rem; margin-bottom:1.5rem;">Choose which emails you'd like to receive from us.</p>

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="pref-item">
                                <label class="pref-toggle">
                                    <input type="checkbox" wire:model="subscribe_to_list">
                                    <span class="slider"></span>
                                </label>
                                <div class="pref-text">
                                    <label for="subscribe_to_list">Mailing List</label>
                                    <small>Subscribe to our main mailing list for updates and deals</small>
                                </div>
                            </div>

                            <div class="pref-item">
                                <label class="pref-toggle">
                                    <input type="checkbox" wire:model="order_updates">
                                    <span class="slider"></span>
                                </label>
                                <div class="pref-text">
                                    <label for="order_updates">Order Updates</label>
                                    <small>Receive notifications about your orders and shipping</small>
                                </div>
                            </div>

                            <div class="pref-item">
                                <label class="pref-toggle">
                                    <input type="checkbox" wire:model="newsletter">
                                    <span class="slider"></span>
                                </label>
                                <div class="pref-text">
                                    <label for="newsletter">Newsletter</label>
                                    <small>Monthly newsletter with health tips and wellness articles</small>
                                </div>
                            </div>

                            <div class="pref-item">
                                <label class="pref-toggle">
                                    <input type="checkbox" wire:model="promotional_offers">
                                    <span class="slider"></span>
                                </label>
                                <div class="pref-text">
                                    <label for="promotional_offers">Promotions & Special Offers</label>
                                    <small>Exclusive deals, discount codes, and seasonal offers</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand mt-3" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save">Saving...</span>
                                <span wire:loading.remove wire:target="save">Save Preferences</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
