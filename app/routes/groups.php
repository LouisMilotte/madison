<?php

Route::group(['prefix' => 'groups'], function () {
    Route::get('/', 'GroupsController@getIndex');
    Route::put('edit', 'GroupsController@putEdit');
    Route::get('edit/{groupId?}', 'GroupsController@getEdit');
    Route::get('members/{groupId}', 'GroupsController@getMembers');
    Route::get('member/{memberId}/delete', 'GroupsController@removeMember');
    Route::post('member/{memberId}/role', 'GroupsController@changeMemberRole');
    Route::get('invite/{groupId}', 'GroupsController@inviteMember');
    Route::put('invite/{groupId}', 'GroupsController@processMemberInvite');
    Route::get('active/{groupId}', 'GroupsController@setActiveGroup');
});