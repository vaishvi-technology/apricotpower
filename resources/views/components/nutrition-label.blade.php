@props([
    'nutritionFact' => null,
    'showLabel' => true,
])

@php
    $labelType = $nutritionFact?->label_type ?? 'nutrition';
    $labelTitle = $labelType === 'supplement' ? 'Supplement Facts' : 'Nutrition Facts';
    $isEnabled = $nutritionFact?->is_enabled ?? false;
    $ingredientsEnabled = $nutritionFact?->ingredients_enabled ?? false;
    $servingSize = $nutritionFact?->serving_size ?? '';
    $servingsPerContainer = $nutritionFact?->servings_per_container ?? '';
    $caloriesPerServing = $nutritionFact?->calories_per_serving ?? '';
    $caloriesFromFat = $nutritionFact?->calories_from_fat ?? '';
    $ingredients = $nutritionFact?->ingredients ?? '';
    $nutrients = $nutritionFact?->productNutrients ?? collect();
@endphp

@if($showLabel && $isEnabled)
<div class="nutrition-label">
    <h2 class="nutrition-title">{{ $labelTitle }}</h2>

    @if($servingsPerContainer)
    <div class="servings">{{ $servingsPerContainer }} servings per container</div>
    @endif

    @if($servingSize)
    <div class="serving-size">
        <span>Serving Size</span>
        <span>{{ $servingSize }}</span>
    </div>
    @endif

    <div class="thick-bar"></div>

    <div class="amount-header">Amount per serving</div>

    @if($caloriesPerServing)
    <div class="calories-row">
        <span>Calories</span>
        <span>{{ $caloriesPerServing }}</span>
    </div>
    @endif

    @if($caloriesFromFat)
    <div class="calories-fat-row">
        <span>Calories From Fat</span>
        <span>{{ $caloriesFromFat }}</span>
    </div>
    @endif

    <div class="medium-bar"></div>

    <div class="daily-value-header">% Daily Value*</div>

    @foreach($nutrients->sortBy(fn($pn) => $pn->nutrient->rank ?? 999) as $productNutrient)
        @php
            $nutrient = $productNutrient->nutrient;
            $displayClass = $nutrient->display_class ?? '';
            $isBold = $displayClass === 'bold';
            $isIndented = str_contains($displayClass, 'indent') || str_contains($nutrient->display_title ?? '', '&nbsp;');
        @endphp
        <div class="nutrient-row {{ $isBold ? 'bold' : '' }} {{ $isIndented ? 'indented' : '' }}">
            <span class="nutrient-name">
                {{ trim(html_entity_decode($nutrient->display_title ?? $nutrient->name)) }}
                @if($productNutrient->amount_per_serving)
                    {{ $productNutrient->amount_per_serving }}
                @endif
            </span>
            <span class="nutrient-value">
                @if($productNutrient->not_established)
                    &dagger;
                @elseif($productNutrient->percent_daily_value !== null)
                    {{ $productNutrient->percent_daily_value }}%
                @endif
            </span>
        </div>
    @endforeach

    <div class="thin-bar"></div>

    <div class="footnote">
        @if($nutrients->where('not_established', true)->count() > 0)
            <p>&dagger; Daily Value not established.</p>
        @endif
        <p>*Percent Daily Values are based on a 2,000 calorie diet.</p>
    </div>
</div>

@if($ingredientsEnabled && $ingredients)
<div class="ingredients-section">
    <h3>Ingredients</h3>
    <p>{{ $ingredients }}</p>
</div>
@endif

<style>
.nutrition-label {
    border: 2px solid #000;
    padding: 10px;
    max-width: 400px;
    font-family: Arial, Helvetica, sans-serif;
    background: #fff;
}
.nutrition-label .nutrition-title {
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 5px 0;
    line-height: 1;
}
.nutrition-label .servings {
    font-size: 0.9rem;
}
.nutrition-label .serving-size {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    font-size: 1rem;
    padding: 3px 0;
}
.nutrition-label .thick-bar {
    background: #000;
    height: 10px;
    margin: 5px 0;
}
.nutrition-label .medium-bar {
    background: #000;
    height: 5px;
    margin: 3px 0;
}
.nutrition-label .thin-bar {
    border-top: 1px solid #000;
    margin: 3px 0;
}
.nutrition-label .amount-header {
    font-size: 0.85rem;
    font-weight: bold;
}
.nutrition-label .calories-row {
    display: flex;
    justify-content: space-between;
    font-size: 1.5rem;
    font-weight: bold;
    padding: 5px 0;
}
.nutrition-label .calories-fat-row {
    display: flex;
    justify-content: space-between;
    font-size: 1rem;
    padding: 3px 0;
}
.nutrition-label .daily-value-header {
    text-align: right;
    font-weight: bold;
    font-size: 0.85rem;
    padding: 3px 0;
}
.nutrition-label .nutrient-row {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #000;
    padding: 3px 0;
    font-size: 0.9rem;
}
.nutrition-label .nutrient-row.bold {
    font-weight: bold;
}
.nutrition-label .nutrient-row.indented .nutrient-name {
    padding-left: 20px;
}
.nutrition-label .footnote {
    font-size: 0.75rem;
    margin-top: 8px;
    line-height: 1.3;
}
.nutrition-label .footnote p {
    margin: 2px 0;
}
.ingredients-section {
    margin-top: 20px;
    max-width: 400px;
}
.ingredients-section h3 {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 5px;
    border-bottom: 2px solid #000;
    padding-bottom: 3px;
}
.ingredients-section p {
    font-size: 0.9rem;
    line-height: 1.4;
}
</style>
@endif
