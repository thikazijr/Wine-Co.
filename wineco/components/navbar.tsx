'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { ShoppingBag, Menu, X, Gift, Package, BookOpen, Sparkles } from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export function Navbar() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname();
  const { totalCount, setIsDrawerOpen } = useCart();

  const navLinks = [
    { href: '/', label: 'Home' },
    { href: '/shop', label: 'Wines' },
    { href: '/pairings', label: 'Pairings' },
    { href: '/subscription', label: 'Wine Club', badge: 'Popular' },
    { href: '/corporate-gifts', label: 'Corporate Gifts', icon: Gift },
    { href: '/gift-baskets', label: 'Gift Baskets', icon: Package },
    { href: '/magazine', label: 'Magazine', icon: BookOpen },
  ];

  const isAdminRoute = pathname.startsWith('/admin');
  if (isAdminRoute) return null;

  return (
    <header className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b-2 border-[#c9a03d] shadow-sm">
      {/* Top Notification Bar */}
      <div className="bg-[#1a0f0f] text-[#c9a03d] py-1 px-4 text-xs font-medium text-center tracking-wider flex items-center justify-center gap-2">
        <Sparkles className="w-3.5 h-3.5 text-[#c9a03d]" />
        <span>COMPLIMENTARY DELIVERY ACROSS ESWATINI ON ORDERS OVER E600 • ALL PRICES IN (E / SZL)</span>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 group">
            <div className="relative w-12 h-12 bg-[#1a0f0f] rounded-xl overflow-hidden shadow-md flex items-center justify-center p-0.5 border-2 border-[#c9a03d]">
              <Image
                src="/wines/logo1.jpg"
                alt="Wine & Co. Crest"
                width={48}
                height={48}
                className="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform"
                priority
              />
            </div>
            <div>
              <span className="text-2xl font-serif font-bold text-[#722f37] tracking-tight block leading-tight">
                Wine & Co.
              </span>
              <span className="text-[10px] text-[#c9a03d] font-bold tracking-widest uppercase block">
                Eswatini
              </span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-1 xl:gap-2">
            {navLinks.map((link) => {
              const isActive = pathname === link.href;
              const Icon = link.icon;
              return (
                <Link
                  key={link.href}
                  href={link.href}
                  className={`px-3 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-1.5 ${
                    isActive
                      ? 'bg-[#722f37] text-white shadow-sm'
                      : 'text-[#4a2c2a] hover:bg-[#722f37]/10 hover:text-[#722f37]'
                  }`}
                >
                  {Icon && <Icon className="w-4 h-4" />}
                  <span>{link.label}</span>
                  {link.badge && (
                    <span className="bg-[#c9a03d] text-[#1a1a2e] text-[10px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider">
                      {link.badge}
                    </span>
                  )}
                </Link>
              );
            })}
          </nav>

          {/* Actions */}
          <div className="flex items-center gap-3">
            <button
              onClick={() => setIsDrawerOpen(true)}
              className="relative p-2.5 rounded-xl text-[#722f37] hover:bg-[#722f37]/10 transition-colors flex items-center flex-shrink-0"
              aria-label="Open Shopping Bag"
            >
              <ShoppingBag className="w-6 h-6" />
              {totalCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-[#c9a03d] text-[#1a1a2e] text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-md animate-pulse">
                  {totalCount}
                </span>
              )}
            </button>

            <Link
              href="/admin"
              className="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold bg-[#1a0f0f] text-[#c9a03d] hover:bg-[#33181c] px-3.5 py-2 rounded-xl border border-[#c9a03d]/40 transition-colors shadow-sm whitespace-nowrap flex-shrink-0"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>Login Portal</span>
            </Link>

            {/* Mobile menu toggle */}
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 rounded-xl text-[#722f37] hover:bg-[#722f37]/10"
              aria-label="Toggle navigation menu"
            >
              {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Drawer */}
      {mobileMenuOpen && (
        <div className="lg:hidden bg-white border-b border-[#e8e0d8] px-4 pt-2 pb-6 space-y-2 shadow-xl animate-in slide-in-from-top-2">
          {navLinks.map((link) => {
            const isActive = pathname === link.href;
            const Icon = link.icon;
            return (
              <Link
                key={link.href}
                href={link.href}
                onClick={() => setMobileMenuOpen(false)}
                className={`flex items-center justify-between px-4 py-3 rounded-xl text-base font-medium ${
                  isActive
                    ? 'bg-[#722f37] text-white'
                    : 'text-[#4a2c2a] hover:bg-[#722f37]/10 hover:text-[#722f37]'
                }`}
              >
                <div className="flex items-center gap-2.5">
                  {Icon && <Icon className="w-5 h-5" />}
                  <span>{link.label}</span>
                </div>
                {link.badge && (
                  <span className="bg-[#c9a03d] text-[#1a1a2e] text-xs font-bold px-2 py-0.5 rounded-full">
                    {link.badge}
                  </span>
                )}
              </Link>
            );
          })}
        </div>
      )}
    </header>
  );
}
