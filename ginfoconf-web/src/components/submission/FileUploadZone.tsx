'use client';

import { useCallback } from 'react';
import { useDropzone, type Accept } from 'react-dropzone';
import { Upload, AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Spinner } from '@/components/ui/Spinner';

interface FileUploadZoneProps {
  label: string;
  onFile: (file: File) => void;
  accept?: Accept;
  maxSize?: number;
  uploading?: boolean;
  progress?: number;
  hint?: string;
  error?: string;
  className?: string;
}

export function FileUploadZone({
  label,
  onFile,
  accept,
  maxSize,
  uploading = false,
  progress = 0,
  hint,
  error,
  className,
}: FileUploadZoneProps) {
  const onDrop = useCallback(
    (acceptedFiles: File[]) => {
      if (acceptedFiles[0]) onFile(acceptedFiles[0]);
    },
    [onFile]
  );

  const { getRootProps, getInputProps, isDragActive, fileRejections } = useDropzone({
    onDrop,
    accept,
    maxSize,
    multiple: false,
    disabled: uploading,
  });

  const rejectionMsg = fileRejections[0]?.errors?.[0]?.message;

  return (
    <div className={cn('space-y-2', className)}>
      {label && <p className="label">{label}</p>}

      <div
        {...getRootProps()}
        className={cn(
          'relative rounded-xl border-2 border-dashed p-8 text-center cursor-pointer transition-colors',
          isDragActive ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-brand-400 hover:bg-slate-50/50',
          uploading && 'cursor-not-allowed opacity-70'
        )}
      >
        <input {...getInputProps()} />

        {uploading ? (
          <div className="space-y-3">
            <Spinner className="mx-auto h-7 w-7" />
            <p className="text-sm text-slate-600">Uploading... {progress}%</p>
            <div className="mx-auto max-w-xs h-1.5 rounded-full bg-slate-200">
              <div
                className="h-full rounded-full bg-brand-500 transition-all"
                style={{ width: `${progress}%` }}
              />
            </div>
          </div>
        ) : (
          <div className="space-y-2">
            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
              <Upload className="h-6 w-6 text-slate-400" />
            </div>
            <p className="text-sm font-medium text-slate-700">
              {isDragActive ? 'Drop the file here' : 'Drag & drop or click to upload'}
            </p>
            {hint && <p className="text-xs text-slate-400">{hint}</p>}
          </div>
        )}
      </div>

      {(error || rejectionMsg) && (
        <div className="flex items-center gap-1.5 text-xs text-red-600">
          <AlertCircle className="h-3.5 w-3.5 shrink-0" />
          {error ?? rejectionMsg}
        </div>
      )}
    </div>
  );
}
