<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class StaffResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            ...$existing,

            Forms\Components\Section::make('Role & Status')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'staff' => 'Staff',
                            ])
                            ->default('staff')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
                ]),
        ]);
    }

    public function extendTable(Table $table): Table
    {
        return $table->columns([
            ...$table->getColumns(),

            Tables\Columns\TextColumn::make('role')
                ->label('Role')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'admin' => 'danger',
                    'manager' => 'warning',
                    default => 'gray',
                })
                ->toggleable(),

            Tables\Columns\IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->toggleable(),
        ]);
    }
}
