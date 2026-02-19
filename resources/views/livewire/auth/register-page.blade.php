@push('styles')
<style>
    body { background: #f5f0e8; }

    /* Fix nav: absolute-positioned header with white links is invisible on white form bg */
    .main-header {
        position: relative !important;
        background: linear-gradient(90deg, #c97d0a 0%, #d68910 100%) !important;
    }
    .text-login { color: #fff !important; }
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
        overflow-y: auto;
    }
    .auth-form-wrap { max-width: 520px; width: 100%; margin: 0 auto; }

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

    /* Section dividers */
    .form-section-label {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #9ca3af;
        margin-bottom: .85rem;
        margin-top: 1.6rem;
        padding-bottom: .4rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .form-section-label:first-of-type { margin-top: 0; }

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

    .form-label {
        font-weight: 600;
        font-size: .85rem;
        color: #374151;
        margin-bottom: .4rem;
    }
    .label-optional {
        font-weight: 400;
        font-size: .78rem;
        color: #9ca3af;
        margin-left: .3rem;
    }
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
    .form-hint { font-size: .78rem; color: #9ca3af; margin-top: .3rem; }

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

    .auth-footer-text { text-align: center; font-size: .88rem; color: #6b7280; margin-top: 1.5rem; }

    .required-note {
        font-size: .78rem;
        color: #9ca3af;
        margin-bottom: 1.5rem;
    }
    .required-note span { color: #ef4444; }
</style>
@endpush

<div class="auth-screen">

    {{-- ── Brand Panel (desktop only) ── --}}
    <div class="auth-brand col-lg-4 d-none d-lg-flex">
        <div class="auth-brand-inner">
            <img src="{{ asset('images/home/logo-white.png') }}" alt="Apricot Power">
            <h2>Join Apricot Power</h2>
            <p>Create your free account and start shopping our premium apricot seed products.</p>
            <ul class="auth-trust-list">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Free account, no fees
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Order history &amp; tracking
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Faster checkout saved info
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>
                    Exclusive member pricing
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Form Panel ── --}}
    <div class="auth-form-panel col-12 col-lg-8">
        <div class="auth-form-wrap">

            {{-- Mobile logo --}}
            <div class="text-center mb-4 d-lg-none">
                <img src="{{ asset('images/home/logo.png') }}" alt="Apricot Power" style="max-height:50px;">
            </div>

            <h1 class="auth-title">Create your account</h1>
            <p class="auth-subtitle">It's free and takes less than a minute.</p>

            <p class="required-note">Fields marked <span>*</span> are required.</p>

            <form wire:submit.prevent="register" novalidate>

                {{-- Personal Info ─────────────────────────── --}}
                <p class="form-section-label">Personal Information</p>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.029 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg>
                            </span>
                            <input type="text"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name"
                                   wire:model="first_name"
                                   placeholder="Jane"
                                   autocomplete="given-name"
                                   required>
                        </div>
                        @error('first_name')
                            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name"
                               wire:model="last_name"
                               placeholder="Doe"
                               autocomplete="family-name"
                               required>
                        @error('last_name')
                            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="phone" class="form-label">Phone <span class="label-optional">(optional)</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58z"/></svg>
                            </span>
                            <input type="tel"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   wire:model="phone"
                                   placeholder="555-000-0000"
                                   autocomplete="tel">
                        </div>
                        @error('phone')
                            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="company_name" class="form-label">Company <span class="label-optional">(optional)</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/><path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/></svg>
                            </span>
                            <input type="text"
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name"
                                   wire:model="company_name"
                                   placeholder="Company name"
                                   autocomplete="organization">
                        </div>
                        @error('company_name')
                            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Account Info ────────────────────────────── --}}
                <p class="form-section-label">Account Information</p>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
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

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
                            </span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   wire:model="password"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password"
                                   required>
                        </div>
                        @error('password')
                            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
                            </span>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   wire:model="password_confirmation"
                                   placeholder="Re-enter password"
                                   autocomplete="new-password"
                                   required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-brand w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create Account</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Creating account…
                    </span>
                </button>

            </form>

            <p class="auth-footer-text">
                Already have an account?
                <a href="{{ route('login') }}" class="auth-link">Sign in</a>
            </p>

        </div>
    </div>

</div>
