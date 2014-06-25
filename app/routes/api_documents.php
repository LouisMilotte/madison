<?php

// Document API Routes
  Route::get('api/user/sponsors/all', 'DocumentApiController@getAllSponsorsForUser');
  Route::get('api/sponsors/all', 'SponsorApiController@getAllSponsors');

  //Document Support / Oppose routes
    Route::post('api/docs/{doc}/support/', 'DocController@postSupport');
    Route::get('api/users/{user}/support/{doc}', 'UserApiController@getSupport');

    //Document Api Routes
    Route::get('api/docs/recent/{query?}', 'DocumentApiController@getRecent')->where('query', '[0-9]+');
    Route::get('api/docs/categories', 'DocumentApiController@getCategories');
    Route::get('api/docs/statuses', 'DocumentApiController@getAllStatuses');
    Route::get('api/docs/sponsors', 'DocumentApiController@getAllSponsors');
    Route::get('api/docs/{doc}/categories', 'DocumentApiController@getCategories');
    Route::post('api/docs/{doc}/categories', 'DocumentApiController@postCategories');
    Route::get('api/docs/{doc}/sponsor/{sponsor}', 'DocumentApiController@hasSponsor');
    Route::get('api/docs/{doc}/sponsor', 'DocumentApiController@getSponsor');
    Route::post('api/docs/{doc}/sponsor', 'DocumentApiController@postSponsor');
    Route::get('api/docs/{doc}/status', 'DocumentApiController@getStatus');
    Route::post('api/docs/{doc}/status', 'DocumentApiController@postStatus');
    Route::get('api/docs/{doc}/dates', 'DocumentApiController@getDates');
    Route::post('api/docs/{doc}/dates', 'DocumentApiController@postDate');
    Route::put('api/dates/{date}', 'DocumentApiController@putDate');
    Route::delete('api/docs/{doc}/dates/{date}', 'DocumentApiController@deleteDate');
    Route::get('api/docs/{doc}', 'DocumentApiController@getDoc');
    Route::post('api/docs/{doc}', 'DocumentApiController@postDoc');
    Route::get('api/docs/', 'DocumentApiController@getDocs');