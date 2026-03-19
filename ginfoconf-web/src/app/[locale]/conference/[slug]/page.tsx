import { getTranslations } from 'next-intl/server';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { Calendar, MapPin, Globe, Users, ArrowRight, ChevronRight, FileText, Phone } from 'lucide-react';
import { Navbar } from '@/components/layout/Navbar';
import { Badge } from '@/components/ui/Badge';
import { publicApi } from '@/lib/api';
import { formatDate, cn } from '@/lib/utils';
import type { Metadata } from 'next';

export async function generateMetadata({
  params,
}: {
  params: { locale: string; slug: string };
}): Promise<Metadata> {
  try {
    const conf = await publicApi.getConference(params.slug);
    return { title: conf.title ?? conf.slug };
  } catch {
    return { title: 'Conference' };
  }
}

export default async function ConferencePage({ params }: { params: { locale: string; slug: string } }) {
  const { locale, slug } = params;
  const t = await getTranslations({ locale, namespace: 'conference' });

  let conf;
  try {
    conf = await publicApi.getConference(slug);
  } catch {
    notFound();
  }

  const sections = [
    { id: 'overview', label: t('overview') },
    { id: 'cfp', label: t('cfp') },
    { id: 'topics', label: t('topics') },
    { id: 'dates', label: t('dates') },
    { id: 'committee', label: t('committee') },
    { id: 'venue', label: t('venue') },
  ];

  return (
    <>
      <Navbar />
      <main>
        {/* Hero */}
        <div
          className="relative min-h-[400px] flex items-end bg-hero-gradient"
          style={
            conf.cover
              ? {
                  backgroundImage: `linear-gradient(to top, rgba(15,12,50,0.9) 0%, rgba(15,12,50,0.4) 60%, transparent 100%), url(${conf.cover})`,
                  backgroundSize: 'cover',
                  backgroundPosition: 'center',
                }
              : undefined
          }
        >
          <div className="page-container pb-12 pt-32">
            <div className="flex items-start gap-4">
              {conf.logo && (
                <img src={conf.logo} alt="Logo" className="h-16 w-16 rounded-xl object-contain bg-white/10 p-1" />
              )}
              <div>
                <div className="flex items-center gap-3 mb-3">
                  <Badge status={conf.status} />
                  {conf.is_submission_open && (
                    <span className="badge bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                      {t('submission_open')}
                    </span>
                  )}
                </div>
                <h1 className="text-3xl md:text-5xl font-extrabold text-white leading-tight">
                  {conf.title ?? slug}
                </h1>
                {conf.subtitle && (
                  <p className="mt-2 text-lg text-white/60">{conf.subtitle}</p>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Sticky nav + content */}
        <div className="border-b border-slate-200 bg-white sticky top-16 z-30 shadow-sm">
          <div className="page-container">
            <nav className="flex overflow-x-auto gap-1 -mb-px">
              {sections.map((s) => (
                <a
                  key={s.id}
                  href={`#${s.id}`}
                  className="shrink-0 px-4 py-3 text-sm font-medium text-slate-500 hover:text-brand-600 border-b-2 border-transparent hover:border-brand-600 transition-colors"
                >
                  {s.label}
                </a>
              ))}
            </nav>
          </div>
        </div>

        <div className="page-container py-12">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {/* Main content */}
            <div className="lg:col-span-2 space-y-16">
              {/* Overview */}
              <section id="overview">
                <h2 className="section-heading">{t('overview')}</h2>
                <div className="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
                  {conf.description ?? 'Conference description coming soon.'}
                </div>
              </section>

              {/* CFP */}
              {conf.cfp_text && (
                <section id="cfp">
                  <h2 className="section-heading">{t('cfp')}</h2>
                  <div className="card p-6">
                    <div className="prose prose-slate max-w-none text-slate-600 whitespace-pre-line text-sm leading-relaxed">
                      {conf.cfp_text}
                    </div>
                  </div>
                </section>
              )}

              {/* Topics */}
              {conf.tracks && conf.tracks.length > 0 && (
                <section id="topics">
                  <h2 className="section-heading">{t('topics')}</h2>
                  <div className="space-y-4">
                    {conf.tracks.map((track) => (
                      <div key={track.id} className="card p-5">
                        <h3 className="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                          <span className="h-2 w-2 rounded-full bg-brand-500" />
                          {track.name}
                        </h3>
                        {track.topics && track.topics.length > 0 && (
                          <ul className="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            {track.topics.map((topic) => (
                              <li key={topic.id} className="flex items-center gap-1.5 text-sm text-slate-600">
                                <ChevronRight className="h-3.5 w-3.5 text-brand-400 shrink-0" />
                                {topic.name}
                              </li>
                            ))}
                          </ul>
                        )}
                      </div>
                    ))}
                  </div>
                </section>
              )}

              {/* Dates */}
              {conf.dates && conf.dates.length > 0 && (
                <section id="dates">
                  <h2 className="section-heading">{t('dates')}</h2>
                  <div className="relative pl-6">
                    <div className="absolute left-2 top-0 bottom-0 w-0.5 bg-brand-100" />
                    {conf.dates.map((d, i) => (
                      <div key={d.id} className="relative mb-6 pl-6">
                        <div className="absolute -left-4 top-1 h-4 w-4 rounded-full border-2 border-brand-500 bg-white" />
                        <p className="text-xs font-medium text-brand-600 uppercase tracking-wide mb-0.5">
                          {d.label ?? d.key}
                        </p>
                        <p className="text-base font-semibold text-slate-800">{formatDate(d.date, 'MMMM d, yyyy')}</p>
                      </div>
                    ))}
                  </div>
                </section>
              )}

              {/* Committee */}
              {conf.committee_members && conf.committee_members.length > 0 && (
                <section id="committee">
                  <h2 className="section-heading">{t('committee')}</h2>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {conf.committee_members.filter((m) => m.is_public).map((member) => (
                      <div key={member.id} className="card p-4 flex items-center gap-3">
                        <div className="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-semibold shrink-0">
                          {member.name[0]}
                        </div>
                        <div>
                          <p className="font-medium text-slate-800">{member.name}</p>
                          {member.affiliation && (
                            <p className="text-xs text-slate-500">{member.affiliation}</p>
                          )}
                          {member.role && (
                            <span className="badge bg-brand-50 text-brand-700 mt-1">{member.role}</span>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </section>
              )}

              {/* Venue */}
              <section id="venue">
                <h2 className="section-heading">{t('venue')}</h2>
                <div className="card p-5 flex items-start gap-3">
                  <MapPin className="h-5 w-5 text-brand-500 mt-0.5 shrink-0" />
                  <div>
                    <p className="font-medium text-slate-800">{conf.location ?? 'Location to be announced'}</p>
                    {conf.website_url && (
                      <a href={conf.website_url} target="_blank" rel="noopener" className="text-sm text-brand-600 hover:underline mt-1 flex items-center gap-1">
                        <Globe className="h-3.5 w-3.5" />
                        {conf.website_url}
                      </a>
                    )}
                  </div>
                </div>
              </section>

              {/* FAQ placeholder */}
              <section id="faq">
                <h2 className="section-heading">FAQ</h2>
                <div className="card divide-y divide-slate-100">
                  {[
                    { q: 'What is the page limit?', a: 'Papers are limited to 10 pages in the IEEE two-column format, including references.' },
                    { q: 'Can I submit to multiple tracks?', a: 'Yes, you may submit to multiple tracks, but each submission counts independently.' },
                    { q: 'Is it double-blind?', a: conf.blind_mode === 'double' ? 'Yes, this conference uses double-blind review. Remove all author information from your submission.' : 'This conference does not use blind review.' },
                  ].map((item, i) => (
                    <details key={i} className="group p-5 cursor-pointer">
                      <summary className="font-medium text-slate-800 flex items-center justify-between list-none">
                        {item.q}
                        <ChevronRight className="h-4 w-4 text-slate-400 group-open:rotate-90 transition-transform" />
                      </summary>
                      <p className="mt-3 text-sm text-slate-500">{item.a}</p>
                    </details>
                  ))}
                </div>
              </section>
            </div>

            {/* Sidebar */}
            <div className="space-y-5">
              {/* CTA card */}
              <div className="card p-6 bg-brand-950 text-white">
                <h3 className="font-bold text-lg mb-2">Submit Your Paper</h3>
                <p className="text-sm text-white/60 mb-5">
                  {conf.is_submission_open
                    ? `Deadline: ${formatDate(conf.submission_close, 'PPP')}`
                    : 'Submissions are currently closed.'}
                </p>
                {conf.is_submission_open ? (
                  <Link
                    href={`/${locale}/dashboard/author/submissions/new?conference=${slug}`}
                    className="btn btn-lg bg-brand-500 hover:bg-brand-400 text-white w-full justify-center"
                  >
                    <FileText className="h-4 w-4" />
                    {t('submit_cta')}
                  </Link>
                ) : (
                  <Link href={`/${locale}/login`} className="btn btn-lg bg-white/10 hover:bg-white/20 text-white w-full justify-center">
                    {t('sign_in_cta')}
                  </Link>
                )}
              </div>

              {/* Quick dates */}
              {conf.dates && conf.dates.length > 0 && (
                <div className="card p-5">
                  <h3 className="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <Calendar className="h-4 w-4 text-brand-500" />
                    Key Dates
                  </h3>
                  <div className="space-y-3">
                    {conf.dates.slice(0, 4).map((d) => (
                      <div key={d.id} className="flex justify-between items-start text-sm">
                        <span className="text-slate-500">{d.label ?? d.key}</span>
                        <span className="font-medium text-slate-800 text-right">{formatDate(d.date, 'MMM d, yyyy')}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Contact */}
              <div className="card p-5" id="contact">
                <h3 className="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                  <Phone className="h-4 w-4 text-brand-500" />
                  {t('contact')}
                </h3>
                <p className="text-sm text-slate-500">
                  For inquiries, contact the program committee via the conference website.
                </p>
                {conf.website_url && (
                  <a href={conf.website_url} target="_blank" rel="noopener" className="btn-secondary btn-sm mt-3 w-full justify-center">
                    Visit Website
                    <ArrowRight className="h-3.5 w-3.5" />
                  </a>
                )}
              </div>
            </div>
          </div>
        </div>
      </main>
    </>
  );
}
