<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\backend\Auth;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\MessagingController;
use App\Http\Controllers\API\RemainderController;
use App\Http\Controllers\API\SocialmediaController;
use App\Http\Controllers\API\VideoUploadController;
use App\Http\Controllers\API\category\CategoryController;


Route::controller(UserAuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');

    // Resend Otp
    Route::post('resend-otp', 'resendOtp');

    // Forget Password
    Route::post('forget-password', 'forgetPassword');
    Route::post('verify-otp-password', 'varifyOtpWithOutAuth');
    Route::post('reset-password', 'resetPassword');

    // Google Login
    Route::post('google/login', 'googleLogin');
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('me', [UserAuthController::class, 'me']);
    Route::post('refresh', [UserAuthController::class, 'refresh']);


    Route::delete('/delete/user', [UserController::class, 'deleteUser']);

    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('user-update', [UserController::class, 'updateUserInfo']);

    // Get Notifications
    Route::get('/my-notifications', [UserController::class, 'getMyNotifications']);
    Route::get('send-notification', function () {
        $user = User::where('id', Auth::id())->first();
        $user->notify(new UserNotifications('Jb', "Test Notification"));

        //Send fire base notification
        $device_tokens = FirebaseTokens::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhereNull('user_id');
        })
            ->where('is_active', '1')
            ->get();
        $data = [
            'message' => $user->name . ' has sent you a notification',
        ];
        foreach ($device_tokens as $device_token) {
            Helper::sendNotifyMobile($device_token->token, $data);
        }

        return $response = ['success' => true, 'message' => 'Notification sent successfully'];
    });


    // Dua & Dua SubCategory
    Route::controller(DuaController::class)->group(function () {
        Route::get('/dua-subcategories/{cat_id}', 'DuaSubCategories');
        Route::get('/subcategories/{subcat_id}', 'SubCatGetDua');
    });

    // Firebase Token Module
    Route::post("firebase/token/add", [FirebaseTokenController::class, "store"]);
    Route::post("firebase/token/get", [FirebaseTokenController::class, "getToken"]);
    Route::post("firebase/token/detele", [FirebaseTokenController::class, "deleteToken"]);

    //social media
    Route::controller(SocialmediaController::class)->group(function () {
        Route::post('add-social-media', 'addSocialMedia');
    });

    //Videp Upload
    Route::controller(VideoUploadController::class)->group(function () {
        Route::post('video-upload', 'uploadVideo');
    });
    //Reminder
    Route::controller(RemainderController::class)->group(function () {
        Route::post('reminder-add', 'remainder');
    });


     Route::controller(CategoryController::class)->group(function () {

        Route::get('/category', 'categoryShow');

     });
   // Route::post('/category', [CategoryController::class, 'uploadVideo']);

   Route::controller(MessagingController::class)->group(function () {
       Route::get('get-conversations','getConversations');
       Route::post('send-message','sendMessage');
   });
});
