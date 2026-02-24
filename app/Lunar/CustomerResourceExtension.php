<?php

namespace App\Lunar;

use App\Filament\RelationManagers\CustomerAddressRelationManager;
use App\Models\CustomerGroup;
use App\Services\ImpersonationService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
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
                            Forms\Components\Grid::make(5)->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title')
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
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

                                Forms\Components\TextInput::make('alt_phone')
                                    ->label('Alt Phone')
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
                                            ->live(debounce: 500),

                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->label('Confirm New Password')
                                            ->password()
                                            ->revealable()
                                            ->same('password')
                                            ->requiredWith('password')
                                            ->dehydrated(false),
                                    ]),
                                ]),
                        ]),

                    // ── Tab 2: Account Settings ──
                    Forms\Components\Tabs\Tab::make('Account Settings')
                        ->icon('heroicon-o-cog-6-tooth')
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

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false),

                                Forms\Components\Toggle::make('account_locked')
                                    ->label('Account Locked / Closed')
                                    ->helperText('Prevents signing in and hides from searches.')
                                    ->onColor('danger')
                                    ->offColor('success')
                                    ->inline(false),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('account_ref')
                                    ->label('Account Reference')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('tax_identifier')
                                    ->label('Tax Identifier')
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Grid::make(4)->schema([
                                Forms\Components\Toggle::make('is_tax_exempt')
                                    ->label('Tax Exempt')
                                    ->onColor('success')
                                    ->inline(false),

                                Forms\Components\TextInput::make('tax_exempt_certificate')
                                    ->label('Tax Exempt Certificate')
                                    ->maxLength(255),
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

                    // ── Tab 3: Account Details ──
                    Forms\Components\Tabs\Tab::make('Account Details')
                        ->icon('heroicon-o-clipboard-document-list')
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

                    // ── Tab 4: Wholesale / Billing (visible only for wholesale group) ──
                    Forms\Components\Tabs\Tab::make('Wholesale / Billing')
                        ->icon('heroicon-o-building-storefront')
                        ->visible(function (Forms\Get $get, ?Model $record): bool {
                            $selectedGroupIds = $get('customerGroups') ?? [];
                            if (!empty($selectedGroupIds)) {
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
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('net_terms_approved')
                                    ->label('Approved for Net 30 Terms')
                                    ->onColor('success')
                                    ->inline(false),

                                Forms\Components\TextInput::make('credit_limit')
                                    ->label('Net 30 Credit Limit')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),

                            Forms\Components\Group::make()
                                ->relationship('retailerProfile')
                                ->schema([
                                    Forms\Components\TextInput::make('accounts_payable_email')
                                        ->label('Accounts Payable Email')
                                        ->email()
                                        ->maxLength(255),

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
                                                    ->searchable(),
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
                ...$table->getColumns(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('account_locked')
                    ->label('Locked')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ...$table->getActions(),
                $impersonateAction,
            ])
            ->filters([
                TernaryFilter::make('account_locked')
                    ->label('Closed Accounts')
                    ->placeholder('Hide Closed')
                    ->trueLabel('Show All')
                    ->falseLabel('Only Closed')
                    ->queries(
                        true: fn (Builder $query) => $query,
                        false: fn (Builder $query) => $query->where('account_locked', true),
                        blank: fn (Builder $query) => $query->where('account_locked', false),
                    ),
            ]);
    }
}
