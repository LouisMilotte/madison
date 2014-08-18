<?php

Route::group(array('prefix' => 'groups'), function () {
  Route::get('', 'GroupsController@getIndex');
});


Route::put('groups/edit', 'GroupsController@putEdit');
Route::get('groups/edit/{groupId?}', 'GroupsController@getEdit');
Route::get('groups/members/{groupId}', 'GroupsController@getMembers');
Route::get('groups/member/{memberId}/delete', 'GroupsController@removeMember');
Route::post('groups/member/{memberId}/role', 'GroupsController@changeMemberRole');
Route::get('groups/invite/{groupId}', 'GroupsController@inviteMember');
Route::put('groups/invite/{groupId}', 'GroupsController@processMemberInvite');
Route::get('groups/active/{groupId}', 'GroupsController@setActiveGroup');