'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { Phone, Mail, MapPin, ShieldCheck, Heart } from 'lucide-react';

export function Footer() {
  const pathname = usePathname();
  if (pathname.startsWith('/admin')) return null;

  return (
    <footer className="bg-[#1a1a2e] text-[#a0a0b0] pt-16 pb-12 border-t-4 border-[#c9a03d]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-white/10">
          {/* Brand Info */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-[#722f37] p-1 border border-[#c9a03d] flex items-center justify-center">
                <Image
                  src="/wines/logo.jpg"
                  alt="Wine & Co."
                  width={44}
                  height={44}
                  className="object-cover rounded-lg"
                />
              </div>
              <div>
                <h3 className="text-xl font-serif font-bold text-white tracking-wide">
                  Wine & Co.
                </h3>
                <p className="text-xs text-[#c9a03d] uppercase tracking-widest font-semibold">
                  Eswatini
                </p>
              </div>
            </div>
            <p className="text-sm leading-relaxed text-[#b0b0c0]">
              The kingdom&apos;s premier boutique wine purveyor. Hand-picked cellar collections,
              monthly subscription surprise boxes, and corporate luxury gifting delivered straight to your door.
            </p>
            <div className="flex items-center gap-2 text-xs text-[#c9a03d]">
              <MapPin className="w-4 h-4 shrink-0" />
              <span>Doorstep Delivery: Mbabane • Manzini • Ezulwini & Countrywide</span>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-serif font-semibold text-base mb-4 tracking-wider">
              Explore Collections
            </h4>
            <ul className="space-y-2.5 text-sm">
              <li>
                <Link href="/shop" className="hover:text-[#c9a03d] transition-colors">
                  Our Wine Cellar
                </Link>
              </li>
              <li>
                <Link href="/subscription" className="hover:text-[#c9a03d] transition-colors">
                  Wine Club & Surprise Boxes
                </Link>
              </li>
              <li>
                <Link href="/pairings" className="hover:text-[#c9a03d] transition-colors">
                  Gourmet Food Pairings
                </Link>
              </li>
              <li>
                <Link href="/corporate-gifts" className="hover:text-[#c9a03d] transition-colors">
                  Corporate Luxury Gifts
                </Link>
              </li>
              <li>
                <Link href="/gift-baskets" className="hover:text-[#c9a03d] transition-colors">
                  Celebration Gift Baskets
                </Link>
              </li>
              <li>
                <Link href="/magazine" className="hover:text-[#c9a03d] transition-colors">
                  Boutique Wine Magazine
                </Link>
              </li>
            </ul>
          </div>

          {/* Customer Service */}
          <div>
            <h4 className="text-white font-serif font-semibold text-base mb-4 tracking-wider">
              Customer Concierge
            </h4>
            <ul className="space-y-2.5 text-sm">
              <li>
                <Link href="/about" className="hover:text-[#c9a03d] transition-colors">
                  About Our Cellar
                </Link>
              </li>
              <li>
                <Link href="/contact" className="hover:text-[#c9a03d] transition-colors">
                  Contact & Enquiries
                </Link>
              </li>
              <li>
                <Link href="/cart" className="hover:text-[#c9a03d] transition-colors">
                  Shopping Bag & Checkout
                </Link>
              </li>
              <li className="pt-2 text-xs flex items-center gap-1.5 text-emerald-400">
                <ShieldCheck className="w-4 h-4" />
                <span>100% Genuine Temperature-Controlled Imports</span>
              </li>
            </ul>
          </div>

          {/* Direct Contact */}
          <div>
            <h4 className="text-white font-serif font-semibold text-base mb-4 tracking-wider">
              Sommelier Contact
            </h4>
            <div className="space-y-3 text-sm">
              <a
                href="tel:+26878381971"
                className="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 hover:text-white transition-all text-white/90"
              >
                <Phone className="w-4 h-4 text-[#c9a03d]" />
                <span>+268 7838 1971 / 7686 9104</span>
              </a>
              <a
                href="mailto:siphiwosethuthikazi@gmail.com"
                className="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 hover:text-white transition-all text-white/90 truncate"
              >
                <Mail className="w-4 h-4 text-[#c9a03d]" />
                <span className="truncate">siphiwosethuthikazi@gmail.com</span>
              </a>
              <div className="text-xs text-[#a0a0b0] bg-[#722f37]/30 p-3 rounded-xl border border-[#722f37]/50">
                <p className="text-[#c9a03d] font-semibold mb-1">🔞 18+ Mandatory</p>
                <p>Alcohol is not for sale to persons under the age of 18. Please enjoy responsibly.</p>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[#808090]">
          <p>
            © {new Date().getFullYear()} Wine & Co. Eswatini. All rights reserved. Prices in{' '}
            <strong className="text-[#c9a03d]">Swaziland Lilangeni (E / SZL)</strong>.
          </p>
          <div className="flex items-center gap-4">
            <span>Crafted with refinement for wine connoisseurs</span>
            <span className="text-[#722f37]">•</span>
            <Link href="/admin" className="hover:text-white transition-colors">
              Staff Portal
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
