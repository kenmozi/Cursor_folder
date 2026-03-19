'use client';

import { useTranslations, useLocale } from 'next-intl';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { authApi } from '@/lib/api';
import { useAuthStore } from '@/store/auth';
import { FormField } from '@/components/ui/FormField';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { getErrorMessage } from '@/lib/utils';

const schema = z.object({
  email: z.string().email('Invalid email'),
  password: z.string().min(1, 'Password required'),
});

type FormData = z.infer<typeof schema>;

export default function LoginPage() {
  const t = useTranslations('auth');
  const locale = useLocale();
  const router = useRouter();
  const { setAuth } = useAuthStore();
  const [error, setError] = useState('');

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<FormData>({ resolver: zodResolver(schema) });

  const onSubmit = async (data: FormData) => {
    try {
      setError('');
      const res = await authApi.login(data);
      setAuth(res.user, res.token);
      router.push(`/${locale}/dashboard/author`);
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4">
      <div className="w-full max-w-md">
        {/* Logo */}
        <div className="text-center mb-8">
          <Link href={`/${locale}`} className="text-2xl font-extrabold text-brand-700">
            ginfoconf
          </Link>
          <h1 className="mt-4 text-2xl font-bold text-slate-900">{t('login_title')}</h1>
          <p className="mt-1 text-sm text-slate-500">{t('login_subtitle')}</p>
        </div>

        <div className="card p-8">
          {error && <ErrorAlert message={error} className="mb-5" />}

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
            <FormField
              label={t('email')}
              type="email"
              autoComplete="email"
              required
              {...register('email')}
              error={errors.email?.message}
            />
            <FormField
              label={t('password')}
              type="password"
              autoComplete="current-password"
              required
              {...register('password')}
              error={errors.password?.message}
            />

            <div className="flex items-center justify-end">
              <Link href="#" className="text-xs text-brand-600 hover:underline">
                {t('forgot_password')}
              </Link>
            </div>

            <button type="submit" disabled={isSubmitting} className="btn-primary w-full justify-center btn-lg">
              {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : t('login_btn')}
            </button>
          </form>
        </div>

        <p className="mt-5 text-center text-sm text-slate-500">
          {t('no_account')}{' '}
          <Link href={`/${locale}/register`} className="text-brand-600 font-medium hover:underline">
            {t('register_btn')}
          </Link>
        </p>
      </div>
    </div>
  );
}
