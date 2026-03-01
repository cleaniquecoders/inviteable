<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Controllers;

use Carbon\Carbon;
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Exceptions\InvalidInvitationTokenException;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvitationController extends Controller
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        $invitation = Invite::query()->forToken($token)->first();

        if (! $invitation) {
            throw new InvalidInvitationTokenException("Invalid invitation token: {$token}");
        }

        if ($invitation->isAccepted()) {
            event(new InvitationAlreadyAccepted($invitation));

            return redirect()->route(config('inviteable.redirect.already_accepted_token'));
        }

        if ($invitation->isExpired()) {
            return redirect()->route(config('inviteable.redirect.expired_token'));
        }

        if ($invitation->isRevoked()) {
            return redirect()->route(config('inviteable.redirect.revoked_token'));
        }

        $invitation->update([
            'status' => InvitationStatus::Accepted,
            'accepted_at' => Carbon::now(),
        ]);

        event(new InvitationAccepted($invitation));

        return redirect()->route(config('inviteable.redirect.accepted_token'));
    }
}
