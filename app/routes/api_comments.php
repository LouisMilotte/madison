<?php

    //Document Comment Routes
    Route::post('api/docs/{doc}/comments', 'CommentApiController@postIndex');
    Route::get('api/docs/{doc}/comments', 'CommentApiController@getIndex');
    Route::get('api/docs/{doc}/comments/{comment?}', 'CommentApiController@getIndex');
    Route::post('api/docs/{doc}/comments/{comment}/likes', 'CommentApiController@postLikes');
    Route::post('api/docs/{doc}/comments/{comment}/dislikes', 'CommentApiController@postDislikes');
    Route::post('api/docs/{doc}/comments/{comment}/flags', 'CommentApiController@postFlags');
    Route::post('api/docs/{doc}/comments/{comment}/comments', 'CommentApiController@postComments');
    Route::post('api/docs/{doc}/comments/{comment}/seen', 'CommentApiController@postSeen');