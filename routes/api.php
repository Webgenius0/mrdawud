<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\backend\Auth;

use App\Http\Controllers\API\product\ProductController;

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
    // Route::get('send-notification', function () {
    //     $user = User::where('id', Auth::id())->first();
    //     $user->notify(new UserNotifications('Jb', "Test Notification"));

    //     //Send fire base notification
    //     $device_tokens = FirebaseTokens::where(function ($query) {
    //         $query->where('user_id', Auth::id())
    //             ->orWhereNull('user_id');
    //     })
    //         ->where('is_active', '1')
    //         ->get();
    //     $data = [
    //         'message' => $user->name . ' has sent you a notification',
    //     ];
    //     foreach ($device_tokens as $device_token) {
    //         Helper::sendNotifyMobile($device_token->token, $data);
    //     }

    //     return $response = ['success' => true, 'message' => 'Notification sent successfully'];
    // });


    // // Dua & Dua SubCategory
    // Route::controller(DuaController::class)->group(function () {
    //     Route::get('/dua-subcategories/{cat_id}', 'DuaSubCategories');
    //     Route::get('/subcategories/{subcat_id}', 'SubCatGetDua');
    // });


    // Dua & Dua Category
    Route::controller(DuaController::class)->group(function () {
        Route::get('/dua-categories', 'DuaCategories');
        Route::get('/dua-categories/{cat_id}', 'GetDua');
        Route::get('/dua/{id}', 'DuaDetails');
    });

    // Dua & Dua SubCategory
    Route::controller(DuaController::class)->group(function () {
        Route::get('/dua-subcategories/{cat_id}', 'DuaSubCategories');
        Route::get('/subcategories/{subcat_id}', 'SubCatGetDua');

    });

    // Bookmark
    Route::controller(BookmarkController::class)->group(function () {
        // For Surah
        Route::get('/bookmark/surah', 'surahBookmarkIndex');
        Route::post('/bookmark/surah/store', 'surahBookmarkStore');
        Route::get('/bookmark/surah/delete/{id}', 'surahBookmarkDestroy');

        // For Hadit
        Route::get('/bookmark/hadit', 'haditBookmarkIndex');
        Route::post('/bookmark/hadit/store', 'haditBookmarkStore');
        Route::get('/bookmark/hadit/delete/{id}', 'haditBookmarkDestroy');

        // For dua
        Route::get('/bookmark/duah', 'duahBookmarkIndex');
        Route::post('/bookmark/duah/store', 'duahBookmarkStore');
        Route::get('/bookmark/duah/delete/{id}', 'duahBookmarkDestroy');
    });

    // Prayer Tracker
    Route::controller(TrackerContoller::class)->group(function () {
        Route::post('/avagage', 'AvgOfSalat');
        Route::post('/getavagage', 'dateWizeAvgOfSalat');
        Route::post('/prayer', 'store');
    });

    // Community
    Route::controller(CommunityController::class)->group(function () {
        Route::get('/community', 'index');
        Route::post('/community', 'store');
        Route::post('/community/update', 'update');
        Route::delete('/community/delete/{id}', 'destroy');

        // Like Post
        Route::post('/community/like', 'LikePost');
    });

    // Community Comment
    Route::controller(CommentController::class)->group(function () {
        Route::get('/comment', 'index');
        Route::get('/getpostcomment/{post_id}', 'getPostComment');
        Route::post('/comment', 'updateOrCreate');
        Route::delete('/comment/delete/{id}', 'destroy');
    });

    // Community Comment Replay
    Route::controller(RepliesController::class)->group(function () {
        Route::get('/getcommentreplies/{comment_id}', 'getCommentReplies');
        Route::post('/replay', 'updateOrCreate');
        Route::delete('/replay/delete/{id}', 'destroy');
    });

    // Community Comment Replay
    Route::controller(MessagingController::class)->group(function () {
        Route::get('/inbox', 'index');
        Route::post('/store/message', 'store');
    });

    // Journals
    Route::controller(JournController::class)->group(function () {
        Route::get('/journal/index/{id}', 'index');
        Route::post('/store/journal/{id}', 'store');
    });

    // News Letter Endpoints
    Route::controller(NewsLetterController::class)->group(function () {
        Route::get('/newsletter', 'index');
    });

    // Verse
    Route::get('/verse', [UserController::class, 'getVerse']);

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
});

    // // Firebase Token Module
    // Route::post("firebase/token/add", [FirebaseTokenController::class, "store"]);
    // Route::post("firebase/token/get", [FirebaseTokenController::class, "getToken"]);
    // Route::post("firebase/token/detele", [FirebaseTokenController::class, "deleteToken"]);


    //social media
    Route::controller(SocialmediaController::class)->group(function () {
        Route::post('add-social-media', 'addSocialMedia');
    });

    //Videp Upload
    Route::controller(VideoUploadController::class)->group(function () {
        Route::post('video-upload', 'uploadVideo');
        Route::get('/show-video', 'showVideo');
        Route::post('/edit-video/{id}', 'editVideo');
        Route::post('/delete-video/{id}', 'deleteVideo');
    });
    //Reminder
    Route::controller(RemainderController::class)->group(function () {
        Route::post('reminder-add', 'remainder');
        Route::get('remainder-list', 'remainderList');
        Route::post('remainder-edit/{id}', 'remainderEdit');
        Route::post('remainder-delete/{id}', 'remainderDelete');
    });


     Route::controller(CategoryController::class)->group(function () {

        Route::get('/category', 'categoryShow');

     });

     Route::controller(ProductController::class)->group(function () {

        Route::get('/show-product', 'showProduct');

     });

   
   // Route::post('/category', [CategoryController::class, 'uploadVideo']);

   Route::controller(MessagingController::class)->group(function () {
       Route::get('get-conversations','getConversations');
       Route::post('send-message','sendMessage');
   });