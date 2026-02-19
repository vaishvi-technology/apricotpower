<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Lunar\Admin\Support\Extending\ResourceExtension;

class StaffResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            ...$existing,

            Forms\Components\Section::make('Account Status')
                ->description('Control account access and visibility.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(5)->schema([
                        Forms\Components\Toggle::make('is_locked')
                            ->label('Locked')
                            ->helperText('Block login access'),

                        Forms\Components\Toggle::make('is_hidden')
                            ->label('Hidden')
                            ->helperText('Hide from admin listings'),

                        Forms\Components\Toggle::make('is_tester')
                            ->label('Tester Account')
                            ->helperText('Flag as test user'),

                        Forms\Components\Toggle::make('track_activity')
                            ->label('Track Activity')
                            ->helperText('Log admin actions'),

                        Forms\Components\Toggle::make('include_for_upsell')
                            ->label('Include for Upsell')
                            ->helperText('Include in sales rep assignments'),
                    ]),
                ]),

            Forms\Components\Section::make('Permissions')
                ->description('Toggle the areas this staff member can access and manage.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Toggle::make('perm_edit_staff')
                            ->label('Edit Staff')
                            ->helperText('Create, edit and delete staff'),

                        Forms\Components\Toggle::make('perm_edit_order_fulfillment')
                            ->label('Order Fulfillment')
                            ->helperText('Process and ship orders'),

                        Forms\Components\Toggle::make('perm_edit_order_accounts')
                            ->label('Order Accounts')
                            ->helperText('Manage order payment accounts'),

                        Forms\Components\Toggle::make('perm_edit_inventory')
                            ->label('Inventory & Items')
                            ->helperText('Add, edit and manage products'),

                        Forms\Components\Toggle::make('perm_edit_income_expenses')
                            ->label('Income & Expenses')
                            ->helperText('View financial reports'),

                        Forms\Components\Toggle::make('perm_view_order_totals')
                            ->label('View Order Totals')
                            ->helperText('View revenue totals'),

                        Forms\Components\Toggle::make('perm_edit_marketing')
                            ->label('Marketing & Promos')
                            ->helperText('Manage coupons and promotions'),

                        Forms\Components\Toggle::make('perm_edit_email_list')
                            ->label('Email List')
                            ->helperText('Manage email subscribers'),

                        Forms\Components\Toggle::make('perm_edit_other_admin')
                            ->label('Other Admin')
                            ->helperText('Miscellaneous admin settings'),

                        Forms\Components\Toggle::make('perm_edit_rep_settings')
                            ->label('Rep Settings')
                            ->helperText('Sales rep configuration'),

                        Forms\Components\Toggle::make('perm_edit_account_locked')
                            ->label('Lock/Unlock Accounts')
                            ->helperText('Lock or unlock customer accounts'),

                        Forms\Components\Toggle::make('perm_edit_credits')
                            ->label('Customer Credits')
                            ->helperText('Add, edit and remove customer credits'),
                    ]),
                ]),

            Forms\Components\Section::make('Commission Rates')
                ->description('Legacy wholesale and customer service commission percentages.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('percent_ws')
                            ->label('Wholesale % (PercentWS)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),

                        Forms\Components\TextInput::make('percent_cs')
                            ->label('Customer Service % (PercentCS)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),
                    ]),
                ]),
        ]);
    }
}
