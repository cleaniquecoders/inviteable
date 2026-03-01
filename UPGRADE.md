# Upgrade Guide

## Upgrading to v3.1 from v3.0

### Token Hashing (Breaking)

Tokens are now stored as SHA-256 hashes. Existing plaintext tokens will no longer match.

**Migration steps:**

1. Run the migration to add new columns (`metadata`, `accepted_by`, `accepted_ip`, indexes).
2. Hash existing tokens: `UPDATE invites SET token = SHA2(token, 256);`
3. Old invitation links with plaintext tokens will continue to work since the system hashes incoming tokens before lookup.

### Route Changes (Breaking)

The invitation acceptance flow changed from single GET to a two-step POST flow:

| Before (v3.0) | After (v3.1) |
|----------------|--------------|
| `GET /invitation/{token}` (accepts immediately) | `GET /invitation/{token}` (shows confirmation page) |
| Route name: `invitation` | Route name: `invitation.show` |
| — | `POST /invitation/{token}/accept` (`invitation.accept`) |
| — | `POST /invitation/{token}/decline` (`invitation.decline`) |

**Update any code that generates invitation URLs:**

```php
// Before
route('invitation', $token);

// After
route('invitation.show', $token);
```

### Config Changes

New config keys added. Publish the latest config:

```bash
php artisan vendor:publish --tag=inviteable-config --force
```

New sections: `rate_limit`, `auth`, `mail`, `routes`, `ui`, `inviteable_types`, and `redirect.declined_token`.

### New Statuses

`InvitationStatus` enum now includes `Declined` and `Cancelled`. No migration needed — these are string-backed values.

### New Dependencies

Livewire 4 and FluxUI 2 are now required dependencies. They are installed automatically but add no overhead unless the UI is enabled via config.

### Mass Assignment

The `Invite` model now uses explicit `$fillable` instead of `$guarded = []`. If you were setting custom attributes directly, ensure they are in the fillable list or use `forceFill()`.
