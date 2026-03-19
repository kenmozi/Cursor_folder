'use client';

import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import type { User } from '@/types';

interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  setAuth: (user: User, token: string) => void;
  clearAuth: () => void;
  setLoading: (v: boolean) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      isLoading: false,
      setAuth: (user, token) => {
        // Also store token in localStorage for the axios interceptor
        if (typeof window !== 'undefined') {
          localStorage.setItem('ginfoconf_token', token);
          localStorage.setItem('ginfoconf_user', JSON.stringify(user));
        }
        set({ user, token, isLoading: false });
      },
      clearAuth: () => {
        if (typeof window !== 'undefined') {
          localStorage.removeItem('ginfoconf_token');
          localStorage.removeItem('ginfoconf_user');
        }
        set({ user: null, token: null });
      },
      setLoading: (v) => set({ isLoading: v }),
    }),
    {
      name: 'ginfoconf-auth',
      storage: createJSONStorage(() =>
        typeof window !== 'undefined' ? localStorage : { getItem: () => null, setItem: () => {}, removeItem: () => {} }
      ),
      partialize: (s) => ({ user: s.user, token: s.token }),
    }
  )
);

// Convenience selectors
export const selectUser = (s: AuthState) => s.user;
export const selectIsAuthenticated = (s: AuthState) => !!s.token;
