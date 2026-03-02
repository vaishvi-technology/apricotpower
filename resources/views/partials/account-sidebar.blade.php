@php
    $currentRoute = request()->route()?->getName();
    $impersonating = app(\App\Services\ImpersonationService::class)->isImpersonating();
@endphp

<div class="account-sidebar">
    <h6 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:1rem;">MY ACCOUNT</h6>
    <nav class="nav flex-column">
        <a class="nav-link {{ $currentRoute === 'basic-info' ? 'active' : '' }}" href="{{ route('basic-info') }}" wire:navigate>Basic Info</a>
        <a class="nav-link {{ $currentRoute === 'account-details' ? 'active' : '' }}" href="{{ route('account-details') }}" wire:navigate>Account Details</a>
        <a class="nav-link {{ $currentRoute === 'order-history.view' || $currentRoute === 'order-history.detail' ? 'active' : '' }}" href="{{ route('order-history.view') }}" wire:navigate>Order History</a>
        <a class="nav-link {{ $currentRoute === 'email-preferences' ? 'active' : '' }}" href="{{ route('email-preferences') }}" wire:navigate>Email Preferences</a>
        @if ($impersonating)
            <a class="nav-link {{ $currentRoute === 'admin.private-notes' ? 'active' : '' }}" href="{{ route('admin.private-notes') }}" wire:navigate>Private Account Notes</a>
        @endif
        <hr class="my-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">Logout</button>
        </form>
    </nav>
</div>
