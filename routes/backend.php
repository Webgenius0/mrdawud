<?php

use App\Http\Controllers\Web\backend\LocationController;
use App\Http\Controllers\Web\backend\PremissionController;
use App\Http\Controllers\Web\backend\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\UserController;
use App\Http\Controllers\Web\backend\SettingController;
use App\Http\Controllers\Web\backend\admin\FAQController;
use App\Http\Controllers\Web\backend\CategoryController;
use App\Http\Controllers\Web\backend\settings\DynamicPagesController;
use App\Http\Controllers\Web\backend\settings\ProfileSettingController;

Route::middleware(['auth'])->group(function () {
    Route::controller(SettingController::class)->group(function () {
        Route::get('/general/setting', 'create')->name('general.setting');
        Route::post('/system/update', 'update')->name('system.update');
        Route::get('/admin/setting', 'adminSetting')->name('admin.setting');
        Route::post('/admin/setting/update', 'adminSettingUpdate')->name('admin.settingupdate');
    });

    //profile Settings Controller
    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile');
        Route::post('/profile/update', 'updateProfile')->name('profile.update');
        Route::post('/profile/update/password', 'updatePassword')->name('profile.update.password');
        Route::post('/profile/update/profile-picture', 'updateProfilePicture')->name('profile.update.profile.picture');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users/list', 'index')->name('user.list');
        Route::get('/view/users/{id}', 'show')->name('show.user');
        Route::get('/status/users/{id}', 'status')->name('user.status');
    });

    Route::prefix('permissions')->controller(PremissionController::class)->group(function () {
        Route::get('/list', 'index')->name('admin.permissions.list');
        Route::get('/view/users/{id}', 'show')->name('show.user');
    });

    Route::prefix('role')->controller(RoleController::class)->group(function () {
        Route::get('/list', 'index')->name('admin.role.list');
        Route::get('/create', 'create')->name('admin.role.create');
        Route::post('/store', 'store')->name('admin.role.store');
        Route::get('/edit/{id}', 'edit')->name('admin.role.edit');
        Route::post('/update/{id}', 'update')->name('admin.role.update');
        Route::delete('/destroy/{id}', 'destroy')->name('admin.role.destroy');

    });

    // Category Route
    Route::prefix('category')->controller(CategoryController::class)->as('admin.category.')->group(function () {
        Route::get('/list', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{category}', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
        Route::delete('/destroy/{category}', 'destroy')->name('destroy');
        Route::get('/status/{id}', 'status')->name('status');
        Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
    });

    //Dynamic Pages Route

    Route::resource('dynamicPages', DynamicPagesController::class);
    Route::post('dynamicPages/status/{id}', [DynamicPagesController::class, 'changeStatus'])->name('dynamicPages.status');

    //FAQ Route
    Route::resource('faq', FAQController::class);
    Route::post('faq/status/{id}', [FAQController::class, 'changeStatus'])->name('faq.status');




});
