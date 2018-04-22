<?php 

Route::get('invitation/{token}', '\CleaniqueCoders\Inviteable\Http\Controllers\InvitationController')->name('invitation');
Route::view('invitation/access-denied', 'inviteable::errors.access_denied')->name('invitation.access_denied');
Route::view('invitation', 'inviteable::invitations.index')->name('invitation.index');