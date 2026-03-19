import { useTranslations } from 'next-intl';
import { getTranslations } from 'next-intl/server';
import Link from 'next/link';
import { ArrowRight, Globe, Shield, Users, Zap } from 'lucide-react';
import { Navbar } from '@/components/layout/Navbar';
import type { Metadata } from 'next';

export async function generateMetadata({ params }: { params: { locale: string } }): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: 'home' });
  return { title: t('hero_title') };
}

export default function HomePage({ params }: { params: { locale: string } }) {
  const t = useTranslations('home');
  const n = useTranslations('nav');
  const { locale } = params;

  const features = [
    { icon: Users, key: 'feature_1' },
    { icon: Shield, key: 'feature_2' },
    { icon: Globe, key: 'feature_3' },
    { icon: Zap, key: 'feature_4' },
  ] as const;

  return (
    <>
      <Navbar />
      <main>
        {/* Hero */}
        <section className="bg-hero-gradient text-white py-24 md:py-36">
          <div className="page-container text-center">
            <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm text-white/80 mb-8 backdrop-blur">
              <span className="h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse" />
              Modern alternative to EasyChair
            </div>
            <h1 className="text-4xl md:text-6xl font-extrabold leading-tight text-white mb-6">
              {t('hero_title')}
            </h1>
            <p className="text-lg md:text-xl text-white/70 max-w-2xl mx-auto mb-10 leading-relaxed">
              {t('hero_subtitle')}
            </p>
            <div className="flex flex-wrap items-center justify-center gap-4">
              <Link href={`/${locale}/conferences`} className="btn btn-lg bg-white text-brand-700 hover:bg-brand-50 font-semibold shadow-lg">
                {t('cta_browse')}
                <ArrowRight className="h-4 w-4" />
              </Link>
              <Link href={`/${locale}/register`} className="btn btn-lg border-2 border-white/40 text-white hover:bg-white/10 font-semibold">
                {t('cta_register')}
              </Link>
            </div>
          </div>
        </section>

        {/* Features */}
        <section className="py-20 bg-white">
          <div className="page-container">
            <h2 className="text-center text-3xl font-extrabold text-slate-900 mb-14">{t('features_title')}</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {features.map(({ icon: Icon, key }) => (
                <div key={key} className="card-hover p-6 group">
                  <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <Icon className="h-6 w-6" />
                  </div>
                  <h3 className="font-semibold text-slate-800 mb-2">
                    {t(`${key}_title` as any)}
                  </h3>
                  <p className="text-sm text-slate-500 leading-relaxed">
                    {t(`${key}_desc` as any)}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* CTA Strip */}
        <section className="py-16 bg-brand-950 text-white">
          <div className="page-container flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
              <h2 className="text-2xl font-bold text-white">Ready to submit your research?</h2>
              <p className="mt-1 text-white/60">Browse open conferences and get started today.</p>
            </div>
            <Link href={`/${locale}/conferences`} className="btn btn-lg bg-brand-500 hover:bg-brand-400 text-white shrink-0">
              Browse Conferences
              <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </section>
      </main>

      <footer className="bg-slate-900 text-slate-400 py-10">
        <div className="page-container flex flex-col md:flex-row items-center justify-between gap-4">
          <span className="font-bold text-white text-lg">ginfoconf</span>
          <p className="text-sm">© {new Date().getFullYear()} ginfoconf. Open source conference platform.</p>
        </div>
      </footer>
    </>
  );
}
