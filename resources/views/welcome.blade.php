<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel Rolodex') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br  min-h-screen">
        <header class="w-full py-4 px-6 lg:px-8">
            @if (Route::has('login'))
                <nav class="flex items-center justify-between max-w-7xl mx-auto">
                    <div class="flex items-center">
{{--                        <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">--}}
{{--                            <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>--}}
{{--                        </svg>--}}
                        <svg xmlns="http://www.w3.org/2000/svg"  class="w-10 h-10 text-blue-600"  viewBox="0 -960 960 960">
                            <path d="M120-200q-33 0-56.5-23.5T40-280v-400q0-33 23.5-56.5T120-760h400q33 0 56.5 23.5T600-680v400q0 33-23.5 56.5T520-200H120Zm0-146q44-26 94-40t106-14q56 0 106 14t94 40v-334H120v334Zm200 26q-41 0-80 10t-74 30h308q-35-20-74-30t-80-10Zm0-110q-45 0-77.5-32.5T210-540q0-45 32.5-77.5T320-650q45 0 77.5 32.5T430-540q0 45-32.5 77.5T320-430Zm0-74q15 0 25.5-10.5T356-540q0-15-10.5-25.5T320-576q-15 0-25.5 10.5T284-540q0 15 10.5 25.5T320-504Zm360 304v-560h80v560h-80Zm160 0v-560h80v560h-80ZM320-540Zm0 260Z"/></svg>
                        <span class="ml-2 text-xl font-bold text-gray-800">Rolodex Laravel</span>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-block px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                            >
                                Panel de Control
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-block px-5 py-2 text-gray-700 hover:text-blue-600 transition font-medium"
                            >
                                Iniciar Sesión
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-block px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                                >
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>

        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    Bienvenido al <span class="text-green-300">Gestor Rolodex</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">
                    Esta aplicación te ayudará a gestionar tus contactos de manera eficiente y segura.
                </p>
            </div>

            <!-- Features Section -->
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Registro de usuarios</h3>
                    <p class="text-gray-600">Sistema de regisro de usuarios completo.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Autenticacion Segura Breeze</h3>
                    <p class="text-gray-600">Inicio de sesión segura con posibilida de recuperación de contraseña</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Gestión de perfil</h3>
                    <p class="text-gray-600">Panel de gestion de datos de usuario</p>
                </div>
            </div>
        </main>

        <footer class="max-w-7xl mx-auto px-6 lg:px-8 py-8 text-center text-gray-500">
            <p>&copy; {{ date('Y') }} Implementacion Laravel Breeze - Asignatura Desarrollo Entorno Servidor </p>
            <p class="mt-2 text-sm">
                <a href="https://laravel.com/docs" target="_blank" class="hover:text-blue-600">Laravel Docs</a>
                &middot;
                <a href="https://laravel.com/docs/starter-kits#laravel-breeze" target="_blank" class="hover:text-blue-600">Laravel Breeze</a>
            </p>
        </footer>
    </body>
</html>
