{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RH & Financeiro') }} - Acesso</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .login-bg {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 30%, #4338ca 60%, #1e1b4b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            position: relative;
        }

        .login-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            z-index: 1;
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
        }

        .logo-pulse {
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Padrão geométrico no fundo */
        .pattern-dots {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.05;
            background-image: radial-gradient(circle, #fff 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased login-bg">
    {{-- Padrão de fundo --}}
    <div class="pattern-dots"></div>

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-9 relative z-10">

        {{-- Logo e Nome do Sistema --}}
        <div class="mb-4 text-center">
            <a href="#" class="inline-block logo-pulse">
                <div
                    class="w-24 h-24 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl shadow-2xl flex items-center justify-center mb-5 border border-white/20">
                    <svg class="w-12 h-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
            </a>
            <h1 class="text-3xl font-bold text-white tracking-tight">{{ config('app.name', 'RH & Financeiro') }}</h1>
            <p class="text-indigo-200 text-sm mt-2 font-medium">Sistema de Gestão Empresarial</p>
        </div>

        {{-- Card de Login --}}
        <div class="w-full max-w-md glass-card rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header do Card --}}
            <div
                class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 px-6 py-4 relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5"></div>
                <div class="relative flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-white">Acesso Restrito</h2>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="px-8 py-6">
                {{ $slot }}
            </div>

            {{-- Footer do Card --}}
            <div class="px-8 py-4 bg-gray-50/80 border-t border-gray-100 text-center space-y-1">
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'RH & Financeiro') }}
                </p>
                <p class="text-xs text-gray-300">
                    🔒 Acesso exclusivo para funcionários autorizados
                </p>
            </div>
        </div>

        {{-- Rodapé --}}
        <div class="mt-8 text-center space-y-2">
            <div class="flex items-center justify-center gap-2 text-indigo-200/60 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Conexão segura • Dados criptografados</span>
            </div>
        </div>
    </div>
</body>

</html>
