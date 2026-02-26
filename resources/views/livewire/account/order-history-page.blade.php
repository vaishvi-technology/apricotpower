<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">ORDER</span> <span class="bold-font">HISTORY</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .btn-brand-outline { border: 2px solid #d68910; border-radius: 10px; padding: 0.4rem 1.25rem; font-weight: 600; color: #d68910; background: transparent; transition: all 0.2s; font-size: 0.85rem; }
        .btn-brand-outline:hover { background: #d68910; color: #fff; }
        .order-table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #888; font-weight: 600; border-bottom: 2px solid #f0f0f0; }
        .order-table td { vertical-align: middle; color: #555; padding: 1rem 0.75rem; border-bottom: 1px solid #f5f5f5; }
        .order-status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .order-status.completed { background: #d4edda; color: #155724; }
        .order-status.pending { background: #fff3cd; color: #856404; }
        .order-status.processing { background: #cce5ff; color: #004085; }
        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-state svg { width: 4rem; height: 4rem; color: #ccc; margin-bottom: 1rem; }
    </style>

    <section class="account-section">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3 mb-4">
                    @include('partials.account-sidebar')
                </div>

                {{-- Content --}}
                <div class="col-lg-9">
                    @if ($selectedOrder)
                        {{-- Order Detail --}}
                        <div class="account-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:0;">
                                    ORDER #{{ $selectedOrder->reference }}
                                </h4>
                                <a href="{{ route('order-history.view') }}" class="btn-brand-outline" wire:navigate>&larr; Back</a>
                            </div>

                            <div class="row mb-4">
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Date</small>
                                    <strong>{{ $selectedOrder->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="order-status {{ $selectedOrder->placed_at ? 'completed' : 'pending' }}">
                                        {{ $selectedOrder->placed_at ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Total</small>
                                    <strong>${{ number_format($selectedOrder->total?->value / 100, 2) }}</strong>
                                </div>
                            </div>

                            <h6 style="font-weight:700; margin-bottom:0.75rem;">Items</h6>
                            <div class="table-responsive">
                                <table class="table order-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th class="text-end">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedOrder->lines as $line)
                                            <tr>
                                                <td>{{ $line->description }}</td>
                                                <td>{{ $line->quantity }}</td>
                                                <td class="text-end">${{ number_format($line->sub_total?->value / 100, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($selectedOrder->shippingAddress)
                                <h6 style="font-weight:700; margin-top:1.5rem; margin-bottom:0.75rem;">Shipping Address</h6>
                                <p style="color:#666; margin-bottom:0;">
                                    {{ $selectedOrder->shippingAddress->first_name }} {{ $selectedOrder->shippingAddress->last_name }}<br>
                                    {{ $selectedOrder->shippingAddress->line_one }}<br>
                                    @if ($selectedOrder->shippingAddress->line_two){{ $selectedOrder->shippingAddress->line_two }}<br>@endif
                                    {{ $selectedOrder->shippingAddress->city }}, {{ $selectedOrder->shippingAddress->state }} {{ $selectedOrder->shippingAddress->postcode }}
                                </p>
                            @endif
                        </div>
                    @else
                        {{-- Order List --}}
                        <div class="account-card">
                            <h4 style="font-family:'lemonMilk',serif; font-weight:700; color:#333; margin-bottom:1.5rem;">Your Orders</h4>

                            @if ($this->orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table order-table">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->orders as $order)
                                                <tr>
                                                    <td><strong>{{ $order->reference }}</strong></td>
                                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <span class="order-status {{ $order->placed_at ? 'completed' : 'pending' }}">
                                                            {{ $order->placed_at ? 'Completed' : 'Pending' }}
                                                        </span>
                                                    </td>
                                                    <td>${{ number_format($order->total?->value / 100, 2) }}</td>
                                                    <td class="text-end">
                                                        <button wire:click="viewOrder('{{ $order->id }}')" class="btn-brand-outline">View</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    <h5 style="color:#666; font-weight:600;">No Orders Yet</h5>
                                    <p style="color:#999; margin-bottom:1.5rem;">Start shopping to see your order history here.</p>
                                    <a href="{{ route('store') }}" class="btn btn-brand">Start Shopping</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
