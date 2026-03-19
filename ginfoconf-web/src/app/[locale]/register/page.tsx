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
  name: z.string().min(2, 'Name must be at least 2 characters'),
  email: z.string().email('Invalid email address'),
  affiliation: z.string().optional(),
  country: z.string().optional(),
  password: z.string().min(8, 'Password must be at least 8 characters'),
  password_confirmation: z.string(),
}).refine((d) => d.password === d.password_confirmation, {
  message: "Passwords don't match",
  path: ['password_confirmation'],
});

type FormData = z.infer<typeof schema>;

export default function RegisterPage() {
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
      const res = await authApi.register(data);
      setAuth(res.user, res.token);
      router.push(`/${locale}/dashboard/author`);
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <Link href={`/${locale}`} className="text-2xl font-extrabold text-brand-700">
            ginfoconf
          </Link>
          <h1 className="mt-4 text-2xl font-bold text-slate-900">{t('register_title')}</h1>
          <p className="mt-1 text-sm text-slate-500">{t('register_subtitle')}</p>
        </div>

        <div className="card p-8">
          {error && <ErrorAlert message={error} className="mb-5" />}

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
            <FormField
              label={t('name')}
              required
              autoComplete="name"
              placeholder="Dr. Jane Smith"
              {...register('name')}
              error={errors.name?.message}
            />
            <FormField
              label={t('email')}
              type="email"
              required
              autoComplete="email"
              placeholder="you@university.edu"
              {...register('email')}
              error={errors.email?.message}
            />

            <div className="grid grid-cols-2 gap-4">
              <FormField
                label={t('affiliation')}
                placeholder="University / CNRS"
                {...register('affiliation')}
                error={errors.affiliation?.message}
              />
              <FormField
                label={t('country')}
                placeholder="FR"
                {...register('country')}
                error={errors.country?.message}
              />
            </div>

            <FormField
              label={t('password')}
              type="password"
              required
              autoComplete="new-password"
              {...register('password')}
              error={errors.password?.message}
            />
            <FormField
              label={t('password_confirm')}
              type="password"
              required
              autoComplete="new-password"
              {...register('password_confirmation')}
              error={errors.password_confirmation?.message}
            />

            <button type="submit" disabled={isSubmitting} className="btn-primary w-full justify-center btn-lg">
              {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : t('register_btn')}
            </button>
          </form>
        </div>

        <p className="mt-5 text-center text-sm text-slate-500">
          {t('have_account')}{' '}
          <Link href={`/${locale}/login`} className="text-brand-600 font-medium hover:underline">
            {t('login_btn')}
          </Link>
        </p>
      </div>
    </div>
  );
}
