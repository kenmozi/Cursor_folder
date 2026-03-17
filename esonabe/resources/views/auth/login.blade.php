<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-SONABE — {{ __('messages.login') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif; }
        .form-input {
            width:100%; border:1.5px solid #d1d5db; border-radius:8px;
            padding:9px 13px; font-size:.875rem; color:#111; outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .form-input:focus { border-color:#D32F2F; box-shadow:0 0 0 3px rgba(211,47,47,.12); }
        .btn-login {
            width:100%; background:#D32F2F; color:#fff; font-weight:700;
            padding:11px; border-radius:8px; border:none; cursor:pointer;
            font-size:.9rem; transition:background .15s; letter-spacing:.3px;
        }
        .btn-login:hover { background:#b71c1c; }
        .divider { height:1px; background:#f0f0f0; margin:20px 0; }
    </style>
</head>
<body style="background:linear-gradient(135deg,#1a1a1a 0%,#2E7D32 50%,#D32F2F 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;">

    <div style="width:100%; max-width:420px;">

        {{-- Logo card --}}
        <div style="text-align:center; margin-bottom:28px;">
            <div style="display:inline-flex; align-items:center; gap:16px; background:rgba(255,255,255,.1); backdrop-filter:blur(10px); padding:16px 28px; border-radius:16px; border:1px solid rgba(255,255,255,.15);">
                <img src="/images/sonabel-logo.svg" alt="SONABEL" style="width:72px; height:auto; background:#fff; border-radius:8px; padding:4px;">
                <div style="text-align:left;">
                    <div style="font-size:1.5rem; font-weight:800; color:#fff; line-height:1.1; letter-spacing:.5px;">e-SONABE</div>
                    <div style="font-size:.78rem; color:#FFD600; font-weight:600; letter-spacing:.3px;">Espace Client</div>
                    <div style="font-size:.68rem; color:rgba(255,255,255,.55); margin-top:2px;">Société Nationale d'Électricité</div>
                </div>
            </div>
        </div>

        {{-- Form card --}}
        <div style="background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden;">

            {{-- Card top accent bar --}}
            <div style="height:4px; background:linear-gradient(90deg, #D32F2F, #FFD600, #2E7D32);"></div>

            <div style="padding:32px;">
                {{-- Language --}}
                <div style="display:flex; justify-content:flex-end; gap:6px; margin-bottom:20px;">
                    <a href="{{ route('locale.switch', 'fr') }}"
                       style="font-size:.72rem; padding:3px 10px; border-radius:5px; font-weight:700; text-decoration:none; border:1.5px solid;
                              {{ app()->getLocale()==='fr' ? 'background:#D32F2F; color:#fff; border-color:#D32F2F;' : 'color:#9ca3af; border-color:#e5e7eb;' }}">FR</a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       style="font-size:.72rem; padding:3px 10px; border-radius:5px; font-weight:700; text-decoration:none; border:1.5px solid;
                              {{ app()->getLocale()==='en' ? 'background:#D32F2F; color:#fff; border-color:#D32F2F;' : 'color:#9ca3af; border-color:#e5e7eb;' }}">EN</a>
                </div>

                <h2 style="margin:0 0 4px; font-size:1.35rem; font-weight:800; color:#111;">{{ __('messages.login') }}</h2>
                <p style="margin:0 0 24px; font-size:.83rem; color:#6b7280;">Accédez à votre espace client sécurisé</p>

                @if(session('status'))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:10px 14px; border-radius:8px; font-size:.83rem; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px;">
                            {{ __('messages.email') }}
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="form-input" style="padding-left:36px;" placeholder="votre@email.com">
                        </div>
                        @error('email') <p style="color:#dc2626; font-size:.75rem; margin:4px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom:20px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:5px;">
                            <label style="font-size:.82rem; font-weight:600; color:#374151;">{{ __('messages.password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" style="font-size:.75rem; color:#D32F2F; text-decoration:none; font-weight:500;">{{ __('messages.forgot_password') }}</a>
                            @endif
                        </div>
                        <div style="position:relative;">
                            <span style="position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" name="password" required class="form-input" style="padding-left:36px;" placeholder="••••••••">
                        </div>
                        @error('password') <p style="color:#dc2626; font-size:.75rem; margin:4px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                        <input type="checkbox" name="remember" id="remember" style="width:15px;height:15px;accent-color:#D32F2F;">
                        <label for="remember" style="font-size:.82rem; color:#6b7280; cursor:pointer;">{{ __('messages.remember_me') }}</label>
                    </div>

                    <button type="submit" class="btn-login">{{ __('messages.login') }}</button>
                </form>

                <div class="divider"></div>

                <p style="text-align:center; font-size:.83rem; color:#6b7280; margin:0;">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" style="color:#D32F2F; font-weight:700; text-decoration:none;">{{ __('messages.register') }}</a>
                </p>
            </div>
        </div>

        <p style="text-align:center; color:rgba(255,255,255,.4); font-size:.72rem; margin-top:20px;">
            © {{ date('Y') }} SONABEL · Tous droits réservés
        </p>
    </div>

</body>
</html>
