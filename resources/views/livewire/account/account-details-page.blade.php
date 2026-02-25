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
        .account-card select.form-control { appearance: auto; cursor: pointer; }
        .account-card textarea.form-control { resize: vertical; }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .btn-outline-brand { border: 2px solid #d68910; border-radius: 10px; padding: 0.5rem 1.5rem; font-weight: 600; color: #d68910; background: transparent; transition: all 0.2s; }
        .btn-outline-brand:hover { background: #d68910; color: #fff; }
        .btn-outline-danger { border: 2px solid #dc3545; border-radius: 10px; padding: 0.5rem 1.5rem; font-weight: 600; color: #dc3545; background: transparent; transition: all 0.2s; }
        .btn-outline-danger:hover { background: #dc3545; color: #fff; }
        .error-text { color: #dc3545; font-size: 0.8rem; margin-top: 0.25rem; }
        .card-section-title { font-family: 'lemonMilk', serif; font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f0; }
        .address-card { border: 2px solid #e8e8e8; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; transition: border-color 0.2s; }
        .address-card.is-default { border-color: #d68910; background: rgba(214,137,16,0.03); }
        .address-badge { display: inline-block; background: #d68910; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .address-label-badge { display: inline-block; background: #e8e8e8; color: #555; font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 6px; }
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

                    {{-- Shipping Addresses --}}
                    <div class="account-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-section-title mb-0" style="border-bottom:none; padding-bottom:0;">Shipping Addresses</h5>
                            @if (!$showAddressForm)
                                <button type="button" class="btn btn-brand btn-sm" wire:click="openAddressForm">
                                    + Add Address
                                </button>
                            @endif
                        </div>

                        @if (session()->has('address_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('address_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('address_error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('address_error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Address Form (Add / Edit) --}}
                        @if ($showAddressForm)
                            <div class="address-card" style="border-color: #d68910; background: rgba(214,137,16,0.03);">
                                <h6 style="font-weight:700; color:#333; margin-bottom:1rem;">
                                    {{ $editing_address_id ? 'Edit Address' : 'Add New Address' }}
                                </h6>
                                <form wire:submit.prevent="saveAddress">
                                    <div class="row">
                                        <div class="col-sm-4 mb-3">
                                            <label class="form-label">Label <span style="font-size:0.75rem;color:#999;">(Optional)</span></label>
                                            <input type="text" class="form-control" wire:model="address_label" placeholder="Home, Office, etc.">
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control @error('address_first_name') border-danger @enderror" wire:model="address_first_name">
                                            @error('address_first_name') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control @error('address_last_name') border-danger @enderror" wire:model="address_last_name">
                                            @error('address_last_name') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 1</label>
                                        <input type="text" class="form-control @error('address_line_one') border-danger @enderror" wire:model="address_line_one" placeholder="Street address">
                                        @error('address_line_one') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 2 <span style="font-size:0.75rem;color:#999;">(Optional)</span></label>
                                        <input type="text" class="form-control" wire:model="address_line_two" placeholder="Apt, suite, unit, etc.">
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label">Country</label>
                                            <select class="form-control @error('address_country_id') border-danger @enderror" wire:model.live="address_country_id">
                                                <option value="">Select Country</option>
                                                @foreach ($this->countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('address_country_id') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control @error('address_city') border-danger @enderror" wire:model="address_city">
                                            @error('address_city') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 mb-3">
                                            <label class="form-label">State</label>
                                            @if ($this->states->count() > 0)
                                                <select class="form-control @error('address_state') border-danger @enderror" wire:model="address_state">
                                                    <option value="">Select State</option>
                                                    @foreach ($this->states as $state)
                                                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" class="form-control @error('address_state') border-danger @enderror" wire:model="address_state" placeholder="State / Province">
                                            @endif
                                            @error('address_state') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label class="form-label">ZIP Code</label>
                                            <input type="text" class="form-control @error('address_postcode') border-danger @enderror" wire:model="address_postcode">
                                            @error('address_postcode') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label">Phone <span style="font-size:0.75rem;color:#999;">(Optional)</span></label>
                                            <input type="tel" class="form-control" wire:model="address_phone" placeholder="Phone number">
                                        </div>
                                        <div class="col-sm-6 mb-3 d-flex align-items-end">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="address_shipping_default" wire:model="address_shipping_default">
                                                <label class="form-check-label" for="address_shipping_default" style="font-weight:600;">Set as default shipping address</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-brand" wire:loading.attr="disabled">
                                            <span wire:loading wire:target="saveAddress">Saving...</span>
                                            <span wire:loading.remove wire:target="saveAddress">{{ $editing_address_id ? 'Update Address' : 'Save Address' }}</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-brand" wire:click="cancelAddressForm">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        {{-- Address List --}}
                        @forelse ($addresses as $address)
                            <div class="address-card {{ $address->shipping_default ? 'is-default' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        @if ($address->shipping_default)
                                            <span class="address-badge mb-2">Primary Address</span>
                                        @endif
                                        @if ($address->label)
                                            <span class="address-label-badge mb-2">{{ $address->label }}</span>
                                        @endif
                                        <p class="mb-1 mt-2" style="font-weight:700; color:#333;">
                                            {{ $address->first_name }} {{ $address->last_name }}
                                        </p>
                                        <p class="mb-0" style="color:#666; line-height:1.6;">
                                            {{ $address->line_one }}
                                            @if ($address->line_two), {{ $address->line_two }}@endif
                                            <br>
                                            {{ $address->city }}, {{ $address->state }} {{ $address->postcode }}
                                            @if ($address->country)
                                                <br>{{ $address->country->name }}
                                            @endif
                                            @if ($address->contact_phone)
                                                <br>Phone: {{ $address->contact_phone }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="d-flex gap-2 flex-shrink-0">
                                        @if (!$address->shipping_default)
                                            <button type="button" class="btn btn-outline-brand btn-sm"
                                                onclick="AppDialog.confirm(this, 'setDefaultAddress', [{{ $address->id }}], {
                                                    title: 'Set Default Address',
                                                    text: 'Set this as your default shipping address?',
                                                    type: 'question',
                                                    confirmText: 'Yes, set default'
                                                })">
                                                Set Default
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-brand btn-sm" wire:click="editAddress({{ $address->id }})">Edit</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="AppDialog.confirm(this, 'deleteAddress', [{{ $address->id }}], {
                                                title: 'Delete Address',
                                                text: 'Are you sure you want to delete this address?',
                                                type: 'danger',
                                                confirmText: 'Yes, delete it'
                                            })">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            @if (!$showAddressForm)
                                <p style="color:#999; text-align:center; padding:2rem 0;">No addresses saved yet. Click "Add Address" to add one.</p>
                            @endif
                        @endforelse
                    </div>

                    {{-- Account Information --}}
                    <div class="account-card">
                        <h5 class="card-section-title">Account Information</h5>

                        @if (session()->has('account_info_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('account_info_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="saveAccountInfo">
                            <div class="mb-3">
                                <label for="referred_by" class="form-label">How Did You Hear About Us?</label>
                                <select class="form-control" id="referred_by" wire:model="referred_by">
                                    <option value="">Select...</option>
                                    <option value="Family or Friend">Family or Friend</option>
                                    <option value="Doctor or Clinic">Doctor or Clinic</option>
                                    <option value="Search Engine">Search Engine</option>
                                    <option value="Internet Article">Internet Article</option>
                                    <option value="Advertisement">Advertisement</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Natural News">Natural News</option>
                                    <option value="Book">Book</option>
                                    <option value="Email or Newsletter">Email or Newsletter</option>
                                    <option value="Church">Church</option>
                                    <option value="Unfiltered News">Unfiltered News</option>
                                    <option value="Event, Expo or Tradeshow">Event, Expo or Tradeshow</option>
                                    <option value="Other...">Other...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="b17_knowledge" class="form-label">What do you know about B17/Apricot Seeds?</label>
                                <textarea class="form-control" id="b17_knowledge" wire:model="b17_knowledge" rows="4"></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand" wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveAccountInfo">Saving...</span>
                                <span wire:loading.remove wire:target="saveAccountInfo">Save Changes</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
