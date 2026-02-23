<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">CREATE</span> <span class="bold-font">ACCOUNT</span></h1>
            </div>
        </div>
    </section>

    <style>
        .auth-section {
            padding: 60px 0;
            background: #f9f9f9;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }
        .auth-card-body {
            padding: 2.5rem 2rem 2rem;
        }
        .auth-input-group {
            margin-bottom: 1.1rem;
        }
        .auth-input-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 0.35rem;
            display: block;
        }
        .auth-input-group .form-control {
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #fafafa;
        }
        .auth-input-group .form-control:focus {
            border-color: #d68910;
            box-shadow: 0 0 0 3px rgba(214, 137, 16, 0.15);
            background: #fff;
        }
        .auth-btn {
            background: linear-gradient(135deg, #d68910, #e8a020);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.5px;
            transition: transform 0.2s, box-shadow 0.2s;
            text-transform: uppercase;
        }
        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(214, 137, 16, 0.35);
            color: #fff;
        }
        .auth-btn:active {
            transform: translateY(0);
        }
        .auth-link {
            color: #d68910;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-link:hover {
            color: #b5740d;
            text-decoration: underline;
        }
        .auth-divider {
            position: relative;
            text-align: center;
            margin: 1.25rem 0;
        }
        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e8e8e8;
        }
        .auth-divider span {
            background: #fff;
            padding: 0 1rem;
            color: #999;
            font-size: 0.85rem;
            position: relative;
        }
        .auth-check .form-check-input:checked {
            background-color: #d68910;
            border-color: #d68910;
        }
        .auth-check .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(214, 137, 16, 0.15);
        }
        .error-text {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .auth-loading {
            display: inline-block;
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .optional-badge {
            font-size: 0.75rem;
            color: #999;
            font-weight: 400;
            margin-left: 0.25rem;
        }
    </style>

    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card">
                        <div class="auth-card-body">
                            <p class="text-center mb-4" style="color: #666; font-size: 0.95rem;">Join the Apricot Power family and start your wellness journey today.</p>

                            <form wire:submit.prevent="register">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="auth-input-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text"
                                                   class="form-control @error('first_name') border-danger @enderror"
                                                   id="first_name"
                                                   wire:model="first_name"
                                                   placeholder="John"
                                                   required>
                                            @error('first_name')
                                                <div class="error-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="auth-input-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text"
                                                   class="form-control @error('last_name') border-danger @enderror"
                                                   id="last_name"
                                                   wire:model="last_name"
                                                   placeholder="Doe"
                                                   required>
                                            @error('last_name')
                                                <div class="error-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="auth-input-group">
                                    <label for="email">Email Address</label>
                                    <input type="email"
                                           class="form-control @error('email') border-danger @enderror"
                                           id="email"
                                           wire:model="email"
                                           placeholder="you@example.com"
                                           required>
                                    @error('email')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="auth-input-group">
                                            <label for="phone">Phone <span class="optional-badge">(Optional)</span></label>
                                            <input type="tel"
                                                   class="form-control @error('phone') border-danger @enderror"
                                                   id="phone"
                                                   wire:model="phone"
                                                   placeholder="(555) 123-4567">
                                            @error('phone')
                                                <div class="error-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="auth-input-group">
                                            <label for="company_name">Company <span class="optional-badge">(Optional)</span></label>
                                            <input type="text"
                                                   class="form-control @error('company_name') border-danger @enderror"
                                                   id="company_name"
                                                   wire:model="company_name"
                                                   placeholder="Company name">
                                            @error('company_name')
                                                <div class="error-text">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="auth-input-group">
                                    <label for="password">Password</label>
                                    <input type="password"
                                           class="form-control @error('password') border-danger @enderror"
                                           id="password"
                                           wire:model="password"
                                           placeholder="Minimum 8 characters"
                                           required>
                                    @error('password')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="auth-input-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           wire:model="password_confirmation"
                                           placeholder="Re-enter your password"
                                           required>
                                </div>

                                <div class="form-check auth-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="subscribe_to_list" wire:model="subscribe_to_list">
                                    <label class="form-check-label" for="subscribe_to_list" style="font-size: 0.88rem; color: #666;">
                                        Subscribe to our newsletter for exclusive deals & health tips
                                    </label>
                                </div>

                                <div class="form-check auth-check mb-3">
                                    <input type="checkbox"
                                           class="form-check-input @error('agreed_terms') border-danger @enderror"
                                           id="agreed_terms"
                                           wire:model="agreed_terms">
                                    <label class="form-check-label" for="agreed_terms" style="font-size: 0.88rem; color: #666;">
                                        I agree to the <a href="{{ route('privacy') }}" class="auth-link" target="_blank">Terms & Conditions</a>
                                    </label>
                                    @error('agreed_terms')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn auth-btn w-100 mb-3" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="register" class="auth-loading"></span>
                                    <span wire:loading.remove wire:target="register">Create Account</span>
                                    <span wire:loading wire:target="register">Creating Account...</span>
                                </button>
                            </form>

                            <div class="auth-divider">
                                <span>Already a member?</span>
                            </div>

                            <div class="text-center">
                                <p class="mb-0" style="color: #666;">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="auth-link">Sign In</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
