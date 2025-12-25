<?php

use App\Http\Controllers\{
    DashboardController,
    UserController,
    MentorController,
    SessionnController,
    HelpRequestController,
    FeedbackController
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    LogoutController,
    ForgotPasswordController,
    ResetPasswordController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest routes (only accessible when not authenticated)
Route::middleware('guest')->group(function () {
    
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});

// Public routes
Route::get('/', function () {
    // Redirect to dashboard if authenticated, otherwise to login
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */

    // User CRUD
    Route::resource('users', UserController::class);

    // User Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::post('/users/{user}/add-points', [UserController::class, 'addPoints'])->name('users.add-points');
    Route::post('/users/{user}/toggle-mentor', [UserController::class, 'toggleMentorStatus'])->name('users.toggle-mentor');

    // Browse and Search
    Route::get('/mentors', [UserController::class, 'mentors'])->name('users.mentors');
    Route::get('/leaderboard', [UserController::class, 'leaderboard'])->name('users.leaderboard');
    Route::get('/users-statistics', [UserController::class, 'statistics'])->name('users.statistics');

    /*
    |--------------------------------------------------------------------------
    | Mentor Routes
    |--------------------------------------------------------------------------
    */

    Route::resource('mentors', MentorController::class);
    Route::get('/mentors/{mentor}/statistics', [MentorController::class, 'statistics'])->name('mentors.statistics');
    Route::post('/mentors/{mentor}/update-rating', [MentorController::class, 'updateRating'])->name('mentors.update-rating');
    Route::get('/top-mentors', [MentorController::class, 'topMentors'])->name('mentors.top');

    /*
    |--------------------------------------------------------------------------
    | Session Routes
    |--------------------------------------------------------------------------
    */

    // Session CRUD
    Route::resource('sessions', SessionnController::class);

    // My Sessions
    Route::get('/my-sessions', [SessionnController::class, 'mySessions'])->name('sessions.my-sessions');
    Route::get('/sessions-upcoming', [SessionnController::class, 'upcoming'])->name('sessions.upcoming');
    Route::get('/sessions-statistics', [SessionnController::class, 'statistics'])->name('sessions.statistics');

    // Session Actions
    Route::patch('/sessions/{sessionn}/complete', [SessionnController::class, 'complete'])->name('sessions.complete');
    Route::patch('/sessions/{sessionn}/cancel', [SessionnController::class, 'cancel'])->name('sessions.cancel');
    Route::patch('/sessions/{sessionn}/reschedule', [SessionnController::class, 'reschedule'])->name('sessions.reschedule');

    /*
    |--------------------------------------------------------------------------
    | Help Request Routes
    |--------------------------------------------------------------------------
    */

    // Help Request CRUD
    Route::resource('help-requests', HelpRequestController::class);

    // My Requests
    Route::get('/my-requests', [HelpRequestController::class, 'myRequests'])->name('help-requests.my-requests');
    Route::get('/assigned-to-me', [HelpRequestController::class, 'assignedToMe'])->name('help-requests.assigned-to-me');
    Route::get('/pending-requests', [HelpRequestController::class, 'pending'])->name('help-requests.pending');
    Route::get('/help-requests-statistics', [HelpRequestController::class, 'statistics'])->name('help-requests.statistics');

    // Help Request Actions
    Route::patch('/help-requests/{helpRequest}/accept', [HelpRequestController::class, 'accept'])->name('help-requests.accept');
    Route::patch('/help-requests/{helpRequest}/reject', [HelpRequestController::class, 'reject'])->name('help-requests.reject');
    Route::patch('/help-requests/{helpRequest}/cancel', [HelpRequestController::class, 'cancel'])->name('help-requests.cancel');
    Route::patch('/help-requests/{helpRequest}/resolve', [HelpRequestController::class, 'resolve'])->name('help-requests.resolve');
    Route::post('/help-requests/{helpRequest}/assign-mentor', [HelpRequestController::class, 'assignMentor'])->name('help-requests.assign-mentor');

    // Search
    Route::get('/help-requests-search', [HelpRequestController::class, 'search'])->name('help-requests.search');
    Route::get('/available-mentors/{module}', [HelpRequestController::class, 'availableMentors'])->name('help-requests.available-mentors');

    /*
    |--------------------------------------------------------------------------
    | Feedback Routes
    |--------------------------------------------------------------------------
    */

    // Feedback CRUD
    Route::resource('feedback', FeedbackController::class);

    // My Feedback
    Route::get('/my-feedback', [FeedbackController::class, 'myFeedback'])->name('feedback.my-feedback');
    Route::get('/received-feedback', [FeedbackController::class, 'receivedFeedback'])->name('feedback.received-feedback');
    Route::get('/pending-feedback', [FeedbackController::class, 'pending'])->name('feedback.pending');
    Route::get('/feedback-statistics', [FeedbackController::class, 'statistics'])->name('feedback.statistics');

    // Feedback by Mentor
    Route::get('/mentor-feedback/{mentorId}', [FeedbackController::class, 'mentorFeedback'])->name('feedback.mentor-feedback');
    Route::get('/top-rated-mentors', [FeedbackController::class, 'topRatedMentors'])->name('feedback.top-rated');
});