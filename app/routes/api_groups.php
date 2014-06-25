<?php

// Group Routes
    Route::get('api/groups/verify/', 'GroupsApiController@getVerify');
    Route::post('api/groups/verify/', 'GroupsApiController@postVerify');