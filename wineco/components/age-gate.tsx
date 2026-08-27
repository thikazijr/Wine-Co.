'use client';

import React, { useEffect, useState } from 'react';
import { Wine, ShieldAlert, Ban, ArrowRight, ArrowLeft } from 'lucide-react';

export function AgeGate() {
  const [isOpen, setIsOpen] = useState(false);
  const [isDenied, setIsDenied] = useState(false);

  useEffect(() => {
    const verified = localStorage.getItem('wineco_age_verified');
    if (verified !== 'true') {
      setIsOpen(true);
    }
  }, []);

  const handleVerify = (isLegal: boolean) => {
    if (isLegal) {
      localStorage.setItem('wineco_age_verified', 'true');
      setIsOpen(false);
    } else {
      setIsDenied(true);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md animate-in fade-in duration-300">
      <div className="bg-[#1a0f0f] border-2 border-[#c9a03d] text-white rounded-3xl max-w-md w-full p-8 text-center shadow-2xl relative overflow-hidden">
        {/* Glow decoration */}
        <div className="absolute -top-24 -left-24 w-48 h-48 bg-[#722f37] rounded-full blur-3xl opacity-50 pointer-events-none" />
        <div className="absolute -bottom-24 -right-24 w-48 h-48 bg-[#c9a03d] rounded-full blur-3xl opacity-30 pointer-events-none" />

        {!isDenied ? (
          <div className="relative z-10 space-y-6">
            <div className="w-16 h-16 bg-[#722f37] rounded-2xl mx-auto flex items-center justify-center border border-[#c9a03d] shadow-lg">
              <Wine className="w-8 h-8 text-[#c9a03d]" />
            </div>

            <div>
              <h2 className="text-2xl font-serif font-bold text-[#c9a03d] tracking-wide">
                Welcome to Wine & Co.
              </h2>
              <p className="text-xs tracking-widest uppercase text-white/70 mt-1">
                Eswatini Premium Wine Purveyor
              </p>
            </div>

            <div className="bg-white/5 rounded-2xl p-4 border border-white/10 text-sm text-white/90 text-left space-y-1">
              <p className="font-semibold text-white flex items-center gap-1.5">
                <ShieldAlert className="w-4 h-4 text-[#c9a03d]" />
                <span>🔞 Age Verification Required</span>
              </p>
              <p className="text-xs text-white/70 leading-relaxed">
                You must be of legal drinking age (<strong>18 years or older</strong> in Eswatini) to enter this site and purchase alcoholic beverages.
              </p>
            </div>

            <div className="space-y-3 pt-2">
              <button
                onClick={() => handleVerify(true)}
                className="w-full bg-[#c9a03d] hover:bg-[#b58d2c] text-[#1a1a2e] font-bold py-3.5 px-6 rounded-full transition-all shadow-lg hover:shadow-[#c9a03d]/30"
              >
                I am 18 or older — Enter Site
              </button>
              <button
                onClick={() => handleVerify(false)}
                className="w-full bg-white/10 hover:bg-white/20 text-white/80 text-sm font-medium py-3 px-6 rounded-full transition-colors flex items-center justify-center gap-1.5"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>I am under 18 — Exit</span>
              </button>
            </div>

            <p className="text-[11px] text-white/50">
              By entering, you accept our terms and conditions. Please drink responsibly.
            </p>
          </div>
        ) : (
          <div className="relative z-10 space-y-6 animate-in fade-in">
            <div className="w-16 h-16 bg-red-900/50 rounded-2xl mx-auto flex items-center justify-center border border-red-500/50 shadow-lg text-red-400">
              <Ban className="w-8 h-8" />
            </div>

            <div>
              <h2 className="text-2xl font-serif font-bold text-red-400 tracking-wide">
                Access Restricted
              </h2>
              <p className="text-xs tracking-widest uppercase text-white/70 mt-1">
                18+ Lawful Requirement
              </p>
            </div>

            <div className="bg-red-950/40 rounded-2xl p-4 border border-red-800/40 text-xs text-white/90 text-left space-y-2">
              <p className="font-semibold text-white">We are unable to grant you access to this website.</p>
              <p className="text-white/75 leading-relaxed">
                Alcoholic beverages and cellar subscriptions cannot be marketed or sold to persons under the age of 18 in Eswatini.
              </p>
            </div>

            <div className="space-y-3 pt-2">
              <button
                onClick={() => (window.location.href = 'https://www.google.com')}
                className="w-full bg-red-800 hover:bg-red-700 text-white font-bold py-3.5 px-6 rounded-full transition-all shadow-lg flex items-center justify-center gap-2"
              >
                <span>Leave Website (Go to Google)</span>
                <ArrowRight className="w-4 h-4" />
              </button>
              <button
                onClick={() => setIsDenied(false)}
                className="w-full bg-white/10 hover:bg-white/20 text-white/70 text-xs font-medium py-2.5 px-6 rounded-full transition-colors"
              >
                Clicked by mistake? Re-verify
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

