<?php

namespace App\Lunar;

use App\Filament\RelationManagers\CustomerAddressRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Filament\Resources\CustomerResource\RelationManagers\AddressRelationManager;
use Lunar\Admin\Support\Extending\ResourceExtension;

class CustomerResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            ...$existing,

            Forms\Components\Tabs::make('Account Details')
                ->columnSpanFull()
                ->tabs([

                    Forms\Components\Tabs\Tab::make('Account Info')
                        ->icon('heroicon-o-user-circle')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(255),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->inline(false),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Placeholder::make('last_login_at_display')
                                    ->label('Last Login')
                                    ->content(fn ($record) => $record?->last_login_at?->format('M d, Y h:i A') ?? 'Never'),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Internal Admin Notes')
                                    ->rows(3),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Additional Info')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('is_tax_exempt')
                                    ->label('Tax Exempt')
                                    ->inline(false),

                                Forms\Components\TextInput::make('tax_exempt_certificate')
                                    ->label('Tax Exempt Certificate')
                                    ->maxLength(255),
                            ]),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Toggle::make('net_terms_approved')
                                    ->label('Net Terms Approved')
                                    ->inline(false),

                                Forms\Components\TextInput::make('credit_limit')
                                    ->label('Credit Limit')
                                    ->numeric()
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('current_balance')
                                    ->label('Current Balance')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
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
        return $table->columns([
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
        ]);
    }
}
