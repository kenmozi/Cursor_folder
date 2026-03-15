<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-SONABE — {{ __('messages.register') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-3">
            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">⚡</div>
            <div class="text-left">
                <div class="text-white font-bold text-2xl leading-none">e-SONABE</div>
                <div class="text-blue-200 text-sm">Énergie & Services</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        {{-- Language toggle --}}
        <div class="flex justify-end gap-2 mb-4">
            <a href="{{ route('locale.switch', 'fr') }}" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'fr' ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-400 hover:text-gray-600' }}">FR</a>
            <a href="{{ route('locale.switch', 'en') }}" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('messages.register') }}</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.name') }} *</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Jean Dupont">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.email') }} *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="jean@example.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.phone') }}</label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="+237 6XX XXX XXX">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.password') }} *</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-lg transition-colors">
                {{ __('messages.register') }}
            </button>
        </form>

        <div class="text-center mt-4 text-sm text-gray-500">
            {{ __('messages.already_registered') }}
            <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">{{ __('messages.login') }}</a>
        </div>
    </div>
</div>

</body>
</html>
