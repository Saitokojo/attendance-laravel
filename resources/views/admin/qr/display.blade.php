<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR — {{ $office->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white min-h-screen flex flex-col items-center justify-center select-none">

    <p class="text-gray-400 text-sm uppercase tracking-widest mb-4">{{ $office->name }}</p>

    <div class="bg-white rounded-2xl p-6 shadow-2xl mb-8">
        {!! $qrSvg !!}
    </div>

    <div id="countdown" class="text-6xl font-mono font-bold tracking-widest tabular-nums text-white">
        --:--
    </div>
    <p class="mt-3 text-gray-500 text-sm">Session expires at {{ $session->expires_at->format('H:i:s') }}</p>

    <a href="{{ route('admin.qr.index') }}"
       class="mt-10 text-xs text-gray-600 hover:text-gray-400 transition-colors">
        ← Back to QR Sessions
    </a>

    <script>
        const expiresAt = new Date({{ $session->expires_at->timestamp }} * 1000);
        const indexUrl  = '{{ route('admin.qr.index') }}';
        const el        = document.getElementById('countdown');

        function tick() {
            const remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));

            if (remaining === 0) {
                el.textContent = '00:00';
                window.location.href = indexUrl;
                return;
            }

            const mm = String(Math.floor(remaining / 60)).padStart(2, '0');
            const ss = String(remaining % 60).padStart(2, '0');
            el.textContent = `${mm}:${ss}`;
        }

        tick();
        setInterval(tick, 1000);
    </script>
</body>
</html>
