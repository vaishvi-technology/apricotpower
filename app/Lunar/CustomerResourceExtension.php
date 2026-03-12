<?php

namespace App\Lunar;

use App\Filament\RelationManagers\CustomerAddressRelationManager;
use App\Models\CustomerGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\CustomerResource\RelationManagers\AddressRelationManager;
use Lunar\Admin\Support\Extending\ResourceExtension;

class CustomerResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        // Completely replace Lunar's default layout with a clean tab-based design.
        // We discard Lunar's default components (top section + sidebar) and rebuild everything in tabs.
        return $form->schema([
            Forms\Components\Tabs::make('Customer')
                ->columnSpanFull()
                ->tabs([

                    // ── Tab 1: Basic Info (matches frontend "Basic Account Information") ──
                    Forms\Components\Tabs\Tab::make('Basic Info')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Section::make('Password')
                                ->description('Leave blank to keep the current password unchanged.')
                                ->collapsible()
                                ->collapsed(fn (?Model $record): bool => $record !== null)
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->label('New Password')
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->live(debounce: 500)
                                            ->autocomplete('new-password'),

                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->label('Confirm New Password')
                                            ->password()
                                            ->revealable()
                                            ->same('password')
                                            ->requiredWith('password')
                                            ->dehydrated(false)
                                            ->autocomplete('new-password'),
                                    ]),
                                ]),
                        ]),

                    // ── Tab 2: Account Details ──
                    Forms\Components\Tabs\Tab::make('Account Details')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            // Account Information
                            Forms\Components\Section::make('Account Information')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Select::make('referred_by')
                                            ->label('How Did You Hear About Us?')
                                            ->options([
                                                'Family or Friend' => 'Family or Friend',
                                                'Doctor or Clinic' => 'Doctor or Clinic',
                                                'Search Engine' => 'Search Engine',
                                                'Internet Article' => 'Internet Article',
                                                'Advertisement' => 'Advertisement',
                                                'Facebook' => 'Facebook',
                                                'Natural News' => 'Natural News',
                                                'Book' => 'Book',
                                                'Email or Newsletter' => 'Email or Newsletter',
                                                'Church' => 'Church',
                                                'Unfiltered News' => 'Unfiltered News',
                                                'Event, Expo or Tradeshow' => 'Event, Expo or Tradeshow',
                                                'Other...' => 'Other...',
                                            ])
                                            ->searchable(),

                                        Forms\Components\Textarea::make('b17_knowledge')
                                            ->label('What do you know about B17/Apricot Seeds?')
                                            ->rows(3),
                                    ]),
                                ]),

                            // Account Details (admin fields)
                            Forms\Components\Section::make('Account Details')
                                ->schema([
                                    Forms\Components\Grid::make(4)->schema([
                                        Forms\Components\Select::make('customerGroups')
                                            ->label('Account Group')
                                            ->helperText('Determines product pricing and payment terms.')
                                            ->relationship(
                                                name: 'customerGroups',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->distinct(['id', 'name', 'handle', 'default'])
                                            )
                                            ->multiple()
                                            ->maxItems(1)
                                            ->preload()
                                            ->searchable()
                                            ->live()
                                            ->dehydrated(false)
                                            ->columnSpan(2),

                                        Forms\Components\Toggle::make('account_locked')
                                            ->label('Account Locked / Closed')
                                            ->helperText('Prevents signing in and hides from searches.')
                                            ->onColor('danger')
                                            ->offColor('success')
                                            ->inline(false),
                                    ]),

                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Select::make('sales_rep_id')
                                            ->label('Sales Rep.')
                                            ->relationship('salesRep', 'first_name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->first_name} {$record->last_name}"))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('None Assigned'),

                                        Forms\Components\Toggle::make('is_tax_exempt')
                                            ->label('Tax Exempt')
                                            ->onColor('success')
                                            ->inline(false),
                                    ]),

                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Placeholder::make('last_login_at_display')
                                            ->label('Last Login')
                                            ->content(fn ($record) => $record?->last_login_at?->format('M d, Y h:i A') ?? 'Never'),

                                        Forms\Components\Placeholder::make('last_order_at_display')
                                            ->label('Last Order')
                                            ->content(fn ($record) => $record?->last_order_at?->format('M d, Y h:i A') ?? 'Never'),
                                    ]),
                                ]),

                            // Wholesale / Billing (visible only for wholesale group)
                            Forms\Components\Section::make('Wholesale / Billing')
                                ->icon('heroicon-o-building-storefront')
                                ->visible(function (Forms\Get $get, ?Model $record): bool {
                                    $selectedGroupIds = $get('customerGroups') ?? [];
                                    if (! empty($selectedGroupIds)) {
                                        return CustomerGroup::whereIn('id', $selectedGroupIds)
                                            ->where('is_wholesale', true)
                                            ->exists();
                                    }

                                    if ($record) {
                                        return $record->customerGroups()
                                            ->where('is_wholesale', true)
                                            ->exists();
                                    }

                                    return false;
                                })
                                ->schema([
                                    Forms\Components\Toggle::make('is_online_wholesaler')
                                        ->label('Is Online Wholesaler?')
                                        ->onColor('success')
                                        ->inline(false),

                                    Forms\Components\Fieldset::make('Add Store Record')
                                        ->schema([
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\DatePicker::make('store_date')
                                                    ->label('Date')
                                                    ->native(),

                                                Forms\Components\TextInput::make('store_count')
                                                    ->label('# of Stores')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->maxValue(100),
                                            ]),
                                        ]),

                                    Forms\Components\TextInput::make('accounts_payable_email')
                                        ->label('Accounts Payable Email')
                                        ->email()
                                        ->maxLength(255),

                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Toggle::make('net_terms_approved')
                                            ->label('Approved for Net 30 Terms')
                                            ->onColor('success')
                                            ->inline(false),

                                        Forms\Components\Select::make('credit_limit_option')
                                            ->label('Net 30 Credit Limit')
                                            ->options([
                                                '' => 'Not approved',
                                                '500' => '$500',
                                                '1000' => '$1,000',
                                                '3000' => '$3,000',
                                                '4000' => '$4,000',
                                                '5000' => '$5,000',
                                                '7000' => '$7,000',
                                                '10000' => '$10,000',
                                                'custom' => 'Custom Limit...',
                                            ])
                                            ->live()
                                            ->afterStateHydrated(function (Forms\Components\Select $component, $state, ?Model $record) {
                                                $value = $record?->credit_limit;
                                                $presets = [null, '500.00', '1000.00', '3000.00', '4000.00', '5000.00', '7000.00', '10000.00'];
                                                if ($value === null || $value === '') {
                                                    $component->state('');
                                                } elseif (in_array($value, $presets)) {
                                                    $component->state(rtrim(rtrim($value, '0'), '.'));
                                                } else {
                                                    $component->state('custom');
                                                }
                                            })
                                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                if ($state !== 'custom') {
                                                    $set('credit_limit', $state !== '' ? $state : null);
                                                } else {
                                                    $set('credit_limit', '');
                                                }
                                            })
                                            ->dehydrated(false),
                                    ]),

                                    Forms\Components\TextInput::make('credit_limit')
                                        ->label('Custom Credit Limit')
                                        ->numeric()
                                        ->prefix('$')
                                        ->visible(fn (Forms\Get $get) => $get('credit_limit_option') === 'custom'),

                                    Forms\Components\Group::make()
                                        ->relationship('retailerProfile')
                                        ->schema([
                                            Forms\Components\Toggle::make('include_in_retailer_map')
                                                ->label('Include in Retailer Map')
                                                ->onColor('success')
                                                ->live()
                                                ->inline(false),

                                            Forms\Components\Section::make('Retailer Map Details')
                                                ->visible(fn (Forms\Get $get): bool => (bool) $get('include_in_retailer_map'))
                                                ->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Name')
                                                            ->maxLength(255),

                                                        Forms\Components\TextInput::make('street')
                                                            ->label('Street')
                                                            ->maxLength(255),
                                                    ]),

                                                    Forms\Components\Grid::make(3)->schema([
                                                        Forms\Components\TextInput::make('city')
                                                            ->label('City')
                                                            ->maxLength(255),

                                                        Forms\Components\Select::make('country')
                                                            ->label('Country')
                                                            ->options(fn () => \Lunar\Models\Country::orderBy('name')->pluck('name', 'name'))
                                                            ->default('United States')
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                            ->live()
                                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('state', null)),

                                                        Forms\Components\Select::make('state')
                                                            ->label('State')
                                                            ->options(function (Forms\Get $get) {
                                                                $country = $get('country');
                                                                if (! $country) {
                                                                    return [];
                                                                }
                                                                $countryModel = \Lunar\Models\Country::where('name', $country)->first();
                                                                if (! $countryModel) {
                                                                    return [];
                                                                }

                                                                return \Lunar\Models\State::where('country_id', $countryModel->id)
                                                                    ->orderBy('name')
                                                                    ->pluck('name', 'name')
                                                                    ->toArray();
                                                            })
                                                            ->searchable()
                                                            ->required(),
                                                    ]),

                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('phone')
                                                            ->label('Phone')
                                                            ->tel()
                                                            ->maxLength(50),

                                                        Forms\Components\TextInput::make('toll_free_phone')
                                                            ->label('Toll-Free Phone')
                                                            ->tel()
                                                            ->maxLength(50),
                                                    ]),

                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('website')
                                                            ->label('Website')
                                                            ->url()
                                                            ->maxLength(255),

                                                        Forms\Components\TextInput::make('email')
                                                            ->label('Email')
                                                            ->email()
                                                            ->maxLength(255),
                                                    ]),

                                                    Forms\Components\Textarea::make('products_sold')
                                                        ->label('Products Sold')
                                                        ->rows(2),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    // ── Tab 5: Private Account Notes ──
                    Forms\Components\Tabs\Tab::make('Private Account Notes')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Private Account Notes')
                                ->helperText('Notes added here cannot be edited once saved. They will stay on the account permanently.')
                                ->rows(4)
                                ->disabled(fn (?Model $record): bool => filled($record?->admin_notes)),

                            Forms\Components\Textarea::make('notes')
                                ->label('Internal Admin Notes')
                                ->rows(3),
                        ]),
                ]),
        ]);
    }

    public function getRelations(array $managers): array
    {
        // Replace Lunar's default AddressRelationManager with ours
        $managers = array_filter($managers, fn ($manager) => $manager !== AddressRelationManager::class);

        // Add our custom one
        $managers[] = CustomerAddressRelationManager::class;

        return $managers;
    }

    public function extendTable(Table $table): Table
    {
        // Create impersonate action
        $impersonateAction = Tables\Actions\Action::make('impersonate')
            ->label('Impersonate')
            ->icon('heroicon-o-user-circle')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Impersonate Customer')
            ->modalDescription(fn ($record) => "You will be logged into the storefront as {$record->full_name} ({$record->email}). You can stop impersonating at any time using the banner at the top of the page.")
            ->modalSubmitActionLabel('Start Impersonating')
            ->url(fn ($record) => route('impersonate.start', $record))
            ->openUrlInNewTab();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => "{$record->last_name}, {$record->first_name} ({$record->email})")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('last_name', $direction)->orderBy('first_name', $direction))
                    ->url(fn ($record) => \Lunar\Admin\Filament\Resources\CustomerResource::getUrl('edit', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone'),

                Tables\Columns\TextColumn::make('customerGroups.name')
                    ->label('Group')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('n/j/Y g:i:s A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->actions([
                Tables\Actions\EditAction::make(),
                $impersonateAction,
            ])
            ->filters([
                // ── Row 1: Global keyword search (full width) ──
                Filter::make('keyword')
                    ->columnSpanFull()
                    ->form([
                        Forms\Components\TextInput::make('keyword')
                            ->label('Quick Search')
                            ->placeholder('Search name, email, phone, company, notes...')
                            ->prefixIcon('heroicon-o-magnifying-glass'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['keyword'],
                        fn (Builder $q, $value) => $q->where(function (Builder $sub) use ($value) {
                            $sub->where('first_name', 'like', "%{$value}%")
                                ->orWhere('last_name', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%")
                                ->orWhere('company_name', 'like', "%{$value}%")
                                ->orWhere('notes', 'like', "%{$value}%")
                                ->orWhere('admin_notes', 'like', "%{$value}%");
                        })
                    )),

                // ── Row 2: Identity fields ──
                Filter::make('customer_id')
                    ->form([
                        Forms\Components\TextInput::make('customer_id')
                            ->label('ID')
                            ->placeholder('Customer ID')
                            ->numeric(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['customer_id'],
                        fn (Builder $q, $value) => $q->where('id', $value)
                    )),

                Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Email address...'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['email'],
                        fn (Builder $q, $value) => $q->where('email', 'like', "%{$value}%")
                    )),

                Filter::make('first_name')
                    ->form([
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->placeholder('First name...'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['first_name'],
                        fn (Builder $q, $value) => $q->where('first_name', 'like', "%{$value}%")
                    )),

                Filter::make('last_name')
                    ->form([
                        Forms\Components\TextInput::make('last_name')
                            ->label('Last Name')
                            ->placeholder('Last name...'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['last_name'],
                        fn (Builder $q, $value) => $q->where('last_name', 'like', "%{$value}%")
                    )),

                // ── Row 3: Contact & Account ──
                Filter::make('phone')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->placeholder('Phone number...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['phone'], function (Builder $q, $value) {
                            $digits = preg_replace('/\D/', '', $value);

                            return $q->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$digits}%"]);
                        });
                    }),

                SelectFilter::make('customerGroups')
                    ->label('Group')
                    ->relationship('customerGroups', 'name')
                    ->preload()
                    ->searchable(),

                Filter::make('wholesale_account')
                    ->form([
                        Forms\Components\TextInput::make('wholesale_company')
                            ->label('Wholesale Account')
                            ->placeholder('Company name...'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['wholesale_company'],
                        fn (Builder $q, $value) => $q->where('is_online_wholesaler', true)
                            ->where('company_name', 'like', "%{$value}%")
                    )),

                // ── Row 4: Advanced ──
                Filter::make('last_order_before')
                    ->form([
                        Forms\Components\DatePicker::make('last_order_before')
                            ->label('Last Order Before')
                            ->native(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['last_order_before'],
                        fn (Builder $q, $value) => $q->whereNotNull('last_order_at')
                            ->where('last_order_at', '<', $value)
                    )),

                Filter::make('retailer')
                    ->form([
                        Forms\Components\TextInput::make('retailer_name')
                            ->label('Retailer')
                            ->placeholder('Retailer name...'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['retailer_name'],
                        fn (Builder $q, $value) => $q->whereHas('retailerProfile', function (Builder $rq) use ($value) {
                            $rq->where('name', 'like', "%{$value}%");
                        })
                    )),

                Filter::make('include_closed')
                    ->form([
                        Forms\Components\Toggle::make('include_closed')
                            ->label('Include Closed Accounts')
                            ->onColor('danger')
                            ->inline(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['include_closed'])) {
                            return $query->where(function (Builder $q) {
                                $q->where('account_locked', false)->orWhereNull('account_locked');
                            });
                        }

                        return $query;
                    })
                    ->default(true),
            ])
            ->filtersFormColumns(4)
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->deferFilters();
    }
}
