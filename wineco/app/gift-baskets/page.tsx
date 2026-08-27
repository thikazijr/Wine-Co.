'use client';

import React from 'react';
import Image from 'next/image';
import { Package, ShoppingBag, Sparkles, Heart } from 'lucide-react';
import { INITIAL_GIFT_BASKETS } from '@/lib/mock-data';
import { useCart } from '@/lib/cart-context';

export default function GiftBasketsPage() {
  const { addToCart } = useCart();

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-12 text-white border-2 border-[#c9a03d]/40 shadow-xl relative overflow-hidden">
        <div className="relative z-10 max-w-2xl space-y-3">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest flex items-center gap-1.5">
            <Package className="w-4 h-4" />
            <span>Celebration & Milestone Hampers</span>
          </span>
          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Luxury Wine Gift Baskets
          </h1>
          <p className="text-sm md:text-base text-white/80">
            Handmade wicker and presentation baskets overflowing with award-winning estate wines,
            fine charcuterie, artisanal chocolates, and crystal accessories.
          </p>
        </div>
      </div>

      {/* Gift Baskets Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {INITIAL_GIFT_BASKETS.map((basket) => (
          <div
            key={basket.id}
            className="bg-white rounded-3xl p-6 border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40 transition-all duration-300 flex flex-col justify-between group"
          >
            <div className="space-y-5">
              <div className="relative h-64 bg-[#faf6f0] rounded-2xl overflow-hidden p-2 flex items-center justify-center border border-[#f0ece8]">
                <Image
                  src={basket.image_url}
                  alt={basket.name}
                  width={300}
                  height={240}
                  className="object-cover w-full h-full rounded-xl group-hover:scale-105 transition-transform duration-500"
                />
              </div>

              <div className="space-y-2">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-wider block">
                  Hamper • {basket.wines_included} Estate Bottles
                </span>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a] group-hover:text-[#722f37] transition-colors">
                  {basket.name}
                </h3>
                <p className="text-xs text-neutral-600 leading-relaxed">{basket.description}</p>
                <div className="bg-[#faf6f0] p-3 rounded-xl border border-[#f0ece8] text-xs text-neutral-700">
                  <strong className="block text-[#722f37] mb-1">Hamper Inclusions:</strong>
                  <span>{basket.features}</span>
                </div>
              </div>
            </div>

            <div className="pt-6 mt-4 border-t border-[#f0ece8] flex items-center justify-between">
              <div>
                <span className="text-xs text-neutral-400 block">Hamper Price</span>
                <span className="text-2xl font-bold text-[#1a6b3c]">
                  E{basket.price.toFixed(2)}
                </span>
              </div>

              <button
                onClick={() =>
                  addToCart({
                    product_id: basket.id,
                    product_type: 'basket',
                    product_name: basket.name,
                    price: basket.price,
                    image_url: basket.image_url,
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
    </div>
  );
}
