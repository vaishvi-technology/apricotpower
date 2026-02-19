@push('styles')
<style>
    body { background: #f5f0e8; }

    /* Fix nav: absolute-positioned header with white links is invisible on white form bg */
    .main-header {
        position: relative !important;
        background: linear-gradient(90deg, #c97d0a 0%, #d68910 100%) !important;
    }
    /* Keep login icon dark text visible */
    .text-login { color: #fff !important; }
    /* Dropdown toggle button text */
    .main-header .btn-link { color: #fff !important; }

    .auth-screen {
        min-height: 100vh;
        display: flex;
        align-items: stretch;
    }

    /* ── Brand panel ─────────────────────────────────── */
    .auth-brand {
        background: linear-gradient(160deg, #c97d0a 0%, #d68910 50%, #e8a225 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .auth-brand::before {
        content: '';
        position: absolute;
        width: 350px; height: 350px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
        top: -80px; right: -80px;
    }
    .auth-brand::after {
        content: '';
        position: absolute;
        width: 250px; height: 250px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
        bottom: -60px; left: -60px;
    }
    .auth-brand-inner { position: relative; z-index: 1; text-align: center; }
    .auth-brand img { max-width: 180px; margin-bottom: 2rem; filter: brightness(0) invert(1); }
    .auth-brand h2 {
        color: #fff;
        font-size: 1.65rem;
        font-weight: 700;
        margin-bottom: .6rem;
        letter-spacing: -.3px;
    }
    .auth-brand p {
        color: rgba(255,255,255,.85);
        font-size: .95rem;
        line-height: 1.6;
        max-width: 280px;
        margin: 0 auto 2rem;
    }
    .auth-trust-list {
        list-style: none;
        padding: 0; margin: 0;
        text-align: left;
        display: inline-block;
    }
    .auth-trust-list li {
        color: rgba(255,255,255,.9);
        font-size: .9rem;
        margin-bottom: .65rem;
        display: flex;
        align-items: center;
        gap: .55rem;
    }

    /* ── Form panel ──────────────────────────────────── */
    .auth-form-panel {
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 2.5rem;
    }
    .auth-form-wrap { max-width: 420px; width: 100%; margin: 0 auto; }

    .auth-title {
        font-size: 1.65rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: .35rem;
    }
    .auth-subtitle {
        color: #6b7280;
        font-size: .92rem;
        margin-bottom: 2rem;
    }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    .input-icon-wrap .form-control { padding-left: 2.6rem; }

    .form-label { font-weight: 600; font-size: .85rem; color: #374151; margin-bottom: .4rem; }
    .form-control {
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        font-size: .93rem;
        padding: .6rem .85rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus {
        border-color: #d68910;
        box-shadow: 0 0 0 .2rem rgba(214,137,16,.18);
        outline: none;
    }
    .form-control.is-invalid { border-color: #ef4444; }

    .btn-brand {
        background: linear-gradient(135deg, #d68910, #c97d0a);
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: .95rem;
        padding: .72rem 1rem;
        border-radius: 8px;
        letter-spacing: .2px;
        transition: transform .15s, box-shadow .15s;
    }
    .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,.4); color: #fff; }
    .btn-brand:active { transform: translateY(0); }
    .btn-brand:disabled { opacity: .7; transform: none; }

    .auth-link { color: #d68910; font-weight: 600; text-decoration: none; }
    .auth-link:hover { color: #b5770e; text-decoration: underline; }

    .remember-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .form-check-input:checked { background-color: #d68910; border-color: #d68910; }
    .form-check-label { font-size: .88rem; color: #6b7280; }
    .auth-footer-text { text-align: center; font-size: .88rem; color: #6b7280; margin-top: 1.5rem; }
</style>
@endpush

<div class="auth-screen">

    {{-- ── Brand Panel (desktop only) ── --}}
    <div class="auth-brand col-lg-5 d-none d-lg-flex">
        <div class="auth-brand-inner">
            <img src="{{ asset('images/home/logo-white.png') }}" alt="Apricot Power">
            <h2>Welcome Back!</h2>
            <p>Sign in to manage your orders, track shipments, and access your account.</p>
            <ul class="auth-trust-list">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Premium quality since 1999
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Fast &amp; reliable shipping
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Secure &amp; trusted checkout
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Dedicated customer support
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Form Panel ── --}}
    <div class="auth-form-panel col-12 col-lg-7">
        <div class="auth-form-wrap">

            {{-- Mobile logo --}}
            <div class="text-center mb-4 d-lg-none">
                <img src="{{ asset('images/home/logo.png') }}" alt="Apricot Power" style="max-height:50px;">
            </div>

            <h1 class="auth-title">Sign in to your account</h1>
            <p class="auth-subtitle">Enter your credentials below to access your account.</p>

            <form wire:submit.prevent="login" novalidate>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/></svg>
                        </span>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               wire:model="email"
                               placeholder="you@example.com"
                               autocomplete="email"
                               required>
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
                        </span>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               wire:model="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember / Forgot --}}
                <div class="remember-row">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="remember" wire:model="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('forgot-password') }}" class="auth-link" style="font-size:.88rem;">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-brand w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Signing in…
                    </span>
                </button>

            </form>

            <p class="auth-footer-text">
                Don't have an account?
                <a href="{{ route('register') }}" class="auth-link">Create one free</a>
            </p>

        </div>
    </div>

</div>
