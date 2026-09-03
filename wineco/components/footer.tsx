'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { Phone, Mail, MapPin, ShieldCheck, Clock, ArrowUpRight } from 'lucide-react';

export function Footer() {
  const pathname = usePathname();
  if (pathname.startsWith('/admin')) return null;

  return (
    <footer className="bg-[#150d0e] text-[#a8a199] pt-16 pb-10 border-t-2 border-[#c9a03d]/40 text-xs">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Main Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-white/10">
          
          {/* Column 1: Brand & Philosophy */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="w-11 h-11 rounded-xl bg-[#1a0f0f] p-0.5 border-2 border-[#c9a03d] flex items-center justify-center shadow-md overflow-hidden">
                <Image
                  src="/wines/logo1.jpg"
                  alt="Wine & Co. Crest"
                  width={40}
                  height={40}
                  className="object-cover rounded-lg"
                />
              </div>
              <div>
                <h3 className="text-lg font-serif font-bold text-white tracking-wide">
                  Wine & Co.
                </h3>
                <p className="text-[10px] text-[#c9a03d] uppercase tracking-widest font-bold">
                  Eswatini
                </p>
              </div>
            </div>

            <p className="text-xs leading-relaxed text-[#b5ada5]">
              The kingdom&apos;s premier boutique wine purveyor. Hand-selected Cape Winelands & European imports, monthly surprise subscription boxes, and bespoke corporate gifting.
            </p>

            {/* Social Channels */}
            <div className="pt-2">
              <span className="text-[11px] font-semibold text-white/80 block mb-2">Connect With Us</span>
              <div className="flex items-center gap-2.5">
                <a
                  href="https://www.facebook.com/profile.php?id=61593933930355"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Follow Wine & Co on Facebook"
                  className="w-9 h-9 rounded-xl bg-white/5 hover:bg-[#1877F2] hover:text-white text-white/80 border border-white/10 hover:border-[#1877F2] flex items-center justify-center transition-all duration-200 group shadow-xs"
                >
                  <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                  </svg>
                </a>

                <a
                  href="https://wa.me/26878381971?text=Hello%20Wine%20%26%20Co.%20Eswatini!"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Chat with Sommelier on WhatsApp"
                  className="w-9 h-9 rounded-xl bg-white/5 hover:bg-[#25D366] hover:text-white text-white/80 border border-white/10 hover:border-[#25D366] flex items-center justify-center transition-all duration-200 shadow-xs"
                >
                  <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                  </svg>
                </a>

                <a
                  href="mailto:winecoeswatini@yahoo.com"
                  aria-label="Send email to Wine & Co."
                  className="w-9 h-9 rounded-xl bg-white/5 hover:bg-[#722f37] hover:text-white text-white/80 border border-white/10 hover:border-[#722f37] flex items-center justify-center transition-all duration-200 shadow-xs"
                >
                  <Mail className="w-4 h-4" />
                </a>
              </div>
            </div>
          </div>

          {/* Column 2: Cellar Collections */}
          <div>
            <h4 className="text-white font-serif font-bold text-sm mb-3.5 tracking-wide flex items-center gap-1.5">
              <span>Cellar & Offerings</span>
            </h4>
            <ul className="space-y-2 text-xs">
              <li>
                <Link href="/shop" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Our Wine Cellar</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
              <li>
                <Link href="/subscription" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Wine Club & Surprise Boxes</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
              <li>
                <Link href="/pairings" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Gourmet Food Pairings</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
              <li>
                <Link href="/corporate-gifts" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Corporate Luxury Gifts</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
              <li>
                <Link href="/gift-baskets" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Celebration Gift Hampers</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
              <li>
                <Link href="/magazine" className="hover:text-[#c9a03d] transition-colors flex items-center justify-between group">
                  <span>Boutique Magazine (E45)</span>
                  <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#c9a03d]" />
                </Link>
              </li>
            </ul>
          </div>

          {/* Column 3: Logistics & Assurance */}
          <div>
            <h4 className="text-white font-serif font-bold text-sm mb-3.5 tracking-wide">
              Concierge & Logistics
            </h4>
            <div className="space-y-2.5 text-xs text-[#b5ada5]">
              <div className="flex items-start gap-2">
                <MapPin className="w-3.5 h-3.5 text-[#c9a03d] mt-0.5 shrink-0" />
                <span>Mbabane • Manzini • Ezulwini • Matsapha & Nationwide Delivery</span>
              </div>
              <div className="flex items-start gap-2">
                <Clock className="w-3.5 h-3.5 text-[#c9a03d] mt-0.5 shrink-0" />
                <span>Orders & Enquiries: Mon – Sat, 08:00 – 18:00</span>
              </div>
              <div className="flex items-start gap-2 text-emerald-400/90">
                <ShieldCheck className="w-3.5 h-3.5 shrink-0 mt-0.5" />
                <span>Temperature-controlled courier handling for cellar preservation</span>
              </div>
              <div className="pt-2">
                <span className="text-[10px] text-white/60 uppercase tracking-wider block mb-1">Accepted Payment Methods</span>
                <p className="text-[11px] text-[#c9a03d] font-medium">
                  EFT Bank Transfer • Mobile Money • Card • Cash on Delivery
                </p>
              </div>
            </div>
          </div>

          {/* Column 4: Direct Contact & Compliance */}
          <div>
            <h4 className="text-white font-serif font-bold text-sm mb-3.5 tracking-wide">
              Direct Inquiries
            </h4>
            <div className="space-y-2.5">
              <a
                href="mailto:winecoeswatini@yahoo.com"
                className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 hover:text-white transition-all text-white/90 border border-white/5 truncate"
              >
                <Mail className="w-3.5 h-3.5 text-[#c9a03d] shrink-0" />
                <span className="truncate font-medium">winecoeswatini@yahoo.com</span>
              </a>

              <a
                href="tel:+26878381971"
                className="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 hover:text-white transition-all text-white/90 border border-white/5"
              >
                <Phone className="w-3.5 h-3.5 text-[#c9a03d] shrink-0" />
                <span className="font-medium">+268 7838 1971 / 7686 9104</span>
              </a>

              {/* Legal Notice */}
              <div className="bg-[#722f37]/25 p-3 rounded-xl border border-[#722f37]/50 text-[11px] text-[#c4bbb2]">
                <p className="text-[#c9a03d] font-bold mb-0.5">🔞 18+ Mandatory Verification</p>
                <p className="text-[10px] leading-normal text-neutral-400">
                  Alcohol is not sold to minors. Please savour and enjoy responsibly.
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-[#7d7670]">
          <p>
            © {new Date().getFullYear()} Wine & Co. Eswatini. All rights reserved. Prices in{' '}
            <strong className="text-[#c9a03d]">Swaziland Lilangeni (E / SZL)</strong>.
          </p>
          <div className="flex items-center gap-3">
            <Link href="/about" className="hover:text-white transition-colors">About Us</Link>
            <span className="text-[#722f37]">•</span>
            <Link href="/admin" className="hover:text-[#c9a03d] transition-colors">Staff Portal</Link>
            <span className="text-[#722f37]">•</span>
            <Link href="/contact" className="hover:text-white transition-colors">Support</Link>
          </div>
        </div>

      </div>
    </footer>
  );
}
