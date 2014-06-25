<?php

//User Routes
    Route::get('api/user/{user}', 'UserApiController@getUser');
    Route::get('api/user/verify/', 'UserApiController@getVerify');
    Route::post('api/user/verify/', 'UserApiController@postVerify');
    Route::get('api/user/admin/', 'UserApiController@getAdmins');
    Route::post('api/user/admin/', 'UserApiController@postAdmin');

    // User Login / Signup AJAX requests
    Route::get('api/user/login', 'UserManageApiController@getLogin');
    Route::post('api/user/login', 'UserManageApiController@postLogin');
    Route::get('api/user/signup', 'UserManageApiController@getSignup');
    Route::post('api/user/signup', 'UserManageApiController@postSignup');

//Logout Route
Route::get('logout', function(){
  Auth::logout(); //Logout the current user
  Session::flush(); //delete the session
  return Redirect::to('/')->with('message', 'You have been successfully logged out.');
});