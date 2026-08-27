import React from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { Wine, Award, ShieldCheck, Heart, ArrowRight } from 'lucide-react';

export default function AboutPage() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
      {/* Hero Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-14 text-white text-center border-2 border-[#c9a03d] shadow-2xl relative overflow-hidden">
        <div className="relative z-10 max-w-3xl mx-auto space-y-4">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
            The Wine & Co. Story
          </span>
          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Curated. Crafted. Delivered.
          </h1>
          <p className="text-sm md:text-base text-white/80 leading-relaxed">
            Founded with a passion for world-class viticulture, Wine & Co. connects wine lovers
            in Eswatini with prestigious estates across South Africa, Bordeaux, Marlborough, Napa Valley, and beyond.
          </p>
        </div>
      </div>

      {/* Story Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        <div className="lg:col-span-6 space-y-5 text-sm text-neutral-700 leading-relaxed">
          <h2 className="text-3xl font-serif font-bold text-[#722f37]">
            Our Commitment to Wine Excellence
          </h2>
          <p>
            Wine is more than a beverage — it is a living history of soil, climate, passion, and heritage.
            At Wine & Co., every single bottle in our cellar is personally evaluated and imported in
            temperature-controlled environments to preserve delicate aromas and structure.
          </p>
          <p>
            Whether you are unboxing our monthly Wine Surprise Boxes, selecting an exceptional vintage
            for an anniversary dinner, or sending a bespoke wooden corporate gift case, we guarantee
            uncompromised quality and authentic provenance.
          </p>
          <div className="pt-2">
            <Link href="/shop" className="btn-wine text-xs px-6 py-3 shadow-md inline-flex items-center gap-2">
              <span>Explore Our Cellar</span>
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>
        </div>

        <div className="lg:col-span-6 flex justify-center">
          <div className="relative w-full max-w-md h-80 md:h-96 rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
            <Image
              src="/wines/glasswin.jpg"
              alt="Wine & Co. Cellar Tasting"
              width={500}
              height={400}
              className="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </div>
  );
}
