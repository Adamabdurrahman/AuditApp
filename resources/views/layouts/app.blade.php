<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- PASTIKAN SCRIPT INI ADA -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/anchor@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script>
            window.auditVoice = (() => {
                if (typeof window === 'undefined' || !('speechSynthesis' in window)) {
                    return { speak: () => {} };
                }

                const synth = window.speechSynthesis;
                let cachedVoice = null;
                const femaleKeywords = ['female', 'woman', 'zira', 'aria', 'samantha', 'jenny', 'lisa', 'siti', 'ayu', 'lydia', 'luna', 'olivia', 'sofia', 'mia', 'hana', 'ayumi', 'eva'];

                const isFemaleVoice = (voice) => {
                    const name = (voice?.name ?? '').toLowerCase();
                    const uri = (voice?.voiceURI ?? '').toLowerCase();
                    return femaleKeywords.some(keyword => name.includes(keyword) || uri.includes(keyword));
                };

                const forceLoadVoices = () => {
                    synth.getVoices();
                };

                const pickVoice = () => {
                    if (cachedVoice) {
                        return cachedVoice;
                    }

                    const voices = synth.getVoices();
                    if (!voices.length) {
                        return null;
                    }

                    cachedVoice = voices.find(isFemaleVoice) || null;

                    return cachedVoice;
                };

                const speak = (text, { pitch = 1.1, rate = 1, onend } = {}) => {
                    if (!text || !('speechSynthesis' in window)) {
                        if (typeof onend === 'function') {
                            onend();
                        }
                        return;
                    }

                    const attemptSpeak = (retry = 0) => {
                        const voice = pickVoice();
                        if (!voice) {
                            if (retry < 10) {
                                setTimeout(() => attemptSpeak(retry + 1), 200);
                                forceLoadVoices();
                            } else if (typeof onend === 'function') {
                                onend();
                            }
                            return;
                        }

                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.voice = voice;
                        utterance.pitch = pitch;
                        utterance.rate = rate;

                        if (typeof onend === 'function') {
                            utterance.onend = onend;
                        }

                        synth.cancel();
                        synth.speak(utterance);
                    };

                    attemptSpeak();
                };

                synth.onvoiceschanged = () => {
                    cachedVoice = null;
                    pickVoice();
                };

                return { speak };
            })();
        </script>
        @stack('scripts')
    </body>
</html>
