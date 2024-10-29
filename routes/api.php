<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = \App\Models\User::first();
    $user->google_access_token_json = json_decode($user->google_access_token_json);
    return response()->json($user);
});

Route::group(['prefix' => 'google'], function () {
    Route::post('auth/login', [GoogleController::class, 'postLogin']);
    Route::get('login/url', [GoogleController::class, 'getAuthUrl']);

    Route::group(['prefix' => 'youtube'], function () {
        Route::group(['prefix' => 'broadcast'], function () {
            Route::post('', [YoutubeController::class, 'createBroadcast']);
            Route::delete('{broadcast_id}', [YoutubeController::class, 'deleteBroadcast']);
        });

    });
});

