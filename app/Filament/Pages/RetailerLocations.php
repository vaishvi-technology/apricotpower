<?php

namespace App\Filament\Pages;

use App\Models\RetailerProfile;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Lunar\Admin\Filament\Resources\CustomerResource;

class RetailerLocations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'Retailer Locations';

    protected static ?string $title = 'Retailer Locations';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.retailer-locations';

    protected function getRetailerFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
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
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addRetailerLocation')
                ->label('Add Retailer Location')
                ->icon('heroicon-o-plus')
                ->form(fn () => $this->getRetailerFormSchema())
                ->action(function (array $data): void {
                    RetailerProfile::create([
                        ...$data,
                        'include_in_retailer_map' => true,
                    ]);

                    Notification::make()
                        ->title('Retailer location created')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl());
                }),

        ];
    }

    public function editRetailerLocationAction(): Action
    {
        return Action::make('editRetailerLocation')
            ->modalHeading('Edit Retailer Location')
            ->form(function (array $arguments): array {
                $retailer = RetailerProfile::with('customer')->findOrFail($arguments['id']);
                $fields = $this->getRetailerFormSchema();

                if ($retailer->customer) {
                    $customer = $retailer->customer;
                    $label = trim($customer->first_name . ' ' . $customer->last_name);
                    if ($customer->email) {
                        $label .= ' (' . $customer->email . ')';
                    }
                    $customerUrl = CustomerResource::getUrl('edit', ['record' => $customer->id]);

                    $fields[] = Forms\Components\Placeholder::make('associated_account')
                        ->label('Associated Account')
                        ->content(new HtmlString(
                            '<a href="' . $customerUrl . '" target="_blank" class="text-primary-600 hover:underline">' . e($label) . '</a>'
                            . ' - <button type="button" wire:click="removeCustomerAssociation(' . $retailer->id . ')" wire:confirm="Are you sure you want to remove this customer association?" class="text-danger-600 hover:underline text-sm">remove</button>'
                        ));
                }

                return $fields;
            })
            ->fillForm(function (array $arguments): array {
                $retailer = RetailerProfile::findOrFail($arguments['id']);

                return $retailer->only([
                    'name', 'street', 'city', 'country', 'state',
                    'phone', 'toll_free_phone', 'website', 'email', 'products_sold',
                ]);
            })
            ->action(function (array $data, array $arguments): void {
                $retailer = RetailerProfile::findOrFail($arguments['id']);
                $retailer->update($data);

                Notification::make()
                    ->title('Retailer location updated')
                    ->success()
                    ->send();

                $this->redirect(static::getUrl());
            });
    }

    public function deleteRetailerLocationAction(): Action
    {
        return Action::make('deleteRetailerLocation')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Retailer Location')
            ->modalDescription('Are you sure you want to delete this retailer location? This action cannot be undone.')
            ->action(function (array $arguments): void {
                $retailer = RetailerProfile::findOrFail($arguments['id']);
                $retailer->delete();

                Notification::make()
                    ->title('Retailer location deleted')
                    ->success()
                    ->send();

                $this->redirect(static::getUrl());
            });
    }

    public function removeCustomerAssociation(int $retailerId): void
    {
        $retailer = RetailerProfile::findOrFail($retailerId);
        $retailer->update(['customer_id' => null]);

        Notification::make()
            ->title('Customer association removed')
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }

    public string $filterState = '';

    public string $filterCountry = '';

    public int $listPerPage = 15;

    public int $listPage = 1;

    public bool $listExpanded = false;

    public string $listSearch = '';

    public function searchList(): void
    {
        $this->listPage = 1;
    }

    public function resetListSearch(): void
    {
        $this->listSearch = '';
        $this->listPage = 1;
    }

    public function loadMore(): void
    {
        $this->listPage++;
    }

    public function toggleList(): void
    {
        $this->listExpanded = ! $this->listExpanded;
    }

    public function getRetailerListProperty(): array
    {
        $query = RetailerProfile::query()
            ->where('include_in_retailer_map', true)
            ->when($this->listSearch !== '', fn ($q) => $q->where('name', 'like', '%' . $this->listSearch . '%'))
            ->orderBy('name');

        $total = $query->count();
        $items = $query->limit($this->listPage * $this->listPerPage)->get();

        return [
            'items' => $items,
            'total' => $total,
            'hasMore' => $items->count() < $total,
        ];
    }

    public function getRetailersProperty(): Collection
    {
        return RetailerProfile::query()
            ->where('include_in_retailer_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
    }

    public function getStatesProperty(): array
    {
        return RetailerProfile::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();
    }

    public function getCountriesProperty(): array
    {
        return RetailerProfile::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }

    public function getRetailerDataProperty(): array
    {
        return $this->retailers->map(fn (RetailerProfile $profile) => [
            'id' => $profile->id,
            'name' => $profile->name ?? 'Unnamed Store',
            'street' => $profile->street,
            'city' => $profile->city,
            'state' => $profile->state,
            'country' => $profile->country,
            'phone' => $profile->phone,
            'website' => $profile->website,
            'latitude' => $profile->latitude,
            'longitude' => $profile->longitude,
        ])->toArray();
    }

    protected function getViewData(): array
    {
        return [
            'retailers' => $this->retailerData,
            'states' => $this->states,
            'countries' => $this->countries,
            'googleMapsApiKey' => config('services.google_maps.key'),
            'retailerList' => $this->retailerList,
        ];
    }
}
