<div>
    <script src="{{ $this->acceptJsUrl }}"></script>

    <div class="space-y-4">
        {{-- Error Display --}}
        @if($errorMessage)
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Saved Cards --}}
        @if($savedCards->isNotEmpty())
            <div class="space-y-2">
                <h4 class="font-medium text-gray-700">Saved Payment Methods</h4>

                @foreach($savedCards as $card)
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                        {{ $selectedPaymentMethodId === $card->id && !$useNewCard ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio"
                               name="payment_method"
                               value="{{ $card->id }}"
                               wire:click="selectPaymentMethod({{ $card->id }})"
                               {{ $selectedPaymentMethodId === $card->id && !$useNewCard ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <span class="font-medium">{{ $card->display_name }}</span>
                            @if($card->exp_month && $card->exp_year)
                                <span class="text-sm text-gray-500 ml-2">
                                    Exp: {{ str_pad($card->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ substr($card->exp_year, -2) }}
                                </span>
                            @endif
                            @if($card->is_default)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded">Default</span>
                            @endif
                        </div>
                    </label>
                @endforeach

                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50
                    {{ $useNewCard ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio"
                           name="payment_method"
                           value="new"
                           wire:click="useNewCardForm"
                           {{ $useNewCard ? 'checked' : '' }}
                           class="mr-3 text-blue-600 focus:ring-blue-500">
                    <span class="font-medium">Use a new card</span>
                </label>
            </div>
        @endif

        {{-- New Card Form --}}
        @if($useNewCard)
            <div class="space-y-4 p-4 bg-gray-50 rounded-lg" id="card-form">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                    <input type="text"
                           id="card-number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="4111 1111 1111 1111"
                           maxlength="19"
                           autocomplete="cc-number">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiration (MM/YY)</label>
                        <input type="text"
                               id="card-expiry"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="MM/YY"
                               maxlength="5"
                               autocomplete="cc-exp">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                        <input type="text"
                               id="card-cvv"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="123"
                               maxlength="4"
                               autocomplete="cc-csc">
                    </div>
                </div>

                @auth
                    @if($cart->customer)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="saveCard" class="mr-2 text-blue-600 focus:ring-blue-500 rounded">
                            <span class="text-sm text-gray-700">Save card for future purchases</span>
                        </label>
                    @endif
                @endauth
            </div>
        @endif

        {{-- Submit Button --}}
        <button type="button"
                id="submit-payment"
                wire:loading.attr="disabled"
                class="w-full px-5 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                onclick="submitPayment()"
                {{ $processing ? 'disabled' : '' }}>
            <span wire:loading.remove wire:target="processPayment">
                Pay {{ $cart->total?->formatted() ?? '' }}
            </span>
            <span wire:loading wire:target="processPayment" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>

        {{-- Security Note --}}
        <p class="text-xs text-gray-500 text-center flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            Secure payment powered by Authorize.net
        </p>
    </div>

    <script>
        function submitPayment() {
            @if(!$useNewCard && $selectedPaymentMethodId)
                @this.processPayment();
            @else
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

                // Disable button while processing
                document.getElementById('submit-payment').disabled = true;

                Accept.dispatchData(secureData, function(response) {
                    if (response.messages.resultCode === 'Error') {
                        let errors = response.messages.message.map(m => m.text).join(', ');
                        @this.dispatch('acceptJsError', { error: errors });
                        document.getElementById('submit-payment').disabled = false;
                    } else {
                        @this.dispatch('acceptJsResponse', {
                            response: {
                                opaqueDataDescriptor: response.opaqueData.dataDescriptor,
                                opaqueDataValue: response.opaqueData.dataValue
                            }
                        });
                    }
                });
            @endif
        }

        // Card number formatting
        document.getElementById('card-number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });

        // Expiry formatting
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
    </script>
</div>
