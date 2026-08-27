'use client';

import React from 'react';
import Image from 'next/image';
import { UtensilsCrossed, ShoppingBag, Sparkles, Wine as WineIcon } from 'lucide-react';
import { INITIAL_PAIRINGS } from '@/lib/mock-data';
import { useCart } from '@/lib/cart-context';

export default function PairingsPage() {
  const { addToCart } = useCart();

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-12 text-white border-2 border-[#c9a03d]/40 shadow-xl relative overflow-hidden">
        <div className="relative z-10 max-w-2xl space-y-3">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest flex items-center gap-1.5">
            <UtensilsCrossed className="w-4 h-4" />
            <span>Epicurean Pairings</span>
          </span>
          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Artisan Food & Wine Pairings
          </h1>
          <p className="text-sm md:text-base text-white/80">
            Elevate your tasting experience with sommelier-matched artisan cheeses, Belgian dark chocolate truffles, prime biltong, and gourmet delicacies.
          </p>
        </div>
      </div>

      {/* Pairings Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {INITIAL_PAIRINGS.map((pairing) => (
          <div
            key={pairing.id}
            className="bg-white rounded-3xl p-5 border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40 transition-all duration-300 flex flex-col justify-between group"
          >
            <div className="space-y-4">
              <div className="relative h-56 bg-[#faf6f0] rounded-2xl overflow-hidden p-2 flex items-center justify-center border border-[#f0ece8]">
                <Image
                  src={pairing.image_url}
                  alt={pairing.name}
                  width={240}
                  height={200}
                  className="object-cover w-full h-full rounded-xl group-hover:scale-105 transition-transform duration-500"
                />
              </div>

              <div className="space-y-1.5">
                <h3 className="text-lg font-serif font-bold text-[#2c1a1a] group-hover:text-[#722f37] transition-colors">
                  {pairing.name}
                </h3>
                <p className="text-xs text-neutral-600 leading-relaxed">{pairing.description}</p>
                {pairing.compatible_wines && (
                  <div className="pt-2">
                    <span className="text-[11px] font-bold text-[#722f37] block">
                      Recommended With:
                    </span>
                    <span className="text-xs text-[#c9a03d] font-medium">
                      {pairing.compatible_wines}
                    </span>
                  </div>
                )}
              </div>
            </div>

            <div className="pt-4 mt-4 border-t border-[#f0ece8] flex items-center justify-between">
              <div>
                <span className="text-xs text-neutral-400 block">Price</span>
                <span className="text-lg font-bold text-[#1a6b3c]">
                  E{pairing.price.toFixed(2)}
                </span>
              </div>

              <button
                onClick={() =>
                  addToCart({
                    product_id: pairing.id,
                    product_type: 'pairing',
                    product_name: pairing.name,
                    price: pairing.price,
                    image_url: pairing.image_url,
                  })
                }
                className="btn-wine text-xs px-3.5 py-2"
              >
                <ShoppingBag className="w-3.5 h-3.5" />
                <span>Add to Bag</span>
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
