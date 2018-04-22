<?php 

return [
	'redirect' => [
		'accepted_token' => 'invitation.index',
		'already_accepted_token' => 'invitation.index',
		'middleware' => 'invitation.access_denied'
	],
];