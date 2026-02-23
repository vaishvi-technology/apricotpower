<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Customer Registration
     *
     * Creates a new customer account.
     * Legacy fields mapped:
     *   Email          -> email
     *   Password       -> password (bcrypt hashed)
     *   ShippingNameFirst -> first_name
     *   ShippingNameLast  -> last_name
     *   Company        -> company_name
     *   Phone          -> phone
     *   AccountGroupID -> customer_group_id
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'is_active' => true,
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'customer' => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'company_name' => $customer->company_name,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Customer Login
     *
     * Authenticates customer via email/password.
     * Legacy: Passwords were stored in plaintext; migrated passwords
     * must be re-hashed with bcrypt during data migration.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('email', $validated['email'])->first();

        if (! $customer || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (! $customer->is_active) {
            return response()->json([
                'message' => 'Account is deactivated. Please contact support.',
            ], 403);
        }

        $customer->update(['last_login_at' => now()]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'customer' => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'company_name' => $customer->company_name,
                'customer_group_id' => $customer->customer_group_id,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Customer Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get authenticated customer profile
     */
    public function profile(Request $request): JsonResponse
    {
        $customer = $request->user();
        $customer->load(['customerGroup', 'defaultShippingAddress', 'defaultBillingAddress']);

        return response()->json([
            'customer' => $customer,
        ]);
    }
}
