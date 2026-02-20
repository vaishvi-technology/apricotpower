<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;
use Lunar\Admin\Support\Facades\LunarPanel;
use Spatie\Permission\Models\Role;

class StaffResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        // Separate named fields from the role/permissions container (Grid)
        $basicFields = [];
        $otherComponents = [];

        foreach ($existing as $component) {
            if ($component instanceof Forms\Components\TextInput || $component instanceof Forms\Components\Toggle) {
                $basicFields[] = $component;
            } else {
                $otherComponents[] = $component;
            }
        }

        return $form->schema([
            Forms\Components\Tabs::make('Staff Details')
                ->columnSpanFull()
                ->tabs([

                    Forms\Components\Tabs\Tab::make('General')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Section::make('Personal Information')
                                ->icon('heroicon-o-identification')
                                ->schema($basicFields)
                                ->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Role & Access')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Forms\Components\Section::make('Roles')
                                ->icon('heroicon-o-user-group')
                                ->description('Assign roles to control what this staff member can access.')
                                ->schema([
                                    Forms\Components\Select::make('roles')
                                        ->label('Assigned Roles')
                                        ->multiple()
                                        ->options(fn () => Role::where('guard_name', LunarPanel::getPanel()->getAuthGuard())
                                            ->pluck('name', 'name')
                                            ->toArray())
                                        ->afterStateHydrated(fn (Forms\Components\Select $component, $record) => $component->state($record?->getRoleNames()->toArray() ?? []))
                                        ->saveRelationshipsUsing(fn ($state, $record) => $record->syncRoles($state))
                                        ->dehydrated(false),
                                ]),

                            Forms\Components\Section::make('Account Status')
                                ->icon('heroicon-o-lock-closed')
                                ->schema([
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Active')
                                        ->helperText('Inactive staff members cannot log in to the admin panel.')
                                        ->default(true)
                                        ->inline(false),
                                ]),

                            // Lunar's built-in role/permissions container (hidden for super admins)
                            ...$otherComponents,
                        ]),
                ]),
        ]);
    }

    public function extendTable(Table $table): Table
    {
        return $table->columns([
            ...$table->getColumns(),

            Tables\Columns\TextColumn::make('roles.name')
                ->label('Roles')
                ->badge()
                ->toggleable(),

            Tables\Columns\IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->toggleable(),
        ]);
    }
}
