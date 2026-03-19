'use client';

import { useLocale } from 'next-intl';
import { usePathname } from 'next/navigation';
import {
  LayoutDashboard,
  Globe,
  FileText,
  Users,
  GitBranch,
  Calendar,
  UserCheck,
  Image,
  Settings,
} from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';

interface AdminLayoutProps {
  children: React.ReactNode;
  title: string;
  conferenceSlug?: string;
}

export function AdminLayout({ children, title, conferenceSlug }: AdminLayoutProps) {
  const locale = useLocale();

  const globalNavItems = [
    { label: 'Dashboard', href: `/${locale}/dashboard/admin`, icon: LayoutDashboard },
    { label: 'Conferences', href: `/${locale}/dashboard/admin/conferences`, icon: Globe },
  ];

  const conferenceNavItems = conferenceSlug
    ? [
        { label: 'Overview', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}`, icon: LayoutDashboard },
        { label: 'Branding', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/branding`, icon: Image },
        { label: 'Dates', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/dates`, icon: Calendar },
        { label: 'Committee', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/committee`, icon: UserCheck },
        { label: 'Reviewers', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/reviewers`, icon: Users },
        { label: 'Submissions', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/submissions`, icon: FileText },
        { label: 'Assignments', href: `/${locale}/dashboard/admin/conferences/${conferenceSlug}/assignments`, icon: GitBranch },
      ]
    : [];

  const navItems = [...globalNavItems, ...conferenceNavItems];

  return (
    <DashboardLayout title={title} navItems={navItems}>
      {children}
    </DashboardLayout>
  );
}
