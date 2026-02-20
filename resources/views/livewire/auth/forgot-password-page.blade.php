<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">RESET</span> <span class="bold-font">PASSWORD</span></h1>
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
            margin-bottom: 1.25rem;
        }
        .auth-input-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 0.4rem;
            display: block;
        }
        .auth-input-group .form-control {
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            padding: 0.65rem 1rem;
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
        .error-text {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.3rem;
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
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1.25rem;
            color: #155724;
            text-align: center;
        }
        .success-box svg {
            width: 3rem;
            height: 3rem;
            margin-bottom: 0.75rem;
            color: #28a745;
        }
    </style>

    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="auth-card">
                        <div class="auth-card-body">
                            @if ($emailSent)
                                <div class="success-box mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <h5 style="color: #155724; font-weight: 700;">Check Your Email</h5>
                                    <p class="mb-0" style="font-size: 0.9rem;">
                                        We've sent a password reset link to <strong>{{ $email }}</strong>. Please check your inbox and spam folder.
                                    </p>
                                </div>
                            @else
                                <p style="color: #666; font-size: 0.92rem; text-align: center; margin-bottom: 1.5rem;">
                                    Enter the email address associated with your account and we'll send you a link to reset your password.
                                </p>

                                <form wire:submit.prevent="sendResetLink">
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

                                    <button type="submit" class="btn auth-btn w-100 mb-3" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="sendResetLink" class="auth-loading"></span>
                                        <span wire:loading.remove wire:target="sendResetLink">Send Reset Link</span>
                                        <span wire:loading wire:target="sendResetLink">Sending...</span>
                                    </button>
                                </form>
                            @endif

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="auth-link" style="font-size: 0.9rem;">
                                    &larr; Back to Sign In
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
