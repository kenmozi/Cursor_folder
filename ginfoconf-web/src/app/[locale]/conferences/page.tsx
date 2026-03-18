import { getTranslations } from 'next-intl/server';
import Link from 'next/link';
import { ArrowRight, Calendar, MapPin } from 'lucide-react';
import { Navbar } from '@/components/layout/Navbar';
import { Badge } from '@/components/ui/Badge';
import { publicApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import type { ConferencePublic } from '@/types';
import type { Metadata } from 'next';

export async function generateMetadata({ params }: { params: { locale: string } }): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: 'conferences' });
  return { title: t('title') };
}

export default async function ConferencesPage({ params }: { params: { locale: string } }) {
  const { locale } = params;
  const t = await getTranslations({ locale, namespace: 'conferences' });

  let conferences: ConferencePublic[] = [];
  try {
    const res = await publicApi.listConferences();
    conferences = res.data;
  } catch {
    // show empty state
  }

  return (
    <>
      <Navbar />
      <main className="py-12">
        <div className="page-container">
          {/* Header */}
          <div className="mb-10">
            <h1 className="text-4xl font-extrabold text-slate-900">{t('title')}</h1>
            <p className="mt-2 text-lg text-slate-500">{t('subtitle')}</p>
          </div>

          {conferences.length === 0 ? (
            <div className="card p-16 text-center">
              <p className="text-slate-400">{t('empty')}</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              {conferences.map((conf) => (
                <ConferenceCard key={conf.slug} conf={conf} locale={locale} t={t} />
              ))}
            </div>
          )}
        </div>
      </main>
    </>
  );
}

function ConferenceCard({
  conf,
  locale,
  t,
}: {
  conf: ConferencePublic;
  locale: string;
  t: Awaited<ReturnType<typeof getTranslations>>;
}) {
  return (
    <div className="card-hover flex flex-col overflow-hidden">
      {/* Cover */}
      <div
        className="h-36 bg-hero-gradient"
        style={conf.cover ? { backgroundImage: `url(${conf.cover})`, backgroundSize: 'cover' } : undefined}
      />

      <div className="flex flex-1 flex-col p-5">
        {/* Badges */}
        <div className="flex items-center gap-2 mb-3">
          <Badge status={conf.status} />
          {conf.is_submission_open && (
            <span className="badge bg-emerald-100 text-emerald-700">{t('open_badge')}</span>
          )}
        </div>

        <h2 className="font-bold text-slate-900 leading-snug mb-1">
          {conf.title ?? conf.slug}
        </h2>
        {conf.subtitle && <p className="text-xs text-slate-400 mb-3">{conf.subtitle}</p>}

        <p className="text-sm text-slate-500 line-clamp-2 mb-4">
          {conf.description ?? ''}
        </p>

        {/* Meta */}
        <div className="mt-auto space-y-1.5 text-xs text-slate-500">
          {conf.location && (
            <div className="flex items-center gap-1.5">
              <MapPin className="h-3.5 w-3.5 shrink-0" />
              {conf.location}
            </div>
          )}
          {conf.submission_close && (
            <div className="flex items-center gap-1.5">
              <Calendar className="h-3.5 w-3.5 shrink-0" />
              {t('deadline')}: <strong>{formatDate(conf.submission_close, 'PP')}</strong>
            </div>
          )}
        </div>

        {/* CTA */}
        <div className="mt-5 pt-4 border-t border-slate-100 flex gap-2">
          <Link href={`/${locale}/conference/${conf.slug}`} className="btn-secondary btn-sm flex-1 justify-center">
            {t('view_details')}
          </Link>
          {conf.is_submission_open && (
            <Link href={`/${locale}/dashboard/author/submissions/new?conference=${conf.slug}`} className="btn-primary btn-sm flex-1 justify-center">
              {t('submit_paper')}
            </Link>
          )}
        </div>
      </div>
    </div>
  );
}
