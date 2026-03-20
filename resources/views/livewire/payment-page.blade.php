<div>
    {{-- Accept.js Script --}}
    <script src="{{ $this->acceptJsUrl }}"></script>

    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-start">

            {{-- Order Summary (Right Side) --}}
            <div class="px-6 py-8 space-y-4 bg-white border border-gray-100 lg:sticky lg:top-8 rounded-xl lg:order-last">
                <h3 class="font-medium text-lg">Order Summary</h3>

                @if($cart)
                <div class="flow-root">
                    <div class="-my-4 divide-y divide-gray-100">
                        @foreach ($cart->lines as $line)
                            <div class="flex items-center py-4" wire:key="cart_line_{{ $line->id }}">
                                @if($line->purchasable->product->thumbnail)
                                    <img class="object-cover w-16 h-16 rounded"
                                         src="{{ $line->purchasable->getThumbnail()?->getUrl() }}"
                                         alt="{{ $line->purchasable->getDescription() }}"/>
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 ml-4">
                                    <p class="text-sm font-medium max-w-[35ch]">
                                        {{ $line->purchasable->getDescription() }}
                                    </p>
                                    <span class="block mt-1 text-xs text-gray-500">
                                        {{ $line->quantity }} x {{ $line->unitPrice?->formatted() ?? '$0.00' }}
                                    </span>
                                </div>
                                <div class="text-sm font-medium">
                                    {{ $line->subTotal?->formatted() ?? '$0.00' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flow-root pt-4 border-t border-gray-100">
                    <dl class="-my-4 text-sm divide-y divide-gray-100">
                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium">Subtotal</dt>
                            <dd class="w-1/2 text-right">{{ $cart->subTotal?->formatted() ?? '$0.00' }}</dd>
                        </div>

                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium">Shipping</dt>
                            <dd class="w-1/2 text-right text-gray-500">Calculated at checkout</dd>
                        </div>

                        @if($cart->taxTotal && $cart->taxTotal->value > 0)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">Tax</dt>
                                <dd class="w-1/2 text-right">{{ $cart->taxTotal->formatted() }}</dd>
                            </div>
                        @endif

                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-bold text-lg">Total</dt>
                            <dd class="w-1/2 text-right font-bold text-lg">{{ $cart->total?->formatted() ?? '$0.00' }}</dd>
                        </div>
                    </dl>
                </div>
                @else
                <div class="text-center text-gray-500 py-4">
                    Loading cart...
                </div>
                @endif
            </div>

            {{-- Payment Form (Left Side) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Error Message --}}
                @if($errorMessage)
                    <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $errorMessage }}
                        </div>
                    </div>
                @endif

                {{-- Success Message --}}
                @if($successMessage)
                    <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $successMessage }}
                        </div>
                    </div>
                @endif

                {{-- Billing Information --}}
                <div class="bg-white border border-gray-100 rounded-xl">
                    <div class="flex items-center h-16 px-6 border-b border-gray-100">
                        <h3 class="text-lg font-medium">Billing Information</h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="firstName"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="John"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="lastName"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Doe"
                                       required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       wire:model="email"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="john@example.com"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel"
                                       wire:model="phone"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="(555) 123-4567">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="address"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="123 Main Street"
                                   required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="city"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Los Angeles"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="state"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="CA"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Postal Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="postalCode"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="90001"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="country"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="US">USA</option>
                                    <option value="CA">Canada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Payment --}}
                <div class="bg-white border border-gray-100 rounded-xl">
                    <div class="flex items-center h-16 px-6 border-b border-gray-100">
                        <h3 class="text-lg font-medium">Payment Details</h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Card Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="card-number"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="4111 1111 1111 1111"
                                   maxlength="19"
                                   autocomplete="cc-number">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Expiration (MM/YY) <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="card-expiry"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="MM/YY"
                                       maxlength="5"
                                       autocomplete="cc-exp">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    CVV <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="card-cvv"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="123"
                                       maxlength="4"
                                       autocomplete="cc-csc">
                            </div>
                        </div>

                        {{-- Test Cards Info (for sandbox) --}}
                        @if(config('lunar.authorizenet.environment') !== 'production')
                            <div class="p-3 text-xs text-blue-700 bg-blue-50 rounded-lg border border-blue-100">
                                <strong>Test Card Numbers:</strong><br>
                                Visa: 4111 1111 1111 1111 | MC: 5424 0000 0000 0015<br>
                                CVV: 123 | Expiry: Any future date
                            </div>
                        @endif

                        {{-- Submit Button --}}
                        <button type="button"
                                id="submit-payment"
                                onclick="submitPayment()"
                                class="w-full px-5 py-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-lg mt-4"
                                {{ $processing ? 'disabled' : '' }}>
                            <span id="btn-text">
                                Pay {{ $cart?->total?->formatted() ?? 'Now' }}
                            </span>
                            <span id="btn-loading" class="hidden items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing Payment...
                            </span>
                        </button>

                        {{-- Security Note --}}
                        <p class="text-xs text-gray-500 text-center flex items-center justify-center mt-4">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Secure payment powered by Authorize.net
                        </p>
                    </div>
                </div>

                {{-- Back to Cart --}}
                <div class="text-center">
                    <a href="{{ route('cart.view') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        &larr; Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Debug: Log HTTPS detection
        console.log('=== HTTPS Debug Info ===');
        console.log('Protocol:', window.location.protocol);
        console.log('Is Secure:', window.isSecureContext);
        console.log('Location:', window.location.href);
        console.log('Accept.js loaded:', typeof Accept !== 'undefined');

        // Check if Accept.js loaded
        if (typeof Accept === 'undefined') {
            console.error('Accept.js failed to load! This usually means the page is not served over HTTPS.');
        }

        function submitPayment() {
            const btn = document.getElementById('submit-payment');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');

            // Get card details
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiry = document.getElementById('card-expiry').value.split('/');
            const cvv = document.getElementById('card-cvv').value;

            // Basic validation
            if (!cardNumber || cardNumber.length < 13) {
                @this.dispatch('acceptJsError', { error: 'Please enter a valid card number' });
                return;
            }
            if (expiry.length !== 2 || !expiry[0] || !expiry[1]) {
                @this.dispatch('acceptJsError', { error: 'Please enter a valid expiration date (MM/YY)' });
                return;
            }
            if (!cvv || cvv.length < 3) {
                @this.dispatch('acceptJsError', { error: 'Please enter a valid CVV' });
                return;
            }

            // Validate billing first
            if (!@this.validateBilling()) {
                return;
            }

            // Show loading state
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');

            // Prepare Accept.js data
            const secureData = {
                authData: {
                    clientKey: '{{ $this->clientKey }}',
                    apiLoginID: '{{ $this->apiLoginId }}'
                },
                cardData: {
                    cardNumber: cardNumber,
                    month: expiry[0]?.trim().padStart(2, '0'),
                    year: expiry[1]?.trim().length === 2 ? '20' + expiry[1].trim() : expiry[1]?.trim(),
                    cardCode: cvv
                }
            };

            // Call Accept.js to tokenize card
            console.log('=== Calling Accept.dispatchData ===');
            console.log('Secure Data:', JSON.stringify(secureData, null, 2));

            if (typeof Accept === 'undefined') {
                @this.dispatch('acceptJsError', { error: 'Accept.js not loaded. Please access this page via HTTPS (https://localhost).' });
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btnLoading.classList.remove('flex');
                return;
            }

            Accept.dispatchData(secureData, function(response) {
                console.log('Accept.js Response:', JSON.stringify(response, null, 2));
                if (response.messages.resultCode === 'Error') {
                    let errors = response.messages.message.map(m => m.text).join(', ');
                    @this.dispatch('acceptJsError', { error: errors });

                    // Reset button
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                } else {
                    // Send token to Livewire
                    @this.dispatch('acceptJsResponse', {
                        response: {
                            opaqueDataDescriptor: response.opaqueData.dataDescriptor,
                            opaqueDataValue: response.opaqueData.dataValue
                        }
                    });
                }
            });
        }

        // Card number formatting (add spaces)
        document.getElementById('card-number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Expiry formatting (MM/YY)
        document.getElementById('card-expiry')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });

        // CVV - numbers only
        document.getElementById('card-cvv')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Listen for Livewire processing state changes
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('message.processed', (message, component) => {
                const btn = document.getElementById('submit-payment');
                const btnText = document.getElementById('btn-text');
                const btnLoading = document.getElementById('btn-loading');

                if (!@this.processing) {
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                }
            });
        });
    </script>
</div>
