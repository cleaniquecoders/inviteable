<?php 

Route::get('invitation/{token}', '\CleaniqueCoders\Inviteable\Http\Controllers\InvitationController')->name('invitation');