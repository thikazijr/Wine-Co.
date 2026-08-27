'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import { Gift, ShoppingBag, CheckCircle2, Send, Sparkles } from 'lucide-react';
import { INITIAL_CORPORATE_GIFTS } from '@/lib/mock-data';
import { useCart } from '@/lib/cart-context';

export default function CorporateGiftsPage() {
  const { addToCart, showToast } = useCart();
  const [enquirySent, setEnquirySent] = useState(false);
  const [enquiryData, setEnquiryData] = useState({
    companyName: '',
    contactPerson: '',
    email: '',
    phone: '',
    estimatedQuantity: '10 - 25 units',
    notes: '',
  });

  const handleEnquirySubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setEnquirySent(true);
    showToast('Corporate bespoke enquiry sent! Our sommelier will respond within 4 hours.');
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-14 text-white border-2 border-[#c9a03d] shadow-2xl relative overflow-hidden">
        <div className="relative z-10 max-w-3xl space-y-4">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#c9a03d]/20 text-[#c9a03d] text-xs font-bold uppercase tracking-widest border border-[#c9a03d]/40">
            <Gift className="w-4 h-4" />
            <span>Executive & Corporate Gifting</span>
          </div>

          <h1 className="text-3xl md:text-5xl lg:text-6xl font-serif font-bold text-white tracking-tight">
            Corporate Wine Collections
          </h1>

          <p className="text-sm md:text-base text-white/80 leading-relaxed max-w-2xl">
            Reward esteemed clients, executive board members, and valued partners with custom-branded,
            handcrafted wooden gift cases and rare estate allocations.
          </p>
        </div>
      </div>

      {/* Gift Tiers Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {INITIAL_CORPORATE_GIFTS.map((gift) => (
          <div
            key={gift.id}
            className="bg-white rounded-3xl p-6 border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40 transition-all duration-300 flex flex-col justify-between group"
          >
            <div className="space-y-5">
              <div className="relative h-60 bg-[#faf6f0] rounded-2xl overflow-hidden p-2 flex items-center justify-center border border-[#f0ece8]">
                <Image
                  src={gift.image_url}
                  alt={gift.name}
                  width={300}
                  height={220}
                  className="object-cover w-full h-full rounded-xl group-hover:scale-105 transition-transform duration-500"
                />
              </div>

              <div className="space-y-2">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-wider block">
                  {gift.tier} Tier • {gift.wines_included} Bottles
                </span>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a] group-hover:text-[#722f37] transition-colors">
                  {gift.name}
                </h3>
                <p className="text-xs text-neutral-600 leading-relaxed">{gift.description}</p>
                <div className="bg-[#faf6f0] p-3 rounded-xl border border-[#f0ece8] text-xs text-neutral-700">
                  <strong className="block text-[#722f37] mb-1">Includes:</strong>
                  <span>{gift.features}</span>
                </div>
              </div>
            </div>

            <div className="pt-6 mt-4 border-t border-[#f0ece8] flex items-center justify-between">
              <div>
                <span className="text-xs text-neutral-400 block">Unit Price</span>
                <span className="text-2xl font-bold text-[#1a6b3c]">
                  E{gift.price.toFixed(2)}
                </span>
              </div>

              <button
                onClick={() =>
                  addToCart({
                    product_id: gift.id,
                    product_type: 'corporate',
                    product_name: gift.name,
                    price: gift.price,
                    image_url: gift.image_url,
                  })
                }
                className="btn-wine text-xs px-4 py-2.5 shadow-md"
              >
                <ShoppingBag className="w-3.5 h-3.5" />
                <span>Order Now</span>
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Bespoke Corporate Enquiry Form */}
      <div className="bg-white rounded-3xl p-8 md:p-12 border border-[#f0ece8] shadow-lg max-w-3xl mx-auto space-y-6">
        <div className="text-center space-y-2">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
            Bespoke Volume Orders
          </span>
          <h2 className="text-2xl md:text-3xl font-serif font-bold text-[#2c1a1a]">
            Custom Corporate Branding & Bulk Enquiries
          </h2>
          <p className="text-xs md:text-sm text-neutral-600">
            Need 20+ personalized corporate hampers with engraved logos or custom greeting cards?
            Submit your enquiry below.
          </p>
        </div>

        {enquirySent ? (
          <div className="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center space-y-3">
            <CheckCircle2 className="w-12 h-12 text-emerald-600 mx-auto" />
            <h3 className="text-lg font-bold text-emerald-900">Enquiry Received</h3>
            <p className="text-xs text-emerald-700">
              Thank you! Our dedicated corporate wine concierge will be in touch shortly.
            </p>
          </div>
        ) : (
          <form onSubmit={handleEnquirySubmit} className="space-y-4 text-xs">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block font-semibold text-[#2c1a1a] mb-1">Company / Organization Name</label>
                <input
                  type="text"
                  required
                  value={enquiryData.companyName}
                  onChange={(e) => setEnquiryData({ ...enquiryData, companyName: e.target.value })}
                  placeholder="e.g. Standard Bank Eswatini"
                  className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#2c1a1a] mb-1">Contact Person</label>
                <input
                  type="text"
                  required
                  value={enquiryData.contactPerson}
                  onChange={(e) => setEnquiryData({ ...enquiryData, contactPerson: e.target.value })}
                  placeholder="e.g. Siphiwo Thikazi"
                  className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block font-semibold text-[#2c1a1a] mb-1">Official Email</label>
                <input
                  type="email"
                  required
                  value={enquiryData.email}
                  onChange={(e) => setEnquiryData({ ...enquiryData, email: e.target.value })}
                  placeholder="concierge@company.sz"
                  className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                />
              </div>

              <div>
                <label className="block font-semibold text-[#2c1a1a] mb-1">Phone Number</label>
                <input
                  type="tel"
                  required
                  value={enquiryData.phone}
                  onChange={(e) => setEnquiryData({ ...enquiryData, phone: e.target.value })}
                  placeholder="+268 7838 1971"
                  className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                />
              </div>
            </div>

            <div>
              <label className="block font-semibold text-[#2c1a1a] mb-1">Estimated Quantity Needed</label>
              <select
                value={enquiryData.estimatedQuantity}
                onChange={(e) => setEnquiryData({ ...enquiryData, estimatedQuantity: e.target.value })}
                className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl font-medium focus:outline-none focus:border-[#722f37]"
              >
                <option value="5 - 15 units">5 - 15 units</option>
                <option value="15 - 50 units">15 - 50 units</option>
                <option value="50 - 100+ units">50 - 100+ units (Custom branding included)</option>
              </select>
            </div>

            <div>
              <label className="block font-semibold text-[#2c1a1a] mb-1">Special Requirements / Custom Packaging</label>
              <textarea
                rows={3}
                value={enquiryData.notes}
                onChange={(e) => setEnquiryData({ ...enquiryData, notes: e.target.value })}
                placeholder="Mention desired wine preference (Red/White), engraving, or delivery deadline..."
                className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
              />
            </div>

            <button
              type="submit"
              className="btn-wine w-full py-3.5 text-sm font-bold shadow-lg flex items-center justify-center gap-2"
            >
              <Send className="w-4 h-4" />
              <span>Submit Corporate Request</span>
            </button>
          </form>
        )}
      </div>
    </div>
  );
}
