<?php

//User Routes
Route::get('user/{user}', 'UserController@getIndex');
Route::controller('user', 'UserController');