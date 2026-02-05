<div class="forgot-password-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Forgot Password</h2>
                        <p class="text-center text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

                        <form wire:submit.prevent="sendResetLink">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" wire:model="email" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-warning w-100 mb-3">Send Reset Link</button>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">Back to Log In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
