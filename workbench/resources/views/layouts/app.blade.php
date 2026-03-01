<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inviteable Preview</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    @fluxAppearance
    @livewireStyles
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <nav class="flex items-center gap-4 mb-8 pb-4 border-b">
            <a href="{{ route('inviteable.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
            <a href="{{ route('inviteable.create') }}" class="text-blue-600 hover:underline">Create</a>
            <a href="{{ route('inviteable.settings') }}" class="text-blue-600 hover:underline">Settings</a>
        </nav>

        @yield('content')
    </div>

    @fluxScripts
    @livewireScripts
</body>
</html>
