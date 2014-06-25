<?php

//Static Pages
Route::get('about', 'PageController@getAbout');
Route::get('faq', 'PageController@faq');
Route::get('/', array('as' => 'home', 'uses' => 'PageController@home'));