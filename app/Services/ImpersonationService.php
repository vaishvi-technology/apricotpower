<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Lunar\Admin\Models\Staff;

class ImpersonationService
{
    public const SESSION_IMPERSONATING_CUSTOMER_ID = 'impersonating_customer_id';
    public const SESSION_IMPERSONATED_BY_ADMIN_ID = 'impersonated_by_admin_id';
    public const SESSION_ADMIN_RETURN_URL = 'impersonation_admin_return_url';

    /**
     * Roles allowed to impersonate customers.
     */
    protected array $allowedRoles = ['Super Admin', 'Admin', 'Manager'];

    /**
     * Check if the given staff user can impersonate customers.
     */
    public function canImpersonate(Authenticatable $staff): bool
    {
        // Must be a Staff model with Spatie roles
        if (!$staff instanceof Staff) {
            return false;
        }

        // Check if staff has the 'admin' flag or any of the allowed roles
        if ($staff->admin) {
            return true;
        }

        // Check Spatie roles (case-insensitive check)
        return $staff->hasAnyRole($this->allowedRoles);
    }

    /**
     * Start impersonating a customer.
     */
    public function start(Authenticatable $staff, Customer $customer, ?string $returnUrl = null): bool
    {
        if (!$this->canImpersonate($staff)) {
            return false;
        }

        // Store the staff's ID and the customer being impersonated
        Session::put(self::SESSION_IMPERSONATED_BY_ADMIN_ID, $staff->id);
        Session::put(self::SESSION_IMPERSONATING_CUSTOMER_ID, $customer->id);
        Session::put(self::SESSION_ADMIN_RETURN_URL, $returnUrl ?? '/admin/customers');

        // Log in as the customer on the customer guard
        Auth::guard('customer')->login($customer);

        return true;
    }

    /**
     * Stop impersonating and return admin info.
     */
    public function stop(): ?string
    {
        if (!$this->isImpersonating()) {
            return null;
        }

        $returnUrl = Session::get(self::SESSION_ADMIN_RETURN_URL, '/admin/customers');

        // Log out from customer guard
        Auth::guard('customer')->logout();

        // Clear impersonation session data
        Session::forget(self::SESSION_IMPERSONATING_CUSTOMER_ID);
        Session::forget(self::SESSION_IMPERSONATED_BY_ADMIN_ID);
        Session::forget(self::SESSION_ADMIN_RETURN_URL);

        return $returnUrl;
    }

    /**
     * Check if currently impersonating a customer.
     */
    public function isImpersonating(): bool
    {
        return Session::has(self::SESSION_IMPERSONATING_CUSTOMER_ID)
            && Session::has(self::SESSION_IMPERSONATED_BY_ADMIN_ID);
    }

    /**
     * Get the impersonated customer.
     */
    public function getImpersonatedCustomer(): ?Customer
    {
        if (!$this->isImpersonating()) {
            return null;
        }

        return Customer::find(Session::get(self::SESSION_IMPERSONATING_CUSTOMER_ID));
    }

    /**
     * Get the staff member who is impersonating.
     */
    public function getImpersonatingAdmin(): ?Staff
    {
        if (!$this->isImpersonating()) {
            return null;
        }

        return Staff::find(Session::get(self::SESSION_IMPERSONATED_BY_ADMIN_ID));
    }
}
