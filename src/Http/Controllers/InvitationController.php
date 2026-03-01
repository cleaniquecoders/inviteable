<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Controllers;

use Carbon\Carbon;
use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Events\InvitationAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationAlreadyAccepted;
use CleaniqueCoders\Inviteable\Events\InvitationDeclined;
use CleaniqueCoders\Inviteable\Exceptions\InvalidInvitationTokenException;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(Request $request, string $token): View|RedirectResponse
    {
        $hashedToken = hash('sha256', $token);
        $invitation = Invite::query()->forToken($hashedToken)->first();

        if (! $invitation) {
            throw new InvalidInvitationTokenException('The invitation token is invalid or does not exist.');
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

        if ($invitation->isDeclined()) {
            return redirect()->route(config('inviteable.redirect.declined_token'));
        }

        return view('inviteable::invitations.confirm', [
            'invitation' => $invitation,
            'token' => $token,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $hashedToken = hash('sha256', $token);
        $invitation = Invite::query()->forToken($hashedToken)->first();

        if (! $invitation) {
            throw new InvalidInvitationTokenException('The invitation token is invalid or does not exist.');
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

        if (config('inviteable.auth.required') && ! $this->isAuthenticated($request)) {
            session()->put('inviteable.intended_token', $token);

            return redirect()->route(config('inviteable.auth.redirect'));
        }

        $invitation->update([
            'status' => InvitationStatus::Accepted,
            'accepted_at' => Carbon::now(),
            'accepted_by' => $request->user()?->getAuthIdentifier(),
            'accepted_ip' => $request->ip(),
        ]);

        event(new InvitationAccepted($invitation));

        return redirect()->route(config('inviteable.redirect.accepted_token'));
    }

    public function decline(Request $request, string $token): RedirectResponse
    {
        $hashedToken = hash('sha256', $token);
        $invitation = Invite::query()->forToken($hashedToken)->first();

        if (! $invitation) {
            throw new InvalidInvitationTokenException('The invitation token is invalid or does not exist.');
        }

        if (! $invitation->isPending()) {
            return redirect()->route(config('inviteable.redirect.declined_token'));
        }

        if (config('inviteable.auth.required') && ! $this->isAuthenticated($request)) {
            session()->put('inviteable.intended_token', $token);

            return redirect()->route(config('inviteable.auth.redirect'));
        }

        $invitation->update([
            'status' => InvitationStatus::Declined,
        ]);

        event(new InvitationDeclined($invitation));

        return redirect()->route(config('inviteable.redirect.declined_token'));
    }

    private function isAuthenticated(Request $request): bool
    {
        $guard = config('inviteable.auth.guard');

        return $request->user($guard) !== null;
    }
}
