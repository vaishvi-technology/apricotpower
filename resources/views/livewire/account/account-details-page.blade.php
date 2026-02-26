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
        .account-card select.form-select { cursor: pointer; }
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
        .form-switch { padding-left: 3.5em; }
        .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; margin-left: -3.5em; background-size: auto; border: 2px solid #ccc; }
        .form-switch .form-check-input:checked { background-color: #d68910; border-color: #d68910; }
        .form-switch .form-check-input:focus { box-shadow: 0 0 0 3px rgba(214,137,16,0.15); border-color: #d68910; }
        .read-only-field { background: #f5f5f5; border: 2px solid #e8e8e8; border-radius: 10px; padding: 0.6rem 1rem; color: #666; }
        .admin-section-divider { border-top: 3px solid #d68910; margin-top: 1.5rem; padding-top: 0.5rem; }
        .admin-section-badge { display: inline-block; background: #d68910; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; }
        .retailer-map-section { background: #fafafa; border: 2px solid #e8e8e8; border-radius: 10px; padding: 1.5rem; margin-top: 1rem; }
    </style>

    <section class="account-section">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3 mb-4">
                    @include('partials.account-sidebar')
                </div>

                {{-- Content --}}
                <div class="col-lg-9">
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
                                            <select class="form-select @error('address_country_id') border-danger @enderror" wire:model.live="address_country_id">
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
                                                <select class="form-select @error('address_state') border-danger @enderror" wire:model="address_state">
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
                                <select class="form-select" id="referred_by" wire:model="referred_by">
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

                    {{-- ═══ ADMIN OPTIONS (only visible during impersonation) ═══ --}}
                    @if ($isImpersonating)

                        {{-- Account Settings --}}
                        <div class="account-card">
                            <div class="admin-section-divider">
                                <span class="admin-section-badge">Admin Options</span>
                            </div>
                            <h5 class="card-section-title">Account Details</h5>

                            @if (session()->has('admin_settings_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('admin_settings_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form wire:submit.prevent="saveAccountSettings">
                                <div class="mb-3">
                                    <label for="customer_group_id" class="form-label">Account Group</label>
                                    <select class="form-select" id="customer_group_id" wire:model.live="customer_group_id">
                                        <option value="">No Group</option>
                                        @foreach ($this->customerGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Determines product pricing and payment terms.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="sales_rep_id" class="form-label">Sales Rep.</label>
                                    <select class="form-select" id="sales_rep_id" wire:model="sales_rep_id">
                                        <option value="">None Assigned</option>
                                        @foreach ($this->staffMembers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_tax_exempt" wire:model="is_tax_exempt">
                                        <label class="form-check-label fw-bold" for="is_tax_exempt">Tax Exempt</label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Last Login</label>
                                        <div class="read-only-field">{{ $last_login_at ?? 'Never' }}</div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Last Order</label>
                                        <div class="read-only-field">{{ $last_order_at ?? 'Never' }}</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-brand" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="saveAccountSettings">Saving...</span>
                                    <span wire:loading.remove wire:target="saveAccountSettings">Save Settings</span>
                                </button>
                            </form>
                        </div>

                        {{-- Wholesale / Billing (only for wholesale customers) --}}
                        @if ($isWholesale)
                            <div class="account-card">
                                <h5 class="card-section-title">Wholesale / Billing</h5>

                                @if (session()->has('admin_wholesale_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('admin_wholesale_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form wire:submit.prevent="saveWholesaleBilling">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_online_wholesaler" wire:model="is_online_wholesaler">
                                            <label class="form-check-label fw-bold" for="is_online_wholesaler">Is Online Wholesaler?</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Add Store Record</label>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <input type="date" class="form-control" id="store_date" wire:model="store_date">
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <input type="number" class="form-control" id="store_count" wire:model="store_count" min="1" max="100" placeholder="# of Stores">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label for="accounts_payable_email" class="form-label">Accounts Payable Email</label>
                                        <input type="email" class="form-control @error('accounts_payable_email') border-danger @enderror" id="accounts_payable_email" wire:model="accounts_payable_email">
                                        @error('accounts_payable_email') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="net_terms_approved" wire:model="net_terms_approved">
                                                <label class="form-check-label fw-bold" for="net_terms_approved">Approved for Net 30 Terms</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label for="credit_limit_option" class="form-label">Net 30 Credit Limit</label>
                                            <select class="form-select @error('credit_limit') border-danger @enderror" id="credit_limit_option" wire:model.live="credit_limit_option">
                                                <option value="">Not approved</option>
                                                <option value="500">$500</option>
                                                <option value="1000">$1,000</option>
                                                <option value="3000">$3,000</option>
                                                <option value="4000">$4,000</option>
                                                <option value="5000">$5,000</option>
                                                <option value="7000">$7,000</option>
                                                <option value="10000">$10,000</option>
                                                <option value="custom">Custom Limit...</option>
                                            </select>
                                            @if ($credit_limit_option === 'custom')
                                                <div class="input-group mt-2">
                                                    <span class="input-group-text" style="border: 2px solid #e8e8e8; border-right: none; border-radius: 10px 0 0 10px;">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="credit_limit" wire:model="credit_limit" placeholder="Enter custom limit" style="border-radius: 0 10px 10px 0;">
                                                </div>
                                            @endif
                                            @error('credit_limit') <div class="error-text">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 style="font-weight: 700; color: #333; margin-bottom: 1rem;">Retailer Profile</h6>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="include_in_retailer_map" wire:model.live="include_in_retailer_map">
                                            <label class="form-check-label fw-bold" for="include_in_retailer_map">Include in Retailer Map</label>
                                        </div>
                                    </div>

                                    @if ($include_in_retailer_map)
                                        <div class="retailer-map-section">
                                            <h6 style="font-weight: 700; color: #555; margin-bottom: 1rem; font-size: 0.9rem;">Retailer Map Details</h6>

                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="retailer_name" wire:model="retailer_name">
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_street" class="form-label">Street</label>
                                                    <input type="text" class="form-control" id="retailer_street" wire:model="retailer_street">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4 mb-3">
                                                    <label for="retailer_city" class="form-label">City</label>
                                                    <input type="text" class="form-control" id="retailer_city" wire:model="retailer_city">
                                                </div>
                                                <div class="col-sm-4 mb-3">
                                                    <label for="retailer_country" class="form-label">Country</label>
                                                    <select class="form-select" id="retailer_country" wire:model.live="retailer_country">
                                                        <option value="">Select Country</option>
                                                        @foreach ($this->retailerCountries as $country)
                                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 mb-3">
                                                    <label for="retailer_state" class="form-label">State</label>
                                                    @if ($this->retailerStates->count() > 0)
                                                        <select class="form-select" id="retailer_state" wire:model="retailer_state">
                                                            <option value="">Select State</option>
                                                            @foreach ($this->retailerStates as $state)
                                                                <option value="{{ $state->name }}">{{ $state->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control" id="retailer_state" wire:model="retailer_state" placeholder="State / Province">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_phone" class="form-label">Phone</label>
                                                    <input type="tel" class="form-control" id="retailer_phone" wire:model="retailer_phone">
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_toll_free_phone" class="form-label">Toll-Free Phone</label>
                                                    <input type="tel" class="form-control" id="retailer_toll_free_phone" wire:model="retailer_toll_free_phone">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_website" class="form-label">Website</label>
                                                    <input type="url" class="form-control @error('retailer_website') border-danger @enderror" id="retailer_website" wire:model="retailer_website" placeholder="https://">
                                                    @error('retailer_website') <div class="error-text">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label for="retailer_email" class="form-label">Email</label>
                                                    <input type="email" class="form-control @error('retailer_email') border-danger @enderror" id="retailer_email" wire:model="retailer_email">
                                                    @error('retailer_email') <div class="error-text">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="retailer_products_sold" class="form-label">Products Sold</label>
                                                <textarea class="form-control" id="retailer_products_sold" wire:model="retailer_products_sold" rows="2"></textarea>
                                            </div>
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-brand mt-3" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="saveWholesaleBilling">Saving...</span>
                                        <span wire:loading.remove wire:target="saveWholesaleBilling">Save Wholesale Settings</span>
                                    </button>
                                </form>
                            </div>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
