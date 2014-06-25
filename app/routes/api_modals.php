<?php

// Modal Routes
Route::get('modals/annotation_thanks', array(
  'uses' => 'ModalController@getAnnotationThanksModal',
  'before' => 'disable profiler'
));

Route::post('modals/annotation_thanks', 'ModalController@seenAnnotationThanksModal');