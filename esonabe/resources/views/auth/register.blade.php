<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>e-SONABE — {{ __('messages.register') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif; }
        .form-input {
            width:100%; border:1.5px solid #d1d5db; border-radius:8px;
            padding:9px 13px 9px 36px; font-size:.875rem; color:#111; outline:none;
            transition:border-color .15s, box-shadow .15s; box-sizing:border-box;
        }
        .form-input:focus { border-color:#D32F2F; box-shadow:0 0 0 3px rgba(211,47,47,.12); }
        .form-input-nol { padding-left:13px; } /* no left icon */
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af; width:16px; height:16px; }
        .btn-submit {
            width:100%; background:#D32F2F; color:#fff; font-weight:700;
            padding:11px; border-radius:8px; border:none; cursor:pointer;
            font-size:.9rem; transition:background .15s; letter-spacing:.3px;
        }
        .btn-submit:hover { background:#b71c1c; }
        .field { margin-bottom:14px; }
        label.lbl { display:block; font-size:.81rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .err { color:#dc2626; font-size:.74rem; margin:3px 0 0; }
        .divider { height:1px; background:#f0f0f0; margin:18px 0; }
    </style>
</head>
<body style="background:linear-gradient(135deg,#1a1a1a 0%,#2E7D32 50%,#D32F2F 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;">

    <div style="width:100%; max-width:460px;">

        {{-- Logo --}}
        <div style="text-align:center; margin-bottom:24px;">
            <div style="display:inline-flex; align-items:center; gap:16px; background:rgba(255,255,255,.1); backdrop-filter:blur(10px); padding:14px 24px; border-radius:14px; border:1px solid rgba(255,255,255,.15);">
                <img src="/images/sonabel-logo.svg" alt="SONABEL" style="width:64px; height:auto; background:#fff; border-radius:8px; padding:3px;">
                <div style="text-align:left;">
                    <div style="font-size:1.4rem; font-weight:800; color:#fff; line-height:1.1;">e-SONABE</div>
                    <div style="font-size:.75rem; color:#FFD600; font-weight:600;">Espace Client</div>
                </div>
            </div>
        </div>

        {{-- Card --}}
        <div style="background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden;">
            <div style="height:4px; background:linear-gradient(90deg, #D32F2F, #FFD600, #2E7D32);"></div>

            <div style="padding:28px 32px;">
                {{-- Lang --}}
                <div style="display:flex; justify-content:flex-end; gap:6px; margin-bottom:16px;">
                    <a href="{{ route('locale.switch', 'fr') }}"
                       style="font-size:.72rem; padding:3px 10px; border-radius:5px; font-weight:700; text-decoration:none; border:1.5px solid;
                              {{ app()->getLocale()==='fr' ? 'background:#D32F2F; color:#fff; border-color:#D32F2F;' : 'color:#9ca3af; border-color:#e5e7eb;' }}">FR</a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       style="font-size:.72rem; padding:3px 10px; border-radius:5px; font-weight:700; text-decoration:none; border:1.5px solid;
                              {{ app()->getLocale()==='en' ? 'background:#D32F2F; color:#fff; border-color:#D32F2F;' : 'color:#9ca3af; border-color:#e5e7eb;' }}">EN</a>
                </div>

                <h2 style="margin:0 0 4px; font-size:1.25rem; font-weight:800; color:#111;">{{ __('messages.register') }}</h2>
                <p style="margin:0 0 20px; font-size:.82rem; color:#6b7280;">Créez votre espace client e-SONABE</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="field">
                        <label class="lbl">{{ __('messages.name') }} *</label>
                        <div class="input-wrap">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input" placeholder="Jean-Pierre Kamga">
                        </div>
                        @error('name') <p class="err">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="lbl">{{ __('messages.email') }} *</label>
                        <div class="input-wrap">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="jean@example.com">
                        </div>
                        @error('email') <p class="err">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="lbl">{{ __('messages.phone') }}</label>
                        <div class="input-wrap">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+226 70 00 00 00">
                        </div>
                        @error('phone') <p class="err">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="lbl">{{ __('messages.password') }} *</label>
                        <div class="input-wrap">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input type="password" name="password" required class="form-input" placeholder="••••••••">
                        </div>
                        @error('password') <p class="err">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="lbl">Confirmer le mot de passe *</label>
                        <div class="input-wrap">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <input type="password" name="password_confirmation" required class="form-input" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top:6px;">{{ __('messages.register') }}</button>
                </form>

                <div class="divider"></div>

                <p style="text-align:center; font-size:.83rem; color:#6b7280; margin:0;">
                    {{ __('messages.already_registered') }}
                    <a href="{{ route('login') }}" style="color:#D32F2F; font-weight:700; text-decoration:none;">{{ __('messages.login') }}</a>
                </p>
            </div>
        </div>

        <p style="text-align:center; color:rgba(255,255,255,.4); font-size:.72rem; margin-top:20px;">
            © {{ date('Y') }} SONABEL · Tous droits réservés
        </p>
    </div>

</body>
</html>
