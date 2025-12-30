<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PsikiaterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PsikologController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderPaymentController;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Mail\BookingStatusUpdatedMail;
use App\Http\Controllers\TherapyGroupController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/services', 'services')->name('services');
Route::view('/about', 'about')->name('about');

Route::get('/psikiater', [PsikiaterController::class, 'index'])->name('psikiater.index');
Route::get('/psikiater/list', [PsikiaterController::class, 'list'])->name('psikiater.list');
Route::get('/psikiater/bookings/{booking}/detail',
    [PsikiaterController::class, 'bookingDetail']
)->name('psikiater.booking.detail');

Route::get('/psikolog', [PsikologController::class, 'index'])->name('psikolog.index');
Route::get('/psikolog/list', [PsikologController::class, 'list'])->name('psikolog.list'); // optional JSON API

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/users', [AdminController::class, 'users'])
    ->name('admin.users');
Route::get('/admin/psikiater', [AdminController::class, 'psikiater'])
    ->name('admin.psikiater');
Route::get('/admin/bookings', [AdminController::class, 'bookings']);

Route::get('/admin/psikiater/create', [AdminController::class, 'createPsikiater'])
    ->name('admin.psikiater.create');
Route::post('/admin/psikiater', [AdminController::class, 'storePsikiater'])
    ->name('admin.psikiater.store');
Route::delete('/admin/psikiater/{id}', [AdminController::class, 'destroyPsikiater'])
    ->name('admin.psikiater.destroy');
Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])
    ->name('admin.users.destroy');
Route::get('/admin/psikolog', [AdminController::class, 'psikolog'])
    ->name('admin.psikolog');

Route::get('/admin/psikolog/create', [AdminController::class, 'createPsikolog'])
    ->name('admin.psikolog.create');

Route::post('/admin/psikolog', [AdminController::class, 'storePsikolog'])
    ->name('admin.psikolog.store');

Route::delete('/admin/psikolog/{id}', [AdminController::class, 'destroyPsikolog'])
    ->name('admin.psikolog.destroy');
Route::get('/admin/bookings', [AdminController::class, 'bookingMenu'])
    ->name('admin.bookings');
Route::get('/admin/bookings/psikiater', [AdminController::class, 'bookingsPsikiater'])
    ->name('admin.bookings.psikiater');
Route::get('/admin/bookings/psikolog', [AdminController::class, 'bookingsPsikolog'])
    ->name('admin.bookings.psikolog');
Route::get('/admin/therapy-groups', [AdminController::class, 'therapyGroups'])
    ->name('admin.therapy.index');

Route::delete('/admin/therapy-groups/{slug}', [AdminController::class, 'therapyGroupsDestroy'])
    ->name('admin.therapy.destroy');
Route::get('/admin/therapy-groups', [AdminController::class, 'therapyGroups'])
    ->name('admin.therapy.groups');

Route::get('/admin/therapy-groups/create', [AdminController::class, 'createTherapyGroup'])
    ->name('admin.therapy.groups.create');

Route::post('/admin/therapy-groups', [AdminController::class, 'storeTherapyGroup'])
    ->name('admin.therapy.groups.store');
Route::get('/admin/therapy-groups/{slug}', [AdminController::class, 'openTherapyGroup'])
    ->name('admin.therapy.groups.open');



Route::post('/psikiater/logout', [AuthController::class, 'logout'])->name('psikiater.logout');

Route::middleware('auth')->group(function () {

    Route::get('/psikiater/dashboard', [PsikiaterController::class, 'dashboard'])
        ->name('psikiater.dashboard');

    Route::get('/psikolog/dashboard', [PsikologController::class, 'dashboard'])
        ->name('psikolog.dashboard');

    Route::get('/bookings/{booking}/psikolog/approve', [PsikologController::class, 'approveConfirm'])
        ->name('bookings.psikolog.approve.confirm');

    Route::get('/bookings/{booking}/psikolog/reject', [PsikologController::class, 'rejectConfirm'])
        ->name('bookings.psikolog.reject.confirm');
    Route::post('/bookings/{booking}/psikolog/approve', [PsikologController::class, 'approve'])
        ->name('psikolog.booking.approve');

    Route::post('/bookings/{booking}/psikolog/reject', [PsikologController::class, 'reject'])
        ->name('psikolog.booking.reject');

    Route::get('/psikolog/bookings/{booking}/chat', [ChatController::class, 'show'])
        ->name('psikolog.chat.show');
    Route::get('/psikolog/bookings/{booking}/chat/messages', [ChatController::class, 'messages'])
        ->name('psikolog.chat.messages');
    Route::post('/psikolog/bookings/{booking}/chat/send', [ChatController::class, 'send'])
        ->name('psikolog.chat.send');
    Route::get('/bookings/{booking}/chat', [\App\Http\Controllers\ChatController::class, 'show'])
        ->name('user.chat.show');

    Route::get('/bookings/{booking}/chat/messages', [\App\Http\Controllers\ChatController::class, 'messages'])
        ->name('user.chat.messages');

    Route::post('/bookings/{booking}/chat/send', [\App\Http\Controllers\ChatController::class, 'send'])
        ->name('user.chat.send');

    Route::post('/psikolog/bookings/{booking}/end', [ChatController::class, 'endSession'])
        ->name('psikolog.chat.end');
    Route::post('/psikolog/bookings/{booking}/finish', [PsikologController::class, 'finish'])
        ->name('psikolog.booking.finish');

    Route::get('/psikolog/profile', [App\Http\Controllers\PsikologController::class, 'profile'])
        ->name('psikolog.profile');

    Route::get('/psikolog/profile/edit', [App\Http\Controllers\PsikologController::class, 'editProfile'])
        ->name('psikolog.profile.edit');

    Route::post('/psikolog/profile', [App\Http\Controllers\PsikologController::class, 'updateProfile'])
        ->name('psikolog.profile.update');
    Route::get('/psikiater/profile', [\App\Http\Controllers\PsikiaterController::class, 'profile'])
        ->name('psikiater.profile');

    Route::get('/psikiater/profile/edit', [\App\Http\Controllers\PsikiaterController::class, 'editProfile'])
        ->name('psikiater.profile.edit');

    Route::post('/psikiater/profile', [\App\Http\Controllers\PsikiaterController::class, 'updateProfile'])
        ->name('psikiater.profile.update');


    Route::get('/therapy-groups', [TherapyGroupController::class,'index'])->name('therapy.index');
    Route::get('/therapy-groups/{slug}/open', [TherapyGroupController::class,'open'])->name('therapy.open');
    Route::get('/therapy-groups/{slug}', [TherapyGroupController::class,'show'])->name('therapy.show');
    Route::post('/therapy-groups/{slug}/messages', [\App\Http\Controllers\GroupMessageController::class,'store'])->name('therapy.messages.store');
    Route::get('/therapy-groups/{slug}/messages', [\App\Http\Controllers\GroupMessageController::class,'fetch'])->name('therapy.messages.fetch');


        Route::get('/booking/{providerId}/create', [BookingController::class, 'create'])
        ->name('booking.create');
    Route::get('/booking/psikiater/{psikiater}', [BookingController::class, 'create'])
        ->name('booking.create.legacy');

    Route::get('/payment/{booking}', [PaymentController::class, 'show'])
        ->name('payment.show');

    Route::post('/payment/confirm/{payment}', [PaymentController::class, 'confirm'])
        ->name('payment.confirm');

    Route::get('/provider/payments/{payment}',
        [ProviderPaymentController::class, 'show']
    )->name('provider.payment.show');

    Route::post('/provider/payments/{payment}/approve',
        [ProviderPaymentController::class, 'approve']
    )->name('provider.payment.approve');

    Route::post('/provider/payments/{payment}/reject',
        [ProviderPaymentController::class, 'reject']
    )->name('provider.payment.reject');

    Route::get('/provider/payments',
        [ProviderPaymentController::class, 'index']
    )->name('provider.payment.index');


        Route::post('/booking', [BookingController::class, 'store'])
        ->name('booking.store');
    Route::post('/booking/psikiater', [BookingController::class, 'store'])
        ->name('booking.store.legacy');

        Route::get('/psikiater/{psikiater}/available-times', [BookingController::class, 'availableTimes'])
         ->name('psikiater.available_times');

        Route::get('/bookings/{booking}/approve', [PsikiaterController::class, 'approveConfirm'])
        ->name('bookings.approve.confirm');

    Route::get('/bookings/{booking}/reject', [PsikiaterController::class, 'rejectConfirm'])
        ->name('bookings.reject.confirm');

        Route::post('/bookings/{booking}/approve', [PsikiaterController::class, 'approve'])
        ->name('psikiater.booking.approve');

    Route::post('/bookings/{booking}/reject', [PsikiaterController::class, 'reject'])
        ->name('psikiater.booking.reject');

    Route::post('/bookings/{booking}/finish', [PsikiaterController::class, 'finish'])
        ->name('psikiater.booking.finish');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/booking/{service}', function($service){
    return "Booking page for " . e($service);
})->name('booking');

Route::get('/_debug/send-status-mail/{id}', function($id){
    $booking = \App\Models\Booking::with('user','psikiater')->find($id);
    if (!$booking) return response('Booking not found', 404);
    try {
        \Log::info("DEBUG: attempting to send BookingStatusUpdatedMail to " . optional($booking->user)->email);
        \Mail::to(optional($booking->user)->email)->send(new \App\Mail\BookingStatusUpdatedMail($booking));
        \Log::info("DEBUG: Mail::send completed for booking_id={$booking->id}");
        return response('Mail attempted', 200);
    } catch (\Throwable $e) {
        \Log::error("DEBUG: mail exception: " . $e->getMessage(), ['ex'=>$e]);
        return response('Mail exception: ' . $e->getMessage(), 500);
    }
});
