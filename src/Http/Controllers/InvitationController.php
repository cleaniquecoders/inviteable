<?php

namespace CleaniqueCoders\Inviteable\Http\Controllers;

use Carbon\Carbon;
use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Exceptions\InvalidInvitationToken;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __invoke(Request $request, $token)
    {
        try {
            $invitation = Invite::where('token', $token)->firstOrFail();

            if (! $invitation->is_accepted) {
                $invitation->update([
                    'is_accepted' => true,
                    'accepted_at' => Carbon::now(),
                ]);

                event(new InvitationAccepted($invitation));

                return redirect()->route(config('inviteable.redirect.accepted_token'));
            } else {
                event(new InvitationAlreadyAccepted($invitation));

                return redirect()->route(config('inviteable.redirect.already_accepted_token'));
            }
        } catch (ModelNotFoundException $e) {
            throw new InvalidInvitationToken();
        }
    }
}
