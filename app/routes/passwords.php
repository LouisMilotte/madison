<?php

//Password Routes
Route::get( 'password/remind', 'RemindersController@getRemind');
Route::post('password/remind', 'RemindersController@postRemind');
Route::get( 'password/reset/{token}',  'RemindersController@getReset');
Route::post('password/reset',  'RemindersController@postReset');