<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Invitation</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .card { background: white; border-radius: 8px; padding: 2rem; max-width: 400px; width: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        p { color: #6b7280; margin-bottom: 1.5rem; }
        .actions { display: flex; gap: 1rem; justify-content: center; }
        button { padding: 0.625rem 1.25rem; border-radius: 6px; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer; }
        .accept { background: #10b981; color: white; }
        .accept:hover { background: #059669; }
        .decline { background: #ef4444; color: white; }
        .decline:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="card">
        <h1>You've Been Invited</h1>
        <p>{{ $invitation->name }}</p>

        <div class="actions">
            <form method="POST" action="{{ route('invitation.accept', $token) }}">
                @csrf
                <button type="submit" class="accept">Accept</button>
            </form>

            <form method="POST" action="{{ route('invitation.decline', $token) }}">
                @csrf
                <button type="submit" class="decline">Decline</button>
            </form>
        </div>
    </div>
</body>
</html>
