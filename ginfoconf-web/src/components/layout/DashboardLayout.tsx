'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useLocale } from 'next-intl';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/store/auth';
import { authApi } from '@/lib/api';
import { cn } from '@/lib/utils';
import { LogOut, ChevronDown, Bell, User } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { useState } from 'react';

interface NavItem {
  label: string;
  href: string;
  icon: LucideIcon;
}

interface DashboardLayoutProps {
  children: React.ReactNode;
  title: string;
  navItems: NavItem[];
}

export function DashboardLayout({ children, title, navItems }: DashboardLayoutProps) {
  const locale = useLocale();
  const pathname = usePathname();
  const router = useRouter();
  const { user, clearAuth } = useAuthStore();
  const [sidebarOpen, setSidebarOpen] = useState(true);

  const handleLogout = async () => {
    await authApi.logout().catch(() => {});
    clearAuth();
    router.push(`/${locale}/login`);
  };

  return (
    <div className="flex h-screen bg-slate-50 overflow-hidden">
      {/* Sidebar */}
      <aside className={cn(
        'flex flex-col border-r border-slate-200 bg-white transition-all duration-200',
        sidebarOpen ? 'w-64' : 'w-16'
      )}>
        {/* Logo */}
        <div className="flex h-16 items-center border-b border-slate-100 px-4">
          <Link href={`/${locale}`} className={cn('font-bold text-brand-700', !sidebarOpen && 'hidden')}>
            ginfoconf
          </Link>
          {!sidebarOpen && <span className="text-brand-700 font-bold text-xl">g</span>}
        </div>

        {/* Nav */}
        <nav className="flex-1 overflow-y-auto py-4 px-2">
          {navItems.map((item) => {
            const isActive = pathname.startsWith(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                className={cn(isActive ? 'sidebar-link-active' : 'sidebar-link', 'mb-1')}
                title={!sidebarOpen ? item.label : undefined}
              >
                <item.icon className="h-4 w-4 shrink-0" />
                {sidebarOpen && <span>{item.label}</span>}
              </Link>
            );
          })}
        </nav>

        {/* Footer */}
        <div className="border-t border-slate-100 p-2">
          <button
            onClick={handleLogout}
            className={cn('sidebar-link w-full', !sidebarOpen && 'justify-center')}
            title={!sidebarOpen ? 'Sign Out' : undefined}
          >
            <LogOut className="h-4 w-4 shrink-0" />
            {sidebarOpen && <span>Sign Out</span>}
          </button>
        </div>
      </aside>

      {/* Main */}
      <div className="flex flex-1 flex-col overflow-hidden">
        {/* Top header */}
        <header className="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-6">
          <div className="flex items-center gap-3">
            <button
              onClick={() => setSidebarOpen(!sidebarOpen)}
              className="btn-ghost btn-sm p-1.5"
            >
              <ChevronDown className={cn('h-4 w-4 transition-transform', !sidebarOpen && '-rotate-90')} />
            </button>
            <h1 className="text-base font-semibold text-slate-800">{title}</h1>
          </div>
          <div className="flex items-center gap-2">
            <button className="btn-ghost btn-sm p-2">
              <Bell className="h-4 w-4" />
            </button>
            <div className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5">
              <div className="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-brand-700 text-xs font-semibold">
                {user?.name?.[0]?.toUpperCase() ?? 'U'}
              </div>
              <span className="text-sm font-medium text-slate-700 hidden sm:block">{user?.name}</span>
            </div>
          </div>
        </header>

        {/* Content */}
        <main className="flex-1 overflow-y-auto">
          {children}
        </main>
      </div>
    </div>
  );
}
