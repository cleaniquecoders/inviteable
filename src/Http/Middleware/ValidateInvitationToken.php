<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Middleware;

use CleaniqueCoders\Inviteable\Models\Invite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateInvitationToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        if ($token && Invite::query()->forToken($token)->active()->exists()) {
            return $next($request);
        }

        return redirect()->route(config('inviteable.redirect.middleware'));
    }
}
