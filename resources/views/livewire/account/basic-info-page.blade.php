<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">BASIC</span> <span class="bold-font">INFO</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
        .account-card .form-label { font-size: 0.85rem; font-weight: 600; color: #444; }
        .account-card .form-control { border: 2px solid #e8e8e8; border-radius: 10px; padding: 0.6rem 1rem; transition: border-color 0.3s, box-shadow 0.3s; }
        .account-card .form-control:focus { border-color: #d68910; box-shadow: 0 0 0 3px rgba(214,137,16,0.15); }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .error-text { color: #dc3545; font-size: 0.8rem; margin-top: 0.25rem; }
    </style>

    <section class="account-section">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3 mb-4">
                    <div class="account-sidebar">
                        <h6 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:1rem;">MY ACCOUNT</h6>
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="{{ route('basic-info') }}" wire:navigate>Basic Info</a>
                            <a class="nav-link" href="{{ route('account-details') }}" wire:navigate>Account Details</a>
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
                    <div class="account-card">
                        <h4 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:1.5rem;">Personal Information</h4>

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') border-danger @enderror" id="first_name" wire:model="first_name">
                                    @error('first_name') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') border-danger @enderror" id="last_name" wire:model="last_name">
                                    @error('last_name') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') border-danger @enderror" id="email" wire:model="email">
                                @error('email') <div class="error-text">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control @error('phone') border-danger @enderror" id="phone" wire:model="phone" placeholder="(555) 123-4567">
                                    @error('phone') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="company_name" class="form-label">Company <span style="font-size:0.75rem;color:#999;">(Optional)</span></label>
                                    <input type="text" class="form-control" id="company_name" wire:model="company_name">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand mt-2" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save">Saving...</span>
                                <span wire:loading.remove wire:target="save">Save Changes</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
