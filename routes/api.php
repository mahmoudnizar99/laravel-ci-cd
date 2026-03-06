<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Admin\CompanyQuestionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FixedOrderController;
use App\Http\Controllers\Admin\JobOrderController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Company\QuestionController;
use App\Http\Controllers\Company\ProfileController;
use App\Http\Controllers\Company\OrderController as CompanyOrderController;
use App\Http\Controllers\Company\JobController;
use App\Http\Controllers\Company\RatingController;
use App\Http\Controllers\Company\ProductController;
use App\Http\Controllers\Public\CompanyController as PublicCompanyController;
use App\Http\Controllers\Public\EmergencyOrderController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\JobController as UserJobController;
use App\Http\Controllers\User\ProductOrderController;
use App\Http\Controllers\User\RatingController as UserRatingController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\CommentController;

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/login', function() {
    return response()->json(['message' => 'Please use POST to login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Admin 
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Company Questions
    Route::apiResource('questions', CompanyQuestionController::class);
    
    // Users
    Route::apiResource('users', UserController::class);
    
    // Fixed Orders
    Route::get('fixed-orders', [FixedOrderController::class, 'index']);
    Route::delete('fixed-orders/{id}', [FixedOrderController::class, 'destroy']);
    
    // Job Orders
    Route::get('job-orders', [JobOrderController::class, 'index']);
    
    // Companies
    Route::get('companies', [CompanyController::class, 'index']);
    Route::put('companies/{id}/activate', [CompanyController::class, 'activate']);
});

// Company
Route::middleware(['auth:sanctum', 'company'])->prefix('company')->group(function () {
    // Questions & Answers
    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('answers', [QuestionController::class, 'store']);
    
    // Profile
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    
    // Orders
    Route::get('orders', [CompanyOrderController::class, 'index']);
    Route::put('orders/{id}', [CompanyOrderController::class, 'update']);
    
    // Jobs
    Route::apiResource('jobs', JobController::class);
    Route::get('jobs/{id}/applications', [JobController::class, 'applications']);
    
    // Ratings
    Route::get('ratings', [RatingController::class, 'index']);
    Route::get('ratings/average', [RatingController::class, 'average']);
    
    // Products
    Route::apiResource('products', ProductController::class);
});

// Public
Route::prefix('public')->group(function () {
    // Companies
    Route::get('companies', [PublicCompanyController::class, 'index']);
    
    // Emergency Order
    Route::post('emergency-order', [EmergencyOrderController::class, 'store']);
});

// User Routes
Route::middleware(['auth:sanctum', 'user'])->prefix('user')->group(function () {
    // Orders
    Route::get('orders', [UserOrderController::class, 'index']);
    Route::post('orders', [UserOrderController::class, 'store']);
    
    // Jobs
    Route::get('jobs', [UserJobController::class, 'index']);
    Route::post('jobs/{id}/apply', [UserJobController::class, 'apply']);
    
    // Product Orders
    Route::get('products', [ProductOrderController::class, 'index']);
    Route::post('product-orders', [ProductOrderController::class, 'store']);
    
    // Ratings
    Route::post('rate', [UserRatingController::class, 'store']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'notifications'])->middleware('auth:sanctum');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth:sanctum');
});

// Forum
Route::prefix('forum')->group(function () {
    // Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store'])->middleware('auth:sanctum');
    Route::patch('posts/{id}/readonly', [PostController::class, 'makeReadonly'])
        ->middleware(['auth:sanctum', 'admin']);
    
    // Comments
    Route::post('posts/{id}/comment', [CommentController::class, 'store'])
        ->middleware('auth:sanctum');
});

//payment
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);