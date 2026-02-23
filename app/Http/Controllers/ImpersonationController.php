<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function __construct(
        protected ImpersonationService $impersonationService
    ) {}

    /**
     * Start impersonating a customer (called from admin panel).
     */
    public function start(Request $request, Customer $customer): RedirectResponse
    {
        $staff = Auth::guard('staff')->user();

        if (!$staff || !$this->impersonationService->canImpersonate($staff)) {
            abort(403, 'You do not have permission to impersonate customers.');
        }

        // Store the referring page as return URL
        $returnUrl = $request->header('Referer', '/admin/customers');

        $this->impersonationService->start($staff, $customer, $returnUrl);

        // Redirect to storefront home
        return redirect('/')->with('success', "Now viewing as {$customer->full_name}");
    }

    /**
     * Stop impersonating and return to admin panel.
     */
    public function stop(): RedirectResponse
    {
        $returnUrl = $this->impersonationService->stop();

        if (!$returnUrl) {
            return redirect('/');
        }

        return redirect($returnUrl)->with('success', 'Impersonation ended.');
    }
}
