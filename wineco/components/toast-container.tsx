'use client';

import React from 'react';
import { useCart } from '@/lib/cart-context';
import { CheckCircle, AlertCircle } from 'lucide-react';

export function ToastContainer() {
  const { toastMessage, toastType } = useCart();

  if (!toastMessage) return null;

  return (
    <div className="fixed bottom-6 left-6 z-50 animate-in slide-in-from-bottom-5 duration-300">
      <div
        className={`flex items-center gap-3 py-3.5 px-6 rounded-full shadow-2xl text-sm font-semibold border ${
          toastType === 'success'
            ? 'bg-[#1a6b3c] text-white border-emerald-400/40 shadow-emerald-950/20'
            : 'bg-[#dc3545] text-white border-red-400/40 shadow-red-950/20'
        }`}
      >
        {toastType === 'success' ? (
          <CheckCircle className="w-5 h-5 text-emerald-200" />
        ) : (
          <AlertCircle className="w-5 h-5 text-red-200" />
        )}
        <span>{toastMessage}</span>
      </div>
    </div>
  );
}
