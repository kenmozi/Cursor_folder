'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useTranslations, useLocale } from 'next-intl';
import { Globe, LogOut, Menu, User, X } from 'lucide-react';
import { useState } from 'react';
import { useAuthStore } from '@/store/auth';
import { authApi } from '@/lib/api';
import { cn } from '@/lib/utils';
import { locales, type Locale } from '@/lib/i18n';

const LOCALE_LABELS: Record<Locale, string> = { en: 'EN', fr: 'FR', ja: 'JP' };

export function Navbar() {
  const t = useTranslations('nav');
  const locale = useLocale() as Locale;
  const pathname = usePathname();
  const router = useRouter();
  const { user, clearAuth } = useAuthStore();
  const [open, setOpen] = useState(false);

  const pathnameWithoutLocale = pathname.replace(`/${locale}`, '') || '/';

  const handleLogout = async () => {
    await authApi.logout().catch(() => {});
    clearAuth();
    router.push(`/${locale}/login`);
  };

  const switchLocale = (newLocale: Locale) => {
    router.push(`/${newLocale}${pathnameWithoutLocale}`);
  };

  return (
    <header className="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
      <div className="page-container">
        <div className="flex h-16 items-center justify-between">
          {/* Logo */}
          <Link href={`/${locale}`} className="flex items-center gap-2">
            <span className="text-xl font-bold text-brand-700">ginfoconf</span>
          </Link>

          {/* Desktop nav */}
          <nav className="hidden md:flex items-center gap-1">
            <Link href={`/${locale}/conferences`} className="btn-ghost btn-sm">{t('conferences')}</Link>
            {user ? (
              <>
                <Link href={`/${locale}/dashboard/author`} className="btn-ghost btn-sm">{t('dashboard')}</Link>
                <button onClick={handleLogout} className="btn-ghost btn-sm flex items-center gap-1.5">
                  <LogOut className="h-4 w-4" />
                  {t('logout')}
                </button>
              </>
            ) : (
              <>
                <Link href={`/${locale}/login`} className="btn-ghost btn-sm">{t('login')}</Link>
                <Link href={`/${locale}/register`} className="btn-primary btn-sm">{t('register')}</Link>
              </>
            )}

            {/* Locale switcher */}
            <div className="relative ml-2 flex items-center gap-0.5 rounded-lg border border-slate-200 p-0.5">
              <Globe className="ml-1.5 h-3.5 w-3.5 text-slate-400" />
              {locales.map((l) => (
                <button
                  key={l}
                  onClick={() => switchLocale(l)}
                  className={cn(
                    'rounded px-2 py-1 text-xs font-medium transition-colors',
                    l === locale
                      ? 'bg-brand-600 text-white'
                      : 'text-slate-500 hover:text-slate-900'
                  )}
                >
                  {LOCALE_LABELS[l]}
                </button>
              ))}
            </div>
          </nav>

          {/* Mobile menu button */}
          <button className="md:hidden btn-ghost btn-sm" onClick={() => setOpen(!open)}>
            {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      {open && (
        <div className="md:hidden border-t border-slate-100 bg-white animate-slide-up">
          <div className="page-container py-3 flex flex-col gap-1">
            <Link href={`/${locale}/conferences`} className="sidebar-link" onClick={() => setOpen(false)}>{t('conferences')}</Link>
            {user ? (
              <>
                <Link href={`/${locale}/dashboard/author`} className="sidebar-link" onClick={() => setOpen(false)}>{t('dashboard')}</Link>
                <button onClick={handleLogout} className="sidebar-link text-left">{t('logout')}</button>
              </>
            ) : (
              <>
                <Link href={`/${locale}/login`} className="sidebar-link" onClick={() => setOpen(false)}>{t('login')}</Link>
                <Link href={`/${locale}/register`} className="sidebar-link" onClick={() => setOpen(false)}>{t('register')}</Link>
              </>
            )}
          </div>
        </div>
      )}
    </header>
  );
}
