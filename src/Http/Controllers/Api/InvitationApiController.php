<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Http\Controllers\Api;

use CleaniqueCoders\Inviteable\Enums\InvitationStatus;
use CleaniqueCoders\Inviteable\Http\Resources\InviteResource;
use CleaniqueCoders\Inviteable\InviteableManager;
use CleaniqueCoders\Inviteable\Models\Invite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class InvitationApiController extends Controller
{
    public function __construct(
        private readonly InviteableManager $manager,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Invite::query();

        if ($request->has('status')) {
            $status = InvitationStatus::tryFrom((string) $request->input('status'));
            if ($status) {
                $query->where('status', $status);
            }
        }

        if ($request->has('inviteable_type')) {
            $query->where('inviteable_type', $request->input('inviteable_type'));
        }

        return InviteResource::collection(
            $query->latest()->paginate($request->integer('per_page', 15))
        );
    }

    public function store(Request $request): InviteResource
    {
        $request->validate([
            'inviteable_type' => ['required', 'string'],
            'inviteable_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'invited_by' => ['nullable', 'integer'],
            'expiry_hours' => ['nullable', 'integer', 'min:1'],
            'metadata' => ['nullable', 'array'],
        ]);

        $inviteableType = (string) $request->input('inviteable_type');
        $inviteableId = $request->integer('inviteable_id');

        $inviteable = $inviteableType::findOrFail($inviteableId);

        $invite = $this->manager->create(
            inviteable: $inviteable,
            name: (string) $request->input('name'),
            invitedBy: $request->integer('invited_by') ?: null,
            expiryHours: $request->integer('expiry_hours') ?: null,
            metadata: $request->input('metadata'),
        );

        return new InviteResource($invite);
    }

    public function show(Invite $invite): InviteResource
    {
        return new InviteResource($invite);
    }

    public function accept(Request $request, string $token): InviteResource
    {
        $invite = $this->manager->accept($token);

        return new InviteResource($invite);
    }

    public function decline(Request $request, string $token): InviteResource
    {
        $invite = $this->manager->decline($token);

        return new InviteResource($invite);
    }

    public function revoke(Request $request, string $token): InviteResource
    {
        $invite = $this->manager->revoke($token);

        return new InviteResource($invite);
    }

    public function destroy(Invite $invite): JsonResponse
    {
        $invite->delete();

        return response()->json(['message' => 'Invitation deleted.'], 200);
    }
}
