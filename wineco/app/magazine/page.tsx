'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import { BookOpen, Download, Lock, CheckCircle2, CreditCard, Sparkles, X } from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export default function MagazinePage() {
  const { showToast } = useCart();
  const [currentPage, setCurrentPage] = useState(1);
  const [isDownloadModalOpen, setIsDownloadModalOpen] = useState(false);
  const [downloading, setDownloading] = useState(false);
  const [downloadForm, setDownloadForm] = useState({
    name: '',
    email: '',
    phone: '',
  });

  const handleDownloadPay = (e: React.FormEvent) => {
    e.preventDefault();
    setDownloading(true);

    setTimeout(() => {
      setDownloading(false);
      setIsDownloadModalOpen(false);
      showToast('Payment confirmed! Your high-res 24-page PDF download has started.');

      // Trigger download
      const link = document.createElement('a');
      link.href = '/downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';
      link.download = 'WineCo_Boutique_Magazine_Professional_Edition.pdf';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }, 1200);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-12 text-white border-2 border-[#c9a03d] shadow-xl grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
        <div className="md:col-span-8 space-y-4">
          <div className="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#c9a03d]/20 text-[#c9a03d] text-xs font-bold uppercase tracking-widest border border-[#c9a03d]/40">
            <BookOpen className="w-4 h-4" />
            <span>Official Sommelier Publication</span>
          </div>

          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Wine & Co. Boutique Magazine
          </h1>

          <p className="text-sm md:text-base text-white/80 leading-relaxed max-w-2xl">
            A comprehensive 24-page editorial dedicated to vintage cellaring, Cape Winelands exploration,
            masterclass food pairings, and luxury wine gifting. Enjoy a free 3-page preview below!
          </p>

          <div className="flex flex-wrap gap-4 pt-2">
            <a
              href="#viewer"
              className="btn-wine text-sm py-3 px-6 shadow-md"
            >
              <BookOpen className="w-4 h-4" />
              <span>Read Free Preview (3 Pages)</span>
            </a>

            <button
              onClick={() => setIsDownloadModalOpen(true)}
              className="btn-gold text-sm py-3 px-6 shadow-xl"
            >
              <Download className="w-4 h-4" />
              <span>Download Full PDF (E45)</span>
            </button>
          </div>
        </div>

        <div className="md:col-span-4 flex justify-center">
          <div className="relative w-48 md:w-56 rounded-2xl overflow-hidden shadow-2xl border-4 border-[#c9a03d]/50 cursor-pointer group" onClick={() => setIsDownloadModalOpen(true)}>
            <Image
              src="/images/magazine-cover.jpg"
              alt="Wine & Co. Magazine Cover"
              width={240}
              height={320}
              className="w-full object-cover group-hover:scale-105 transition-transform"
            />
            <span className="absolute bottom-2 right-2 bg-[#c9a03d] text-[#1a1a2e] text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow">24 Pgs • E45</span>
          </div>
        </div>
      </div>

      {/* Highlights Grid */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
        <div className="bg-white p-5 rounded-2xl border border-[#f0ece8] shadow-sm space-y-1">
          <strong className="text-2xl font-serif font-bold text-[#722f37] block">24 Pages</strong>
          <span className="text-xs text-neutral-500">Curated Editorial Guide</span>
        </div>
        <div className="bg-white p-5 rounded-2xl border border-[#f0ece8] shadow-sm space-y-1">
          <strong className="text-2xl font-serif font-bold text-[#c9a03d] block">Cape Winelands</strong>
          <span className="text-xs text-neutral-500">Estate Profiles & History</span>
        </div>
        <div className="bg-white p-5 rounded-2xl border border-[#f0ece8] shadow-sm space-y-1">
          <strong className="text-2xl font-serif font-bold text-[#1a6b3c] block">Food Pairings</strong>
          <span className="text-xs text-neutral-500">Chef & Sommelier Secrets</span>
        </div>
        <div className="bg-white p-5 rounded-2xl border border-[#f0ece8] shadow-sm space-y-1">
          <strong className="text-2xl font-serif font-bold text-[#1a1a2e] block">Cellaring 101</strong>
          <span className="text-xs text-neutral-500">Temperature & Ageing Tips</span>
        </div>
      </div>

      {/* Interactive 3-Page Magazine Preview Reader */}
      <div id="viewer" className="bg-white rounded-3xl p-6 md:p-8 border border-[#f0ece8] shadow-lg space-y-6">
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#f0ece8] pb-4">
          <div>
            <div className="flex items-center gap-2">
              <h2 className="text-xl font-serif font-bold text-[#2c1a1a]">
                Interactive Magazine Reader
              </h2>
              <span className="bg-[#c9a03d]/20 text-[#c9a03d] text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase">3-Page Preview</span>
            </div>
            <p className="text-xs text-neutral-500 mt-0.5">
              Read the initial 3 pages free online. Download the complete 24-page high-resolution issue for E45.
            </p>
          </div>

          <button
            onClick={() => setIsDownloadModalOpen(true)}
            className="btn-gold text-xs px-5 py-2.5 shadow-md"
          >
            <Download className="w-3.5 h-3.5" />
            <span>Download Offline Copy (E45)</span>
          </button>
        </div>

        {/* Reader Navigation Toolbar */}
        <div className="flex items-center justify-between bg-[#faf6f0] p-3 rounded-2xl border border-[#e8e0d8] text-xs">
          <div className="flex items-center gap-2">
            <button
              onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
              disabled={currentPage === 1}
              className="px-3 py-1.5 rounded-xl bg-white border border-[#e8e0d8] hover:bg-[#722f37] hover:text-white font-semibold transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
              Prev Page
            </button>
            <span className="font-bold text-[#722f37] px-2">
              {currentPage <= 3 ? `Page ${currentPage} of 3 (Free Preview)` : `Page 4 of 24 (Locked Paywall)`}
            </span>
            <button
              onClick={() => {
                if (currentPage >= 3) {
                  setCurrentPage(4);
                } else {
                  setCurrentPage((p) => p + 1);
                }
              }}
              className="px-3 py-1.5 rounded-xl bg-[#722f37] text-white hover:bg-[#552127] font-semibold transition-colors"
            >
              {currentPage === 3 ? 'Unlock Full (E45)' : 'Next Page'}
            </button>
          </div>

          <div className="hidden sm:flex items-center gap-2">
            <button
              onClick={() => setCurrentPage(1)}
              className={`px-2.5 py-1 rounded-lg border text-[11px] font-bold ${currentPage === 1 ? 'bg-[#722f37] text-white' : 'bg-white text-neutral-700'}`}
            >
              1. Cover
            </button>
            <button
              onClick={() => setCurrentPage(2)}
              className={`px-2.5 py-1 rounded-lg border text-[11px] font-bold ${currentPage === 2 ? 'bg-[#722f37] text-white' : 'bg-white text-neutral-700'}`}
            >
              2. Winelands
            </button>
            <button
              onClick={() => setCurrentPage(3)}
              className={`px-2.5 py-1 rounded-lg border text-[11px] font-bold ${currentPage === 3 ? 'bg-[#722f37] text-white' : 'bg-white text-neutral-700'}`}
            >
              3. Pairings
            </button>
            <button
              onClick={() => setCurrentPage(4)}
              className={`px-2.5 py-1 rounded-lg border text-[11px] font-bold ${currentPage === 4 ? 'bg-[#722f37] text-white' : 'bg-[#c9a03d]/20 text-[#9c7823]'}`}
            >
              🔒 4-24. Full
            </button>
          </div>
        </div>

        {/* Page Content Viewport */}
        <div className="w-full min-h-[520px] bg-[#faf6f0] rounded-2xl border-2 border-[#e8e0d8] overflow-hidden p-4 md:p-8 flex items-center justify-center relative shadow-inner">
          {currentPage === 1 && (
            <div className="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-[#f0ece8] p-6 md:p-10 space-y-6 animate-in fade-in">
              <div className="flex flex-col md:flex-row items-center gap-8 border-b border-[#f0ece8] pb-6">
                <div className="relative w-48 h-64 shrink-0 rounded-xl overflow-hidden shadow-lg border border-[#c9a03d]/40">
                  <Image
                    src="/images/magazine-cover.jpg"
                    alt="Cover"
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="space-y-3 text-left">
                  <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest block">Issue No. 04 • Collector&apos;s Edition</span>
                  <h2 className="text-2xl md:text-3xl font-serif font-bold text-[#722f37]">The Essence of Fine Living</h2>
                  <p className="text-xs md:text-sm text-neutral-600 leading-relaxed">
                    Welcome to the official publication of Wine &amp; Co. Eswatini. In this edition, our resident sommeliers take you through the secret cellars of the Cape Winelands, reveal temperature storage secrets, and present our 2026 Reserve Collection.
                  </p>
                  <div className="bg-[#faf6f0] p-3.5 rounded-xl border border-[#e8e0d8] text-xs text-[#4a2c2a] space-y-1">
                    <strong>Inside this issue:</strong>
                    <ul className="list-disc list-inside space-y-0.5 text-neutral-600">
                      <li>Estate Focus: Kanonkop &amp; Meerlust Heritage</li>
                      <li>The Art of Decanting Bordeaux Blends</li>
                      <li>Swazi Epicurean Pairings &amp; Artisan Cheeses</li>
                    </ul>
                  </div>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs text-neutral-500 pt-2">
                <span>Page 1 of 24 • Editorial Frontpiece</span>
                <button onClick={() => setCurrentPage(2)} className="btn-wine text-xs px-4 py-2">
                  Read Page 2 →
                </button>
              </div>
            </div>
          )}

          {currentPage === 2 && (
            <div className="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-[#f0ece8] p-6 md:p-10 space-y-6 animate-in fade-in">
              <div className="border-b border-[#f0ece8] pb-4 text-left">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">Regional Terroir Deep-Dive</span>
                <h2 className="text-2xl md:text-3xl font-serif font-bold text-[#2c1a1a] mt-1">The Cape Winelands Odyssey</h2>
                <p className="text-xs text-neutral-500">Stellenbosch, Franschhoek &amp; Helderberg Slopes</p>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-neutral-700 leading-relaxed text-left">
                <div className="space-y-3">
                  <p>
                    The unique maritime climate of the Western Cape, kissed by Atlantic sea breezes and rich ancient granite soils, yields some of the world&apos;s most aromatic Sauvignon Blancs and long-lived Bordeaux blends.
                  </p>
                  <div className="bg-[#faf6f0] p-4 rounded-xl border border-[#e8e0d8] space-y-1.5">
                    <h4 className="font-bold text-[#722f37] font-serif text-sm">Key Vintage Notes (2018 - 2021)</h4>
                    <p className="text-neutral-600">Dry winter conditions followed by moderate summer days produced exceptional grape concentration with bright natural acidity.</p>
                  </div>
                </div>
                <div className="space-y-3">
                  <p>
                    At Wine &amp; Co., each estate allocation is transported under constant temperature regulation from the cellar door in South Africa directly to our climate-controlled depository in Eswatini.
                  </p>
                  <div className="bg-[#1a0f0f] text-white p-4 rounded-xl border border-[#c9a03d]/40 space-y-1">
                    <strong className="text-[#c9a03d] block font-serif">Sommelier Recommendation</strong>
                    <p className="text-white/80 text-[11px]">Allow Kanonkop Pinotage to breathe in a wide crystal decanter for 45 minutes before serving at 16°C - 18°C.</p>
                  </div>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs text-neutral-500 pt-4 border-t border-[#f0ece8]">
                <button onClick={() => setCurrentPage(1)} className="text-[#722f37] font-bold hover:underline">← Back to Page 1</button>
                <span>Page 2 of 24 • Regional Feature</span>
                <button onClick={() => setCurrentPage(3)} className="btn-wine text-xs px-4 py-2">
                  Read Page 3 →
                </button>
              </div>
            </div>
          )}

          {currentPage === 3 && (
            <div className="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-[#f0ece8] p-6 md:p-10 space-y-6 animate-in fade-in">
              <div className="border-b border-[#f0ece8] pb-4 text-left">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">Masterclass Tasting Companion</span>
                <h2 className="text-2xl md:text-3xl font-serif font-bold text-[#2c1a1a] mt-1">Epicurean Food &amp; Wine Pairing Compass</h2>
                <p className="text-xs text-neutral-500">Curated harmony of tannins, acid, and gastronomy</p>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-left">
                <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8] space-y-2">
                  <span className="text-lg">🧀</span>
                  <h4 className="font-bold text-[#722f37] font-serif">Aged Cheddar &amp; Brie</h4>
                  <p className="text-neutral-600">Pairs with: <strong>Château Margaux</strong> &amp; bold Cabernet Sauvignon. The rich dairy fats soften vigorous tannins.</p>
                </div>
                <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8] space-y-2">
                  <span className="text-lg">🥩</span>
                  <h4 className="font-bold text-[#722f37] font-serif">Prime Biltong &amp; Game</h4>
                  <p className="text-neutral-600">Pairs with: <strong>Kanonkop Pinotage</strong>. Rich coriander &amp; pepper spices accentuate the smoky berry notes.</p>
                </div>
                <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8] space-y-2">
                  <span className="text-lg">🍫</span>
                  <h4 className="font-bold text-[#722f37] font-serif">Dark Belgian Truffles</h4>
                  <p className="text-neutral-600">Pairs with: <strong>The Reserve Red Blend</strong>. 70% dark cocoa enhances the velvety plum and mocha finish.</p>
                </div>
              </div>
              <div className="flex items-center justify-between text-xs text-neutral-500 pt-2 border-t border-[#f0ece8]">
                <button onClick={() => setCurrentPage(2)} className="text-[#722f37] font-bold hover:underline">← Back to Page 2</button>
                <span>Page 3 of 24 (End of Free Preview)</span>
                <button onClick={() => setCurrentPage(4)} className="btn-gold text-xs px-4 py-2 font-bold">
                  Unlock Full Issue 🔒
                </button>
              </div>
            </div>
          )}

          {currentPage === 4 && (
            <div className="w-full max-w-2xl bg-[#1a0f0f] rounded-3xl shadow-2xl border-2 border-[#c9a03d] p-8 md:p-12 text-white text-center space-y-6 animate-in fade-in">
              <div className="w-16 h-16 bg-[#722f37] rounded-2xl mx-auto flex items-center justify-center border-2 border-[#c9a03d] shadow-xl text-[#c9a03d]">
                <Lock className="w-8 h-8" />
              </div>
              <div className="space-y-2">
                <span className="text-xs uppercase font-bold text-[#c9a03d] tracking-widest">Free Preview Limit Reached</span>
                <h2 className="text-2xl md:text-4xl font-serif font-bold text-white">Unlock the Complete 24-Page Edition</h2>
                <p className="text-xs md:text-sm text-white/80 max-w-md mx-auto leading-relaxed">
                  You have reached the end of the 3-page online preview. To access the entire 24-page high-resolution PDF publication with all vintage ratings and sommelier guides, please complete the download fee.
                </p>
              </div>

              <div className="bg-white/5 border border-white/10 rounded-2xl p-4 max-w-sm mx-auto text-xs text-left space-y-2">
                <div className="flex justify-between text-white/80">
                  <span>Publication:</span>
                  <strong className="text-white">Boutique Magazine 2026</strong>
                </div>
                <div className="flex justify-between text-white/80">
                  <span>Format:</span>
                  <strong className="text-[#c9a03d]">High-Res Printable PDF (24 pgs)</strong>
                </div>
                <div className="flex justify-between text-white/80 border-t border-white/10 pt-2 text-sm">
                  <span>Download Fee:</span>
                  <strong className="text-xl font-bold text-[#1a6b3c]">E45.00</strong>
                </div>
              </div>

              <div className="space-y-3 max-w-sm mx-auto pt-2">
                <button onClick={() => setIsDownloadModalOpen(true)} className="w-full btn-gold py-3.5 px-6 rounded-full font-bold shadow-2xl flex items-center justify-center gap-2">
                  <CreditCard className="w-4 h-4" />
                  <span>Pay E45.00 &amp; Download PDF</span>
                </button>
                <button onClick={() => setCurrentPage(1)} className="w-full bg-white/10 hover:bg-white/20 text-white/70 text-xs py-2.5 px-6 rounded-full transition-colors">
                  ← Return to Page 1 Preview
                </button>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* E45 Download Modal with Stripe Skeleton */}
      {isDownloadModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border-2 border-[#c9a03d] relative">
            <button
              onClick={() => setIsDownloadModalOpen(false)}
              className="absolute top-4 right-4 p-2 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
            >
              <X className="w-5 h-5" />
            </button>

            <div className="space-y-5">
              <div className="border-b border-[#f0ece8] pb-3">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
                  Instant High-Res Download
                </span>
                <h3 className="text-xl font-serif font-bold text-[#722f37] mt-0.5">
                  Boutique Magazine PDF
                </h3>
                <span className="text-xl font-bold text-[#1a6b3c] block mt-1">E45.00</span>
              </div>

              <form onSubmit={handleDownloadPay} className="space-y-3.5 text-xs">
                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Your Name</label>
                  <input
                    type="text"
                    required
                    value={downloadForm.name}
                    onChange={(e) => setDownloadForm({ ...downloadForm, name: e.target.value })}
                    placeholder="e.g. Siphiwo Thikazi"
                    className="w-full p-2.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Email Address</label>
                  <input
                    type="email"
                    required
                    value={downloadForm.email}
                    onChange={(e) => setDownloadForm({ ...downloadForm, email: e.target.value })}
                    placeholder="siphiwo@example.com"
                    className="w-full p-2.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                {/* Stripe Skeleton */}
                <div className="bg-[#1a0f0f] text-white p-3.5 rounded-xl border border-[#c9a03d]/40 space-y-2">
                  <div className="flex items-center justify-between text-[11px] text-[#c9a03d] font-bold">
                    <span className="flex items-center gap-1">
                      <Lock className="w-3 h-3" /> Stripe Secure Card
                    </span>
                    <span className="text-[10px] text-white/50">256-Bit SSL</span>
                  </div>
                  <input
                    type="text"
                    placeholder="Card Number •••• •••• •••• ••••"
                    defaultValue="4242 •••• •••• 4242"
                    className="w-full p-2 bg-white/10 border border-white/20 rounded text-xs text-white placeholder-white/40 focus:outline-none"
                  />
                  <div className="grid grid-cols-2 gap-2">
                    <input
                      type="text"
                      placeholder="MM/YY"
                      defaultValue="12/28"
                      className="p-2 bg-white/10 border border-white/20 rounded text-xs text-white placeholder-white/40 focus:outline-none"
                    />
                    <input
                      type="text"
                      placeholder="CVC"
                      defaultValue="123"
                      className="p-2 bg-white/10 border border-white/20 rounded text-xs text-white placeholder-white/40 focus:outline-none"
                    />
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={downloading}
                  className="btn-wine w-full py-3.5 text-sm font-bold shadow-lg flex items-center justify-center gap-2"
                >
                  {downloading ? (
                    <span>Processing Payment...</span>
                  ) : (
                    <>
                      <Download className="w-4 h-4" />
                      <span>Pay E45.00 & Download PDF</span>
                    </>
                  )}
                </button>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
