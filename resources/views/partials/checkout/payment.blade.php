<div class="bg-white border border-gray-100 rounded-xl">
    <div class="flex items-center h-16 px-6 border-b border-gray-100">
        <h3 class="text-lg font-medium">
            Payment
        </h3>
    </div>

    @if ($currentStep >= $step)
        <div class="p-6 space-y-4">
            {{-- Disclaimer Agreement Section --}}
            @if($this->hasDisclaimerRequirements)
                <div class="p-4 mb-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <h4 class="font-medium text-amber-800 mb-2">Product Disclaimers</h4>
                    @foreach($this->productsRequiringAgreement as $product)
                        <div class="p-2 mb-2 bg-white rounded border text-sm">
                            <p class="font-medium">{{ $product['name'] }}</p>
                            <p class="mt-1 text-gray-600">{{ $product['disclaimer'] }}</p>
                        </div>
                    @endforeach
                    <div class="mt-3 flex items-start">
                        <input type="checkbox"
                               id="disclaimersAgreed"
                               wire:model="disclaimersAgreed"
                               class="mt-1 rounded border-amber-300 text-amber-600 focus:ring-amber-500 @error('disclaimersAgreed') border-red-500 @enderror">
                        <label for="disclaimersAgreed" class="ml-2 text-sm font-medium text-amber-800">
                            I have read and acknowledge the above product disclaimers <span class="text-red-600">*</span>
                        </label>
                    </div>
                    @error('disclaimersAgreed')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="flex gap-4">
                <button @class([
                    'px-5 py-2 text-sm border font-medium rounded-lg',
                    'text-green-700 border-green-600 bg-green-50' => $paymentType === 'card',
                    'text-gray-500 hover:text-gray-700' => $paymentType !== 'card',
                ])
                        type="button"
                        wire:click.prevent="$set('paymentType', 'card')">
                    Pay by card
                </button>

                <button @class([
                    'px-5 py-2 text-sm border font-medium rounded-lg',
                    'text-green-700 border-green-600 bg-green-50' => $paymentType === 'cash-in-hand',
                    'text-gray-500 hover:text-gray-700' => $paymentType !== 'cash-in-hand',
                ])
                        type="button"
                        wire:click.prevent="$set('paymentType', 'cash-in-hand')">
                    Pay with cash
                </button>
            </div>

            @if ($paymentType == 'card')
                <livewire:stripe.payment :cart="$cart"
                                         :returnUrl="route('checkout.view')" />
            @endif

            @if ($paymentType == 'cash-in-hand')
                <form wire:submit="checkout">
                    <div class="p-4 text-sm text-center text-blue-700 rounded-lg bg-blue-50">
                        Payment is offline, no card details needed.
                    </div>

                    <button class="px-5 py-3 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500"
                            type="submit"
                            wire:key="payment_submit_btn">
                        <span wire:loading.remove.delay
                              wire:target="checkout">
                            Submit Order
                        </span>
                        <span wire:loading.delay
                              wire:target="checkout">
                            <svg class="w-5 h-5 text-white animate-spin"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75"
                                      fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
