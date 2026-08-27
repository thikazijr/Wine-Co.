'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import {
  Wine as WineIcon,
  Crown,
  Sparkles,
  ChevronLeft,
  ChevronRight,
  ArrowRight,
  ShieldCheck,
  Truck,
  Award,
  BookOpen,
  ShoppingBag,
  Gift,
  CheckCircle2,
} from 'lucide-react';
import { INITIAL_WINES, INITIAL_SUBSCRIPTIONS } from '@/lib/mock-data';
import { useCart } from '@/lib/cart-context';

export default function HomePage() {
  const { addToCart } = useCart();
  const [currentSlide, setCurrentSlide] = useState(0);

  const heroSlides = [
    {
      title: 'Cloudy Bay',
      subtitle: 'Premium Marlborough Sauvignon Blanc',
      tagline: 'Vibrant, Aromatic & Crisp New Zealand Masterpiece',
      image: '/wines/cloudybay.png',
      price: 'E615.00',
      link: '/shop',
    },
    {
      title: 'Kanonkop Pinotage',
      subtitle: "South Africa's Flagship Vintage",
      tagline: 'Bold, Complex & Aged in Classic French Oak',
      image: '/wines/kanonkop.png',
      price: 'E345.00',
      link: '/shop',
    },
    {
      title: 'Grand Vin de Bordeaux',
      subtitle: 'Médoc Classified Growth Selection',
      tagline: 'Prestige French Heritage with Extraordinary Depth',
      image: '/wines/grand-vin-bordeaux-medoc.png',
      price: 'E416.00',
      link: '/shop',
    },
    {
      title: 'Opus One',
      subtitle: 'Iconic Napa Valley Masterpiece',
      tagline: 'The Legendary Collaboration of Mondavi & Rothschild',
      image: '/wines/opusone.png',
      price: 'E745.00',
      link: '/shop',
    },
  ];

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % heroSlides.length);
    }, 5500);
    return () => clearInterval(timer);
  }, [heroSlides.length]);

  const featuredWines = INITIAL_WINES.slice(0, 4);

  return (
    <div className="space-y-16 pb-20">
      {/* ==================== HERO SLIDESHOW ==================== */}
      <section className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] min-h-[480px] md:min-h-[540px] flex items-center border border-[#c9a03d]/30">
          {/* Slide Background Visual */}
          <div className="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div className="absolute top-1/2 left-1/3 -translate-y-1/2 w-96 h-96 bg-[#c9a03d] rounded-full blur-3xl" />
            <div className="absolute top-1/4 right-1/4 w-80 h-80 bg-[#722f37] rounded-full blur-3xl" />
          </div>

          {/* Slide Content Grid */}
          <div className="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-8 items-center p-8 md:p-14 w-full">
            <div className="md:col-span-7 text-white space-y-5">
              <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#c9a03d]/20 border border-[#c9a03d]/40 text-[#c9a03d] text-xs font-semibold uppercase tracking-wider">
                <Sparkles className="w-3.5 h-3.5" />
                <span>Featured Sommelier Reserve</span>
              </div>

              <div className="space-y-2">
                <h1 className="text-3xl md:text-5xl lg:text-6xl font-serif font-bold text-white tracking-tight leading-tight">
                  {heroSlides[currentSlide].title}
                </h1>
                <p className="text-lg md:text-xl text-[#c9a03d] font-medium font-serif">
                  {heroSlides[currentSlide].subtitle}
                </p>
                <p className="text-sm md:text-base text-white/80 max-w-lg leading-relaxed">
                  {heroSlides[currentSlide].tagline}
                </p>
              </div>

              <div className="flex flex-wrap items-center gap-4 pt-2">
                <span className="text-2xl md:text-3xl font-bold text-white">
                  {heroSlides[currentSlide].price}
                </span>
                <Link
                  href={heroSlides[currentSlide].link}
                  className="btn-gold shadow-xl"
                >
                  <span>Explore Wines</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
                <Link
                  href="/subscription"
                  className="px-6 py-2.5 rounded-full border border-white/30 hover:border-white text-white text-sm font-medium transition-all hover:bg-white/10"
                >
                  Join Wine Club
                </Link>
              </div>
            </div>

            {/* Bottle Image Showcase Container */}
            <div className="md:col-span-5 flex justify-center items-center w-full">
              <div className="w-full max-w-[340px] md:max-w-[360px] bg-white rounded-3xl p-5 shadow-2xl border-2 border-[#c9a03d]/40 relative overflow-hidden flex flex-col justify-between h-[470px] md:h-[490px]">
                <div className="flex items-center justify-between w-full z-10">
                  <span className="bg-[#722f37] text-[#c9a03d] text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    {heroSlides[currentSlide].subtitle}
                  </span>
                  <span className="text-sm font-extrabold text-[#1a6b3c]">
                    {heroSlides[currentSlide].price}
                  </span>
                </div>

                <div className="relative flex-1 flex items-center justify-center my-2 group">
                  <div className="absolute w-40 h-40 bg-[#c9a03d]/15 rounded-full blur-2xl pointer-events-none" />
                  <Image
                    src={heroSlides[currentSlide].image}
                    alt={heroSlides[currentSlide].title}
                    width={260}
                    height={340}
                    className="object-contain max-h-60 md:max-h-64 drop-shadow-[0_20px_25px_rgba(0,0,0,0.3)] transition-all duration-500 transform group-hover:scale-105"
                    priority
                  />
                </div>

                <div className="space-y-2 pt-2 border-t border-[#f5efe8] z-10">
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="font-serif font-bold text-base text-[#2c1a1a] leading-tight">
                        {heroSlides[currentSlide].title}
                      </h4>
                      <p className="text-xs text-neutral-500">
                        {heroSlides[currentSlide].tagline}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Slide Navigation Controls */}
          <button
            onClick={() =>
              setCurrentSlide((prev) => (prev === 0 ? heroSlides.length - 1 : prev - 1))
            }
            className="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/40 hover:bg-black/70 text-white border border-white/20 flex items-center justify-center backdrop-blur-xs transition-transform hover:scale-105"
            aria-label="Previous Slide"
          >
            <ChevronLeft className="w-6 h-6" />
          </button>
          <button
            onClick={() => setCurrentSlide((prev) => (prev + 1) % heroSlides.length)}
            className="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/40 hover:bg-black/70 text-white border border-white/20 flex items-center justify-center backdrop-blur-xs transition-transform hover:scale-105"
            aria-label="Next Slide"
          >
            <ChevronRight className="w-6 h-6" />
          </button>

          {/* Dots Indicator */}
          <div className="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            {heroSlides.map((_, idx) => (
              <button
                key={idx}
                onClick={() => setCurrentSlide(idx)}
                className={`h-2.5 rounded-full transition-all ${
                  currentSlide === idx ? 'w-8 bg-[#c9a03d]' : 'w-2.5 bg-white/40 hover:bg-white/70'
                }`}
                aria-label={`Go to slide ${idx + 1}`}
              />
            ))}
          </div>
        </div>
      </section>

      {/* ==================== TRUST BADGES ==================== */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white p-8 rounded-3xl border border-[#f0ece8] shadow-sm">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 rounded-2xl bg-[#722f37]/10 flex items-center justify-center shrink-0 text-[#722f37]">
              <Truck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-semibold text-[#2c1a1a]">Doorstep Delivery</h4>
              <p className="text-xs text-neutral-500">
                Complimentary delivery in Eswatini on orders over E600
              </p>
            </div>
          </div>

          <div className="flex items-center gap-4 border-y md:border-y-0 md:border-x border-[#f0ece8] py-4 md:py-0 md:px-6">
            <div className="w-12 h-12 rounded-2xl bg-[#c9a03d]/15 flex items-center justify-center shrink-0 text-[#c9a03d]">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-semibold text-[#2c1a1a]">Cellar-Conditioned</h4>
              <p className="text-xs text-neutral-500">
                100% authentic temperature-controlled estate imports
              </p>
            </div>
          </div>

          <div className="flex items-center gap-4">
            <div className="w-12 h-12 rounded-2xl bg-[#1a6b3c]/10 flex items-center justify-center shrink-0 text-[#1a6b3c]">
              <Award className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-semibold text-[#2c1a1a]">Sommelier Curated</h4>
              <p className="text-xs text-neutral-500">
                Award-winning South African & international vintages
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* ==================== FEATURED WINES ==================== */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest block mb-1">
              Hand-Picked Selection
            </span>
            <h2 className="text-3xl md:text-4xl font-serif font-bold text-[#2c1a1a]">
              Featured Cellar Wines
            </h2>
            <p className="text-neutral-600 text-sm mt-1">
              Discover renowned estate wines available for immediate delivery.
            </p>
          </div>
          <Link
            href="/shop"
            className="inline-flex items-center gap-2 text-sm font-semibold text-[#722f37] hover:text-[#552127] transition-colors"
          >
            <span>View All Wines</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {featuredWines.map((wine) => (
            <div
              key={wine.id}
              className="bg-white rounded-3xl p-5 border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40 transition-all duration-300 flex flex-col justify-between group"
            >
              <div className="space-y-4">
                <div className="relative h-64 bg-[#faf6f0] rounded-2xl p-4 flex items-center justify-center overflow-hidden border border-[#f5efe8]">
                  <span className="absolute top-3 left-3 bg-[#722f37] text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    {wine.vintage} Vintage
                  </span>
                  <Image
                    src={wine.image_url}
                    alt={wine.name}
                    width={180}
                    height={240}
                    className="object-contain max-h-56 group-hover:scale-108 transition-transform duration-500 drop-shadow-md"
                  />
                </div>

                <div className="space-y-1">
                  <span className="text-xs text-[#c9a03d] font-semibold tracking-wide block">
                    {wine.variety}
                  </span>
                  <h3 className="text-lg font-serif font-bold text-[#2c1a1a] group-hover:text-[#722f37] transition-colors">
                    {wine.name}
                  </h3>
                  <p className="text-xs text-neutral-500">{wine.origin}</p>
                </div>
              </div>

              <div className="pt-5 mt-4 border-t border-[#f0ece8] flex items-center justify-between">
                <div>
                  <span className="text-xs text-neutral-400 block">Price</span>
                  <span className="text-xl font-bold text-[#1a6b3c]">
                    E{wine.price.toFixed(2)}
                  </span>
                </div>

                <button
                  onClick={() =>
                    addToCart({
                      product_id: wine.id,
                      product_type: 'wine',
                      product_name: wine.name,
                      price: wine.price,
                      image_url: wine.image_url,
                    })
                  }
                  className="btn-wine text-xs px-4 py-2.5 shadow-md"
                >
                  <ShoppingBag className="w-3.5 h-3.5" />
                  <span>Add to Bag</span>
                </button>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ==================== WINE CLUB SURPRISE BOXES ==================== */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-gradient-to-br from-[#2c1a1a] via-[#1a0f0f] to-[#12080a] rounded-3xl p-8 md:p-14 text-white shadow-2xl border-2 border-[#c9a03d]">
          <div className="text-center max-w-2xl mx-auto space-y-3 mb-12">
            <span className="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#c9a03d]/20 text-[#c9a03d] text-xs font-bold uppercase tracking-widest">
              <Crown className="w-3.5 h-3.5" />
              <span>The Wine & Co. Club</span>
            </span>
            <h2 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
              Monthly Surprise Wine Boxes
            </h2>
            <p className="text-sm md:text-base text-white/70">
              Unbox sommelier-curated reserve bottles delivered straight to your door each month.
              Cancel or change anytime.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {INITIAL_SUBSCRIPTIONS.slice(0, 3).map((plan) => (
              <div
                key={plan.id}
                className={`rounded-3xl p-8 flex flex-col justify-between transition-all duration-300 relative ${
                  plan.is_popular
                    ? 'bg-gradient-to-b from-white/15 to-white/5 border-2 border-[#c9a03d] shadow-2xl transform md:-translate-y-2'
                    : 'bg-white/5 border border-white/10 hover:border-white/25'
                }`}
              >
                {plan.is_popular && (
                  <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#c9a03d] text-[#1a1a2e] text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow-lg flex items-center gap-1.5">
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>Most Popular</span>
                  </div>
                )}

                <div className="space-y-6">
                  <div>
                    <h3 className="text-xl font-serif font-bold text-white">{plan.display_name}</h3>
                    <p className="text-xs text-[#c9a03d] mt-1">{plan.tagline}</p>
                  </div>

                  <div className="flex items-baseline gap-1">
                    <span className="text-4xl font-bold text-white">E{plan.price.toFixed(0)}</span>
                    <span className="text-xs text-white/60 font-medium">/ month</span>
                  </div>

                  <p className="text-xs text-white/80 leading-relaxed">{plan.description}</p>

                  <ul className="space-y-3 text-xs text-white/90">
                    {plan.features?.map((feature, idx) => (
                      <li key={idx} className="flex items-center gap-2.5">
                        <CheckCircle2 className="w-4 h-4 text-[#c9a03d] shrink-0" />
                        <span>{feature}</span>
                      </li>
                    ))}
                  </ul>
                </div>

                <div className="pt-8">
                  <Link
                    href={`/subscription?plan=${plan.tier_name}`}
                    className={`w-full text-center text-sm py-3 px-6 rounded-full font-semibold transition-all flex items-center justify-center gap-2 ${
                      plan.is_popular
                        ? 'btn-gold shadow-xl'
                        : 'bg-white/10 hover:bg-white/20 text-white border border-white/20'
                    }`}
                  >
                    <span>Subscribe Now</span>
                    <ArrowRight className="w-4 h-4" />
                  </Link>
                </div>
              </div>
            ))}
          </div>

          <div className="text-center mt-10">
            <Link
              href="/subscription"
              className="text-xs text-[#c9a03d] hover:underline font-semibold tracking-wider uppercase"
            >
              Compare all 4 Membership Tiers including Grand Reserve Society →
            </Link>
          </div>
        </div>
      </section>

      {/* ==================== BOUTIQUE MAGAZINE BANNER ==================== */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-white rounded-3xl p-8 md:p-12 border border-[#f0ece8] shadow-sm grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
          <div className="md:col-span-5 flex justify-center">
            <div className="relative w-64 md:w-72 rounded-2xl overflow-hidden shadow-2xl border-4 border-[#faf6f0]">
              <Image
                src="/images/magazine-cover.jpg"
                alt="Wine & Co. Boutique Magazine"
                width={300}
                height={400}
                className="w-full object-cover"
              />
            </div>
          </div>

          <div className="md:col-span-7 space-y-4">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200">
              <BookOpen className="w-3.5 h-3.5" />
              <span>Free to Read Online • E45 to Download PDF</span>
            </div>

            <h2 className="text-3xl md:text-4xl font-serif font-bold text-[#2c1a1a]">
              The Wine & Co. Boutique Magazine
            </h2>
            <p className="text-sm text-neutral-600 leading-relaxed">
              Your comprehensive guide to the Cape Winelands, sommelier pairing secrets, vintage
              cellaring strategies, and curated recipes crafted for discerning wine lovers in Eswatini.
            </p>

            <div className="grid grid-cols-2 gap-4 py-2 text-xs">
              <div className="bg-[#faf6f0] p-3.5 rounded-xl border border-[#f0ece8]">
                <strong className="text-[#722f37] block font-serif text-sm">24 Full Pages</strong>
                <span className="text-neutral-500">In-depth wine guides & tasting notes</span>
              </div>
              <div className="bg-[#faf6f0] p-3.5 rounded-xl border border-[#f0ece8]">
                <strong className="text-[#722f37] block font-serif text-sm">Sommelier Secrets</strong>
                <span className="text-neutral-500">Food pairing & cellaring advice</span>
              </div>
            </div>

            <div className="flex flex-wrap gap-4 pt-2">
              <Link href="/magazine" className="btn-wine text-sm">
                <BookOpen className="w-4 h-4" />
                <span>Read Magazine Online Free</span>
              </Link>
              <Link href="/magazine#download" className="btn-gold text-sm">
                <span>Download PDF (E45)</span>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
