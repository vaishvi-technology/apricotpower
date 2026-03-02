<?php

use App\Livewire\CheckoutPage;
use App\Livewire\CheckoutSuccessPage;
use App\Livewire\CollectionPage;
use App\Livewire\Home;
use App\Livewire\ProductPage;
use App\Livewire\SearchPage;
use App\Livewire\CartPage;
use App\Livewire\StorePage;
use App\Livewire\CategoryPage;
use App\Livewire\RetailerLocationsPage;
use App\Livewire\BlogsPage;
use App\Livewire\BlogDetailPage;
use App\Livewire\ContactPage;
use App\Livewire\ReviewsPage;
use App\Livewire\TestimonialPage;
use App\Livewire\WholesaleApplicationPage;
use App\Livewire\ReferAFriendPage;
use App\Livewire\FaqPage;
use App\Livewire\SeedsRecipesPage;
use App\Livewire\SuperfoodRecipesPage;
use App\Livewire\LifeStylePage;
use App\Livewire\VideoLibraryPage;
use App\Livewire\PrivacyPage;
use App\Livewire\ReturnPolicyPage;
use App\Livewire\ShippingPolicyPage;
use App\Livewire\AboutPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Account\OrderHistoryPage;
use App\Livewire\Account\BasicInfoPage;
use App\Livewire\Account\AccountDetailsPage;
use App\Livewire\Account\EmailPreferencesPage;
use App\Livewire\Account\Admin\PrivateAccountNotesPage;
use App\Http\Controllers\ImpersonationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home
Route::get('/', Home::class)->name('home');

// Store & Products
Route::get('/store', StorePage::class)->name('store');
Route::get('/category/{slug}', CategoryPage::class)->name('category.view');
Route::get('/collections/{slug}', CollectionPage::class)->name('collection.view');
Route::get('/products/{slug}', ProductPage::class)->name('product.view');
Route::get('/item/{slug}', ProductPage::class)->name('item.view');
Route::get('/search', SearchPage::class)->name('search.view');

// Cart & Checkout
Route::get('/cart', CartPage::class)->name('cart.view');
Route::get('/checkout', CheckoutPage::class)->name('checkout.view');
Route::get('/checkout/success', CheckoutSuccessPage::class)->name('checkout-success.view');

// Retail Locations
Route::get('/retailer-locations', RetailerLocationsPage::class)->name('retailer-locations');

// Blogs & Content
Route::get('/blogs', BlogsPage::class)->name('blogs');
Route::get('/blogs/{slug}', BlogDetailPage::class)->name('blog.detail');
Route::get('/videos', VideoLibraryPage::class)->name('videos');
Route::get('/life-style', LifeStylePage::class)->name('lifestyle');
Route::get('/seeds-recipes', SeedsRecipesPage::class)->name('seeds-recipes');
Route::get('/recipes-superfood', SuperfoodRecipesPage::class)->name('superfood-recipes');

// About & Company
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact-us', ContactPage::class)->name('contact');
Route::get('/reviews', ReviewsPage::class)->name('reviews');
Route::get('/testimonial', TestimonialPage::class)->name('testimonial');
Route::get('/wholesale-application', WholesaleApplicationPage::class)->name('wholesale');
Route::get('/refer-a-friend', ReferAFriendPage::class)->name('refer-friend');
Route::get('/faq', FaqPage::class)->name('faq');

// Policy Pages
Route::get('/privacy', PrivacyPage::class)->name('privacy');
Route::get('/return-policy', ReturnPolicyPage::class)->name('return-policy');
Route::get('/shipping-policy', ShippingPolicyPage::class)->name('shipping-policy');

// Authentication (Guest only – customer guard)
Route::middleware(['guest:customer'])->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
});

Route::post('/logout', function () {
    auth('customer')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Account (Protected – customer guard)
Route::middleware(['auth:customer'])->group(function () {
    Route::get('/order-history', OrderHistoryPage::class)->name('order-history.view');
    Route::get('/order-history/{id}', OrderHistoryPage::class)->name('order-history.detail');
    Route::get('/basic-info', BasicInfoPage::class)->name('basic-info');
    Route::get('/account-details', AccountDetailsPage::class)->name('account-details');
    Route::get('/email-preferences', EmailPreferencesPage::class)->name('email-preferences');

    // Admin Options (only accessible during impersonation — enforced in each component)
    Route::prefix('admin-options')->group(function () {
        Route::get('/private-notes', PrivateAccountNotesPage::class)->name('admin.private-notes');
    });
});

// Impersonation Routes (Admin only – staff guard)
Route::middleware(['auth:staff'])->group(function () {
    Route::get('/impersonate/customer/{customer}', [ImpersonationController::class, 'start'])
        ->name('impersonate.start');
});

Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])
    ->name('impersonate.stop');
