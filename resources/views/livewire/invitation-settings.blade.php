<div>
    <flux:heading size="xl" class="mb-6">Invitation Settings</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <flux:card>
            <flux:heading size="sm" class="mb-4">Token & Expiry</flux:heading>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Token Length</dt>
                    <dd class="text-sm font-medium">{{ $config['token_length'] }} characters</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Expiry Duration</dt>
                    <dd class="text-sm font-medium">{{ $config['expiry_duration'] }} hours</dd>
                </div>
            </dl>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4">Rate Limiting</flux:heading>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Max Attempts</dt>
                    <dd class="text-sm font-medium">{{ $config['rate_limit_attempts'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Decay Period</dt>
                    <dd class="text-sm font-medium">{{ $config['rate_limit_decay'] }} minute(s)</dd>
                </div>
            </dl>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4">Authentication</flux:heading>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Auth Required</dt>
                    <dd class="text-sm font-medium">{{ $config['auth_required'] ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Auth Guard</dt>
                    <dd class="text-sm font-medium">{{ $config['auth_guard'] ?? 'Default' }}</dd>
                </div>
            </dl>
        </flux:card>

        <flux:card>
            <flux:heading size="sm" class="mb-4">Features</flux:heading>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Mail Queue</dt>
                    <dd class="text-sm font-medium">{{ $config['mail_queue'] ? 'Enabled' : 'Disabled' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">API Routes</dt>
                    <dd class="text-sm font-medium">{{ $config['api_enabled'] ? 'Enabled' : 'Disabled' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">UI Dashboard</dt>
                    <dd class="text-sm font-medium">{{ $config['ui_enabled'] ? 'Enabled' : 'Disabled' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">UI Prefix</dt>
                    <dd class="text-sm font-medium">/{{ $config['ui_prefix'] }}</dd>
                </div>
            </dl>
        </flux:card>
    </div>
</div>
