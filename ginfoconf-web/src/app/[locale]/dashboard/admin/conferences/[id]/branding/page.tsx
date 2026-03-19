'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { FileUploadZone } from '@/components/submission/FileUploadZone';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { adminConferenceApi } from '@/lib/api';
import { getErrorMessage } from '@/lib/utils';
import type { ConferenceAdmin } from '@/types';

export default function BrandingPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [conference, setConference] = useState<ConferenceAdmin | null>(null);
  const [loading, setLoading] = useState(true);
  const [logoProgress, setLogoProgress] = useState(0);
  const [coverProgress, setCoverProgress] = useState(0);
  const [uploadingLogo, setUploadingLogo] = useState(false);
  const [uploadingCover, setUploadingCover] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  useEffect(() => {
    adminConferenceApi.get(slug).then(setConference).finally(() => setLoading(false));
  }, [slug]);

  const handleUpload = async (type: 'logo' | 'cover', file: File) => {
    const setProgress = type === 'logo' ? setLogoProgress : setCoverProgress;
    const setUploading = type === 'logo' ? setUploadingLogo : setUploadingCover;
    try {
      setUploading(true);
      setError('');
      await adminConferenceApi.uploadMedia(slug, type, file, setProgress);
      const updated = await adminConferenceApi.get(slug);
      setConference(updated);
      setSuccess(`${type.charAt(0).toUpperCase() + type.slice(1)} updated successfully.`);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setUploading(false);
      setProgress(0);
    }
  };

  const mediaUrl = (type: 'logo' | 'cover') => conference?.media?.[type];

  return (
    <AdminLayout title="Branding" conferenceSlug={slug}>
      <div className="p-6 max-w-2xl space-y-6">
        <h2 className="text-xl font-bold text-slate-900">Conference Branding</h2>

        {error && <ErrorAlert message={error} />}
        {success && (
          <div className="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">{success}</div>
        )}

        {loading ? (
          <PageLoader />
        ) : (
          <>
            {/* Logo */}
            <div className="card p-6 space-y-4">
              <h3 className="font-semibold text-slate-800">Conference Logo</h3>
              {mediaUrl('logo') && (
                <div className="flex items-center gap-4 p-3 rounded-lg bg-slate-50 border border-slate-200">
                  <img src={mediaUrl('logo')} alt="Current logo" className="h-16 w-16 object-contain rounded" />
                  <div>
                    <p className="text-sm text-slate-600">Current logo</p>
                    <button
                      onClick={() => adminConferenceApi.deleteMedia(slug, 'logo').then(() => adminConferenceApi.get(slug).then(setConference))}
                      className="text-xs text-red-600 hover:underline mt-0.5"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              )}
              <FileUploadZone
                label="Upload Logo"
                accept={{ 'image/*': ['.jpg', '.jpeg', '.png', '.webp'] }}
                maxSize={5 * 1024 * 1024}
                uploading={uploadingLogo}
                progress={logoProgress}
                onFile={(f) => handleUpload('logo', f)}
                hint="JPEG, PNG, or WebP. Max 5 MB. Recommended: 200×200px square."
              />
            </div>

            {/* Cover image */}
            <div className="card p-6 space-y-4">
              <h3 className="font-semibold text-slate-800">Cover / Hero Image</h3>
              {mediaUrl('cover') && (
                <div className="relative overflow-hidden rounded-lg h-36">
                  <img src={mediaUrl('cover')} alt="Current cover" className="w-full h-full object-cover" />
                  <button
                    onClick={() => adminConferenceApi.deleteMedia(slug, 'cover').then(() => adminConferenceApi.get(slug).then(setConference))}
                    className="absolute top-2 right-2 btn-danger btn-sm text-xs"
                  >
                    Remove
                  </button>
                </div>
              )}
              <FileUploadZone
                label="Upload Cover Image"
                accept={{ 'image/*': ['.jpg', '.jpeg', '.png', '.webp'] }}
                maxSize={5 * 1024 * 1024}
                uploading={uploadingCover}
                progress={coverProgress}
                onFile={(f) => handleUpload('cover', f)}
                hint="JPEG, PNG, or WebP. Max 5 MB. Recommended: 1600×600px landscape."
              />
            </div>
          </>
        )}
      </div>
    </AdminLayout>
  );
}
