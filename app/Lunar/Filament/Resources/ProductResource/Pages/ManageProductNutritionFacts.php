<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\NutrientResource;
use App\Models\Nutrient;
use App\Models\ProductNutrient;
use App\Models\ProductNutritionFact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;

class ManageProductNutritionFacts extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    // Form state properties
    public bool $is_enabled = false;
    public bool $ingredients_enabled = false;
    public ?string $label_type = 'nutrition';
    public ?string $serving_size = null;
    public ?string $servings_per_container = null;
    public ?string $calories_per_serving = null;
    public ?string $calories_from_fat = null;
    public ?string $ingredients = null;
    public array $nutrients = [];
    public ?int $selected_nutrient_id = null;

    public function getTitle(): string|Htmlable
    {
        return 'Nutrition Facts';
    }

    public static function getNavigationLabel(): string
    {
        return 'Nutrition Facts';
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }

    public function getBreadcrumb(): string
    {
        return 'Nutrition Facts';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-beaker';
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $nutritionFact = $this->getRecord()->nutritionFact;

        if ($nutritionFact) {
            $this->is_enabled = $nutritionFact->is_enabled;
            $this->ingredients_enabled = $nutritionFact->ingredients_enabled;
            $this->label_type = $nutritionFact->label_type;
            $this->serving_size = $nutritionFact->serving_size;
            $this->servings_per_container = $nutritionFact->servings_per_container;
            $this->calories_per_serving = $nutritionFact->calories_per_serving;
            $this->calories_from_fat = $nutritionFact->calories_from_fat;
            $this->ingredients = $nutritionFact->ingredients;

            // Load existing nutrients
            $this->nutrients = $nutritionFact->productNutrients()
                ->with('nutrient')
                ->get()
                ->map(fn($pn) => [
                    'id' => $pn->id,
                    'nutrient_id' => $pn->nutrient_id,
                    'nutrient_name' => $pn->nutrient->display_title ?? $pn->nutrient->name,
                    'amount_per_serving' => $pn->amount_per_serving,
                    'percent_daily_value' => $pn->percent_daily_value,
                    'not_established' => $pn->not_established,
                ])
                ->toArray();
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Get or create nutrition fact
        $nutritionFact = $record->nutritionFact ?? new ProductNutritionFact();
        $nutritionFact->product_id = $record->id;
        $nutritionFact->is_enabled = $this->is_enabled;
        $nutritionFact->ingredients_enabled = $this->ingredients_enabled;
        $nutritionFact->label_type = $this->label_type;
        $nutritionFact->serving_size = $this->serving_size;
        $nutritionFact->servings_per_container = $this->servings_per_container;
        $nutritionFact->calories_per_serving = $this->calories_per_serving;
        $nutritionFact->calories_from_fat = $this->calories_from_fat;
        $nutritionFact->ingredients = $this->ingredients;
        $nutritionFact->save();

        // Sync nutrients
        $existingIds = [];
        foreach ($this->nutrients as $nutrientData) {
            if (isset($nutrientData['id'])) {
                $productNutrient = ProductNutrient::find($nutrientData['id']);
                if ($productNutrient) {
                    $productNutrient->update([
                        'amount_per_serving' => $nutrientData['amount_per_serving'] ?? null,
                        'percent_daily_value' => $nutrientData['percent_daily_value'] ?? null,
                        'not_established' => $nutrientData['not_established'] ?? false,
                    ]);
                    $existingIds[] = $productNutrient->id;
                }
            } else {
                $productNutrient = ProductNutrient::create([
                    'nutrition_fact_id' => $nutritionFact->id,
                    'nutrient_id' => $nutrientData['nutrient_id'],
                    'amount_per_serving' => $nutrientData['amount_per_serving'] ?? null,
                    'percent_daily_value' => $nutrientData['percent_daily_value'] ?? null,
                    'not_established' => $nutrientData['not_established'] ?? false,
                ]);
                $existingIds[] = $productNutrient->id;
            }
        }

        // Delete removed nutrients
        ProductNutrient::where('nutrition_fact_id', $nutritionFact->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        return $record;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function getDefaultForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('is_enabled')
                                ->label('Show Nutrition Label')
                                ->live(),
                            Forms\Components\Select::make('label_type')
                                ->label('Label Type')
                                ->options([
                                    'nutrition' => 'Nutrition Facts',
                                    'supplement' => 'Supplement Facts',
                                ])
                                ->default('nutrition'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('ingredients_enabled')
                                ->label('Show Ingredients'),
                        ]),
                    ]),

                Forms\Components\Section::make('Serving Information')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('serving_size')
                                ->label('Serving Size')
                                ->placeholder('e.g., 3 seeds'),
                            Forms\Components\TextInput::make('servings_per_container')
                                ->label('Servings Per Size')
                                ->placeholder('e.g., apx. 125'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('calories_per_serving')
                                ->label('Calories Per Serving')
                                ->placeholder('e.g., 10'),
                            Forms\Components\TextInput::make('calories_from_fat')
                                ->label('Calories From Fat')
                                ->placeholder('e.g., 10'),
                        ]),
                    ]),

                Forms\Components\Section::make('Nutrients')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('manage_nutrients')
                                ->label('Edit Line Item Options')
                                ->icon('heroicon-o-cog-6-tooth')
                                ->url(fn () => NutrientResource::getUrl('index'))
                                ->openUrlInNewTab(),
                        ]),

                        Forms\Components\Grid::make(4)->schema([
                            Forms\Components\Select::make('selected_nutrient_id')
                                ->label('Select Nutrient')
                                ->options(fn () => Nutrient::active()->ordered()->pluck('display_title', 'id'))
                                ->searchable()
                                ->preload()
                                ->columnSpan(3),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('add_nutrient')
                                    ->label('Add')
                                    ->icon('heroicon-o-plus')
                                    ->action(function () {
                                        if ($this->selected_nutrient_id) {
                                            // Check if already added
                                            $exists = collect($this->nutrients)->firstWhere('nutrient_id', $this->selected_nutrient_id);
                                            if (!$exists) {
                                                $nutrient = Nutrient::find($this->selected_nutrient_id);
                                                $this->nutrients[] = [
                                                    'nutrient_id' => $nutrient->id,
                                                    'nutrient_name' => $nutrient->display_title ?? $nutrient->name,
                                                    'amount_per_serving' => '',
                                                    'percent_daily_value' => null,
                                                    'not_established' => false,
                                                ];
                                            }
                                            $this->selected_nutrient_id = null;
                                        }
                                    }),
                            ])->columnSpan(1),
                        ]),

                        Forms\Components\Repeater::make('nutrients')
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\Hidden::make('nutrient_id'),
                                Forms\Components\TextInput::make('nutrient_name')
                                    ->label('Nutrient')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('amount_per_serving')
                                    ->label('Amount/Serving')
                                    ->placeholder('e.g., 60mg'),
                                Forms\Components\TextInput::make('percent_daily_value')
                                    ->label('Daily % Value')
                                    ->numeric()
                                    ->placeholder('e.g., 4'),
                                Forms\Components\Toggle::make('not_established')
                                    ->label('Not Established')
                                    ->inline(),
                            ])
                            ->columns(4)
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(true)
                            ->itemLabel(fn (array $state): ?string => $state['nutrient_name'] ?? null),
                    ]),

                Forms\Components\Section::make('Ingredients')
                    ->schema([
                        Forms\Components\Textarea::make('ingredients')
                            ->label('Ingredients List')
                            ->rows(4)
                            ->placeholder('Enter ingredients separated by commas'),
                    ]),

                Forms\Components\Section::make('Preview')
                    ->schema([
                        Forms\Components\View::make('components.nutrition-label-preview')
                            ->viewData([
                                'getNutritionData' => fn () => $this->getNutritionPreviewData(),
                            ]),
                    ])
                    ->collapsed(),
            ])
            ->statePath('');
    }

    public function getNutritionPreviewData(): array
    {
        return [
            'label_type' => $this->label_type,
            'serving_size' => $this->serving_size,
            'servings_per_container' => $this->servings_per_container,
            'calories_per_serving' => $this->calories_per_serving,
            'calories_from_fat' => $this->calories_from_fat,
            'nutrients' => $this->nutrients,
            'ingredients' => $this->ingredients,
            'is_enabled' => $this->is_enabled,
            'ingredients_enabled' => $this->ingredients_enabled,
        ];
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
