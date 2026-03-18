'use client';

import { cn } from '@/lib/utils';
import { forwardRef } from 'react';

interface FormFieldProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  hint?: string;
}

export const FormField = forwardRef<HTMLInputElement, FormFieldProps>(
  ({ label, error, hint, className, id, ...props }, ref) => {
    const fieldId = id ?? props.name;
    return (
      <div>
        {label && (
          <label htmlFor={fieldId} className="label">
            {label}
            {props.required && <span className="ml-0.5 text-red-500">*</span>}
          </label>
        )}
        <input
          ref={ref}
          id={fieldId}
          className={cn('input', error && 'input-error', className)}
          {...props}
        />
        {hint && !error && <p className="mt-1.5 text-xs text-slate-500">{hint}</p>}
        {error && <p className="field-error">{error}</p>}
      </div>
    );
  }
);
FormField.displayName = 'FormField';

interface TextAreaFieldProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
  label?: string;
  error?: string;
  hint?: string;
}

export const TextAreaField = forwardRef<HTMLTextAreaElement, TextAreaFieldProps>(
  ({ label, error, hint, className, id, ...props }, ref) => {
    const fieldId = id ?? props.name;
    return (
      <div>
        {label && (
          <label htmlFor={fieldId} className="label">
            {label}
            {props.required && <span className="ml-0.5 text-red-500">*</span>}
          </label>
        )}
        <textarea
          ref={ref}
          id={fieldId}
          className={cn('input min-h-[100px] resize-y', error && 'input-error', className)}
          {...props}
        />
        {hint && !error && <p className="mt-1.5 text-xs text-slate-500">{hint}</p>}
        {error && <p className="field-error">{error}</p>}
      </div>
    );
  }
);
TextAreaField.displayName = 'TextAreaField';

interface SelectFieldProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  label?: string;
  error?: string;
  options: { value: string; label: string }[];
  placeholder?: string;
}

export const SelectField = forwardRef<HTMLSelectElement, SelectFieldProps>(
  ({ label, error, options, placeholder, className, id, ...props }, ref) => {
    const fieldId = id ?? props.name;
    return (
      <div>
        {label && (
          <label htmlFor={fieldId} className="label">
            {label}
            {props.required && <span className="ml-0.5 text-red-500">*</span>}
          </label>
        )}
        <select
          ref={ref}
          id={fieldId}
          className={cn('input appearance-none', error && 'input-error', className)}
          {...props}
        >
          {placeholder && <option value="">{placeholder}</option>}
          {options.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
        {error && <p className="field-error">{error}</p>}
      </div>
    );
  }
);
SelectField.displayName = 'SelectField';
