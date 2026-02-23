@php
    $impersonationService = app(\App\Services\ImpersonationService::class);
    $isImpersonating = $impersonationService->isImpersonating();
    $customer = $isImpersonating ? $impersonationService->getImpersonatedCustomer() : null;
    $admin = $isImpersonating ? $impersonationService->getImpersonatingAdmin() : null;
@endphp

@if($isImpersonating && $customer && $admin)
<div class="impersonation-banner" style="
    background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 10px 20px;
    text-align: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
">
    <div style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
        <span style="display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
            </svg>
            <strong>Impersonation Mode:</strong>
            Viewing as <strong>{{ $customer->full_name }}</strong> ({{ $customer->email }})
        </span>
        <span style="color: rgba(255,255,255,0.8); font-size: 0.85em;">
            - Staff: {{ $admin->full_name }}
        </span>
        <form action="{{ route('impersonate.stop') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="
                background: white;
                color: #dc3545;
                border: none;
                padding: 6px 16px;
                border-radius: 4px;
                font-weight: 600;
                cursor: pointer;
                font-size: 0.9em;
                transition: all 0.2s ease;
            " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                Stop Impersonating
            </button>
        </form>
    </div>
</div>
<div style="height: 50px;"></div>
@endif
