<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class CustomerResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            ...$existing,

            Forms\Components\Section::make('Contact Info')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('alt_phone')
                            ->label('Alt Phone')
                            ->tel()
                            ->maxLength(50),
                    ]),
                ]),

            Forms\Components\Section::make('Account Status')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active'),

                        Forms\Components\Toggle::make('account_locked')
                            ->label('Account Locked'),

                        Forms\Components\Toggle::make('subscribe_to_list')
                            ->label('Subscribed to Mailing List'),
                    ]),
                ]),

            Forms\Components\Section::make('B2B / Wholesale')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_tax_exempt')
                            ->label('Tax Exempt'),

                        Forms\Components\TextInput::make('tax_exempt_certificate')
                            ->label('Tax Exempt Certificate')
                            ->maxLength(255),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('net_terms_approved')
                            ->label('Net Terms Approved'),

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

            Forms\Components\Section::make('VIP / Loyalty')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_vip')
                            ->label('VIP Customer'),

                        Forms\Components\TextInput::make('referred_by')
                            ->label('Referred By')
                            ->maxLength(75),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('vip_since')
                            ->label('VIP Since'),

                        Forms\Components\DatePicker::make('vip_expire')
                            ->label('VIP Expires'),
                    ]),
                ]),

            Forms\Components\Section::make('Retailer')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_retailer')
                            ->label('Retailer'),

                        Forms\Components\Toggle::make('is_online_retailer')
                            ->label('Online Retailer'),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('store_count')
                            ->label('Store Count')
                            ->numeric(),

                        Forms\Components\TextInput::make('sales_rep_id')
                            ->label('Sales Rep ID')
                            ->numeric(),
                    ]),
                ]),

            Forms\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),

                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Admin Notes')
                        ->rows(3),

                    Forms\Components\Textarea::make('extra_emails')
                        ->label('Extra Emails')
                        ->rows(2),
                ]),

            Forms\Components\Section::make('Tracking')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('Last Login')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('last_order_at')
                            ->label('Last Order')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('agreed_terms_at')
                            ->label('Agreed to Terms')
                            ->disabled(),
                    ]),
                ]),
        ]);
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

            Tables\Columns\IconColumn::make('is_vip')
                ->label('VIP')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make('is_retailer')
                ->label('Retailer')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
    }
}
