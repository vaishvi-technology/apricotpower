<?php

namespace App\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Country;
use Lunar\Models\State;

class CustomerAddressRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'Addresses';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function getAddressFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('type')
                    ->label('Address Type')
                    ->options([
                        'shipping' => 'Shipping',
                        'billing' => 'Billing',
                    ])
                    ->default('shipping')
                    ->required(),

                Forms\Components\TextInput::make('label')
                    ->label('Label')
                    ->placeholder('Home, Office, Warehouse...')
                    ->maxLength(255),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Toggle::make('shipping_default')
                    ->label('Default Shipping Address')
                    ->inline(false),

                Forms\Components\Toggle::make('billing_default')
                    ->label('Default Billing Address')
                    ->inline(false),
            ]),

            Forms\Components\Grid::make(5)->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->columnSpan(2),

                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->columnSpan(2),
            ]),

            Forms\Components\TextInput::make('company_name')
                ->label('Company'),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('line_one')
                    ->label('Address Line 1')
                    ->required(),

                Forms\Components\TextInput::make('line_two')
                    ->label('Address Line 2'),

                Forms\Components\TextInput::make('line_three')
                    ->label('Address Line 3'),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->options(fn () => Country::orderBy('name')->get()->mapWithKeys(fn ($c) => [$c->id => "{$c->emoji} {$c->name}"]))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('state', '')),

                Forms\Components\Select::make('state')
                    ->label('State / Province')
                    ->options(function (Forms\Get $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) {
                            return [];
                        }
                        return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'name')->toArray();
                    })
                    ->searchable()
                    ->required(),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->required(),

                Forms\Components\TextInput::make('postcode')
                    ->label('ZIP / Postal Code')
                    ->required(),
            ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('contact_phone')
                    ->label('Phone')
                    ->tel(),

                Forms\Components\TextInput::make('contact_email')
                    ->label('Email')
                    ->email(),
            ]),

            Forms\Components\Textarea::make('delivery_instructions')
                ->label('Delivery Instructions')
                ->rows(2),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Addresses')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'shipping' => 'success',
                        'billing' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}"),

                Tables\Columns\TextColumn::make('line_one')
                    ->label('Address')
                    ->description(fn (Model $record) => implode(', ', array_filter([
                        $record->city,
                        $record->state,
                        $record->postcode,
                    ]))),

                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Phone'),

                Tables\Columns\IconColumn::make('shipping_default')
                    ->label('Ship Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('billing_default')
                    ->label('Bill Default')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Address')
                    ->form($this->getAddressFormSchema())
                    ->hidden(fn (): bool => $this->getOwnerRecord()->addresses()->count() >= 10)
                    ->before(function (Tables\Actions\CreateAction $action) {
                        if ($this->getOwnerRecord()->addresses()->count() >= 10) {
                            Notification::make()
                                ->danger()
                                ->title('Address limit reached')
                                ->body('You can save up to 10 addresses. Please delete an existing one to add more.')
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getAddressFormSchema()),

                Tables\Actions\Action::make('setShippingDefault')
                    ->label('Set as Primary Shipping')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->hidden(fn (Model $record): bool => (bool) $record->shipping_default)
                    ->requiresConfirmation()
                    ->action(function (Model $record) {
                        // Unset all other shipping defaults for this customer
                        $this->getOwnerRecord()->addresses()->update(['shipping_default' => false]);
                        $record->update(['shipping_default' => true]);

                        Notification::make()
                            ->success()
                            ->title('Primary shipping address updated')
                            ->send();
                    }),

                Tables\Actions\Action::make('setBillingDefault')
                    ->label('Set as Primary Billing')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->hidden(fn (Model $record): bool => (bool) $record->billing_default)
                    ->requiresConfirmation()
                    ->action(function (Model $record) {
                        // Unset all other billing defaults for this customer
                        $this->getOwnerRecord()->addresses()->update(['billing_default' => false]);
                        $record->update(['billing_default' => true]);

                        Notification::make()
                            ->success()
                            ->title('Primary billing address updated')
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
