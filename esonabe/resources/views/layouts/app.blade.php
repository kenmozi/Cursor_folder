<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>e-SONABE — @yield('title', __('messages.dashboard'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <!-- Heroicons via unpkg for consistent SVG icons -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Brand tokens ─────────────────────────── */
        :root {
            --sb-red:    #D32F2F;
            --sb-green:  #2E7D32;
            --sb-yellow: #FFD600;
            --sb-dark:   #1a1a1a;
            --sidebar-w: 260px;
        }

        /* ── Sidebar ──────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(175deg, #1b1b1b 0%, #111 100%);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand img {
            width: 56px;
            height: auto;
            border-radius: 6px;
            background: #fff;
            padding: 3px;
        }

        .sidebar-brand-text { line-height: 1.2; }
        .sidebar-brand-text strong {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: .5px;
        }
        .sidebar-brand-text span {
            font-size: .7rem;
            color: #FFD600;
            letter-spacing: .3px;
        }

        /* ── Nav links ────────────────────────────── */
        .nav-section-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.35);
            padding: 16px 20px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            margin: 1px 10px;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 500;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .nav-link.active {
            background: rgba(211,47,47,.25);
            color: #fff;
            border-left: 3px solid var(--sb-red);
            padding-left: 11px;
        }
        .nav-link.active .nav-icon { color: #FFD600; }

        /* Fixed icon sizing — no more misalignment */
        .nav-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: rgba(255,255,255,.5);
        }
        .nav-link:hover .nav-icon { color: rgba(255,255,255,.85); }

        /* ── Utility classes ──────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
            border: 1px solid #f0f0f0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: .7rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-pending  { background:#fef9c3; color:#854d0e; }
        .badge-paid     { background:#dcfce7; color:#166534; }
        .badge-overdue  { background:#fee2e2; color:#991b1b; }
        .badge-cancelled{ background:#f3f4f6; color:#4b5563; }
        .badge-active   { background:#dbeafe; color:#1e40af; }
        .badge-inactive { background:#f3f4f6; color:#6b7280; }

        /* Buttons — always inline-flex so icon+text align */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            transition: filter .15s, box-shadow .15s;
            border: none;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn:hover { filter: brightness(1.08); }
        .btn-primary  { background: var(--sb-red);   color: #fff; }
        .btn-success  { background: var(--sb-green);  color: #fff; }
        .btn-warning  { background: #f59e0b; color: #fff; }
        .btn-secondary{ background: #f3f4f6; color: #374151; }
        .btn-danger   { background: #dc2626; color: #fff; }
        .btn-sm       { padding: 5px 11px; font-size: .78rem; }
        .btn-lg       { padding: 10px 20px; font-size: .9rem; }
        .btn svg      { width: 15px; height: 15px; flex-shrink: 0; }

        /* Form controls */
        .form-label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: .85rem;
            color: #111;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-input:focus { border-color: var(--sb-red); box-shadow: 0 0 0 3px rgba(211,47,47,.15); }

        /* Card */
        .card { background:#fff; border-radius:12px; border:1px solid #ebebeb; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
        .card-header {
            padding: 14px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .card-header h3 { font-size:.9rem; font-weight:700; color:#111; margin:0; }
        .card-body { padding: 20px; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table thead tr { background: #fafafa; border-bottom: 2px solid #f0f0f0; }
        table.data-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #6b7280;
            white-space: nowrap;
        }
        table.data-table td { padding: 11px 14px; font-size: .84rem; color: #374151; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
        table.data-table tbody tr:last-child td { border-bottom: none; }
        table.data-table tbody tr:hover { background: #fafafa; }

        /* Flash messages */
        .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:.84rem; margin-bottom:16px; }
        .alert svg { width:18px; height:18px; flex-shrink:0; margin-top:1px; }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50" style="font-family:'Figtree',sans-serif;">

<div style="display:flex; height:100vh; overflow:hidden;">

    {{-- ═══════════════ SIDEBAR ═══════════════ --}}
    <aside class="sidebar">

        {{-- Brand / Logo --}}
        <div class="sidebar-brand">
            <img src="/images/sonabel-logo.svg" alt="SONABEL Logo">
            <div class="sidebar-brand-text">
                <strong>e-SONABE</strong>
                <span>Espace Client</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav style="flex:1; padding:8px 0; overflow-y:auto;">
            <div class="nav-section-label">Navigation</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ __('messages.dashboard') }}</span>
            </a>

            <a href="{{ route('contracts.index') }}"
               class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>{{ __('messages.contracts') }}</span>
            </a>

            <a href="{{ route('bills.index') }}"
               class="nav-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ __('messages.bills') }}</span>
            </a>

            <a href="{{ route('cashpower.index') }}"
               class="nav-link {{ request()->routeIs('cashpower.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>{{ __('messages.cashpower') }}</span>
            </a>

            <div class="nav-section-label" style="margin-top:8px;">Compte</div>

            <a href="{{ route('profile.edit') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>{{ __('messages.profile') }}</span>
            </a>
        </nav>

        {{-- Bottom: locale + logout --}}
        <div style="padding:12px 10px; border-top:1px solid rgba(255,255,255,.08);">

            {{-- Language toggle --}}
            <div style="display:flex; align-items:center; gap:6px; padding:6px 10px 10px;">
                <svg style="width:14px;height:14px;color:rgba(255,255,255,.4);flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
                <a href="{{ route('locale.switch', 'fr') }}"
                   style="font-size:.72rem; padding:3px 8px; border-radius:5px; font-weight:700; text-decoration:none;
                          {{ app()->getLocale()==='fr' ? 'background:#FFD600;color:#111;' : 'color:rgba(255,255,255,.45);' }}">FR</a>
                <a href="{{ route('locale.switch', 'en') }}"
                   style="font-size:.72rem; padding:3px 8px; border-radius:5px; font-weight:700; text-decoration:none;
                          {{ app()->getLocale()==='en' ? 'background:#FFD600;color:#111;' : 'color:rgba(255,255,255,.45);' }}">EN</a>
            </div>

            {{-- User info --}}
            <div style="display:flex; align-items:center; gap:9px; padding:8px 10px; border-radius:8px; background:rgba(255,255,255,.05);">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--sb-red);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="min-width:0;flex:1;">
                    <div style="font-size:.8rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Auth::user()->name }}
                    </div>
                    <div style="font-size:.68rem;color:rgba(255,255,255,.4);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Auth::user()->email }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:6px;">
                @csrf
                <button type="submit" class="nav-link" style="width:100%;background:none;cursor:pointer;border:none;text-align:left;">
                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('messages.logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════ MAIN AREA ═══════════════ --}}
    <div style="flex:1; display:flex; flex-direction:column; min-height:0; overflow:hidden;">

        {{-- Top bar --}}
        <header style="background:#fff; border-bottom:1px solid #ebebeb; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div>
                @hasSection('title')
                    <h1 style="margin:0; font-size:1.05rem; font-weight:800; color:#111;">@yield('title')</h1>
                @else
                    <h1 style="margin:0; font-size:1.05rem; font-weight:800; color:#111;">{{ __('messages.dashboard') }}</h1>
                @endif
                @hasSection('subtitle')
                    <p style="margin:2px 0 0; font-size:.78rem; color:#9ca3af;">@yield('subtitle')</p>
                @endif
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash messages --}}
        <div style="padding:0 24px;">
            @if(session('success'))
                <div class="alert alert-success" style="margin-top:16px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-top:16px;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main style="flex:1; overflow-y:auto; padding:20px 24px 40px;">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
