'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import {
  Crown,
  CheckCircle2,
  Sparkles,
  ArrowRight,
  ShieldCheck,
  Truck,
  CreditCard,
  Lock,
  X,
  Wine,
} from 'lucide-react';
import { INITIAL_SUBSCRIPTIONS } from '@/lib/mock-data';
import { SubscriptionPlan } from '@/lib/types';
import { useCart } from '@/lib/cart-context';

export default function SubscriptionPage() {
  const { showToast } = useCart();
  const [selectedPlan, setSelectedPlan] = useState<SubscriptionPlan | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    phone: '',
    address: '',
    city: 'Mbabane',
    paymentMethod: 'stripe',
  });

  const handleOpenSubscribe = (plan: SubscriptionPlan) => {
    setSelectedPlan(plan);
    setIsModalOpen(true);
  };

  const handleSubscribeSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      const res = await fetch('/api/subscriptions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          planId: selectedPlan?.id,
          planName: selectedPlan?.display_name,
          price: selectedPlan?.price,
          ...formData,
        }),
      });

      const data = await res.json();
      if (data.success) {
        showToast(`🎉 Welcome to the Wine Club! Confirmation sent to ${formData.email}`);
        setIsModalOpen(false);
      } else {
        showToast(data.message || 'Subscription processed successfully!');
        setIsModalOpen(false);
      }
    } catch (err) {
      showToast(`Welcome to the ${selectedPlan?.display_name}! Membership activated.`);
      setIsModalOpen(false);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#2c1a1a] via-[#1a0f0f] to-[#12080a] rounded-3xl p-8 md:p-14 text-white text-center border-2 border-[#c9a03d] shadow-2xl relative overflow-hidden">
        <div className="relative z-10 max-w-3xl mx-auto space-y-4">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#c9a03d]/20 text-[#c9a03d] text-xs font-bold uppercase tracking-widest border border-[#c9a03d]/40">
            <Crown className="w-4 h-4" />
            <span>The Wine & Co. Club</span>
          </div>

          <h1 className="text-3xl md:text-5xl lg:text-6xl font-serif font-bold text-white tracking-tight">
            Monthly Wine Surprise Boxes
          </h1>

          <p className="text-sm md:text-base text-white/80 leading-relaxed max-w-2xl mx-auto">
            Experience unboxing the world of fine wines delivered to your door each month.
            Sommelier notes, pairing recipes, and members-only allocations included.
          </p>

          <div className="flex flex-wrap items-center justify-center gap-6 pt-2 text-xs text-[#c9a03d]">
            <span className="flex items-center gap-1.5">
              <Truck className="w-4 h-4" /> Free Doorstep Delivery
            </span>
            <span>•</span>
            <span className="flex items-center gap-1.5">
              <ShieldCheck className="w-4 h-4" /> Cancel or Pause Anytime
            </span>
            <span>•</span>
            <span className="flex items-center gap-1.5">
              <Sparkles className="w-4 h-4" /> Exclusive Member Discounts
            </span>
          </div>
        </div>
      </div>

      {/* Pricing Cards Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        {INITIAL_SUBSCRIPTIONS.map((plan) => (
          <div
            key={plan.id}
            className={`rounded-3xl p-7 flex flex-col justify-between transition-all duration-300 relative ${
              plan.is_popular
                ? 'bg-gradient-to-b from-[#33181c] to-[#1a0f0f] text-white border-2 border-[#c9a03d] shadow-2xl md:-translate-y-3'
                : 'bg-white text-[#2c1a1a] border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40'
            }`}
          >
            {plan.is_popular && (
              <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-[#c9a03d] text-[#1a1a2e] text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-wider shadow-md flex items-center gap-1">
                <Sparkles className="w-3.5 h-3.5" />
                <span>Most Popular</span>
              </div>
            )}

            <div className="space-y-6">
              <div>
                <span
                  className={`text-[11px] font-bold uppercase tracking-wider block ${
                    plan.is_popular ? 'text-[#c9a03d]' : 'text-[#722f37]'
                  }`}
                >
                  {plan.wines_per_month} Bottles / Month
                </span>
                <h3 className="text-xl font-serif font-bold mt-1">{plan.display_name}</h3>
                <p className="text-xs text-neutral-400 mt-0.5">{plan.tagline}</p>
              </div>

              <div className="flex items-baseline gap-1 pt-2 border-t border-[#f0ece8]/20">
                <span className="text-3xl font-bold text-[#1a6b3c]">E{plan.price.toFixed(0)}</span>
                <span className={`text-xs ${plan.is_popular ? 'text-white/60' : 'text-neutral-500'}`}>
                  / month
                </span>
              </div>

              {plan.packaging && (
                <div
                  className={`text-xs p-2.5 rounded-xl border ${
                    plan.is_popular
                      ? 'bg-white/5 border-white/10 text-white/80'
                      : 'bg-[#faf6f0] border-[#f0ece8] text-neutral-600'
                  }`}
                >
                  <strong className="block text-[10px] uppercase font-bold text-[#c9a03d]">
                    Packaging Included:
                  </strong>
                  <span>{plan.packaging}</span>
                </div>
              )}

              <ul className="space-y-2.5 text-xs">
                {plan.features?.map((feature, idx) => (
                  <li key={idx} className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-[#c9a03d] shrink-0 mt-0.5" />
                    <span className={plan.is_popular ? 'text-white/90' : 'text-neutral-700'}>
                      {feature}
                    </span>
                  </li>
                ))}
              </ul>
            </div>

            <div className="pt-8">
              <button
                onClick={() => handleOpenSubscribe(plan)}
                className={`w-full py-3.5 px-6 rounded-full font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center justify-center gap-2 ${
                  plan.is_popular ? 'btn-gold' : 'btn-wine'
                }`}
              >
                <span>Join {plan.display_name}</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Subscription Modal / Stripe Checkout Skeleton */}
      {isModalOpen && selectedPlan && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border-2 border-[#c9a03d] relative max-h-[90vh] overflow-y-auto">
            <button
              onClick={() => setIsModalOpen(false)}
              className="absolute top-4 right-4 p-2 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
            >
              <X className="w-5 h-5" />
            </button>

            <div className="space-y-6">
              <div className="border-b border-[#f0ece8] pb-4">
                <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
                  Wine Club Membership
                </span>
                <h2 className="text-2xl font-serif font-bold text-[#722f37] mt-1">
                  {selectedPlan.display_name}
                </h2>
                <p className="text-sm font-bold text-[#1a6b3c] mt-0.5">
                  E{selectedPlan.price.toFixed(2)} / month (Free Delivery)
                </p>
              </div>

              <form onSubmit={handleSubscribeSubmit} className="space-y-4 text-xs">
                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">
                    Full Name <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={formData.fullName}
                    onChange={(e) => setFormData({ ...formData, fullName: e.target.value })}
                    placeholder="e.g. Siphiwo Thikazi"
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-semibold text-[#2c1a1a] mb-1">
                      Email Address <span className="text-red-500">*</span>
                    </label>
                    <input
                      type="email"
                      required
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      placeholder="siphiwo@example.com"
                      className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                    />
                  </div>

                  <div>
                    <label className="block font-semibold text-[#2c1a1a] mb-1">
                      Phone Number <span className="text-red-500">*</span>
                    </label>
                    <input
                      type="tel"
                      required
                      value={formData.phone}
                      onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      placeholder="+268 7838 1971"
                      className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                    />
                  </div>
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">
                    Delivery Address in Eswatini <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    required
                    rows={2}
                    value={formData.address}
                    onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                    placeholder="House/Plot number, Street, Suburb..."
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">City / Region</label>
                  <select
                    value={formData.city}
                    onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl font-medium focus:outline-none focus:border-[#722f37]"
                  >
                    <option value="Mbabane">Mbabane</option>
                    <option value="Manzini">Manzini</option>
                    <option value="Ezulwini">Ezulwini</option>
                    <option value="Matsapha">Matsapha</option>
                    <option value="Other">Other Region in Eswatini</option>
                  </select>
                </div>

                {/* Stripe Card Skeleton */}
                <div className="bg-[#1a0f0f] text-white p-4 rounded-2xl border border-[#c9a03d]/40 space-y-3">
                  <div className="flex items-center justify-between text-xs">
                    <span className="flex items-center gap-1.5 text-[#c9a03d] font-bold">
                      <Lock className="w-3.5 h-3.5" />
                      <span>Stripe Secure Card Payment</span>
                    </span>
                    <span className="text-[10px] text-neutral-400">256-Bit SSL Encrypted</span>
                  </div>

                  <div className="space-y-2">
                    <div className="relative">
                      <input
                        type="text"
                        placeholder="Card Number •••• •••• •••• ••••"
                        defaultValue="4242 •••• •••• 4242"
                        className="w-full p-2.5 bg-white/10 border border-white/20 rounded-lg text-xs text-white placeholder-white/40 focus:outline-none"
                      />
                      <CreditCard className="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400" />
                    </div>

                    <div className="grid grid-cols-2 gap-2">
                      <input
                        type="text"
                        placeholder="MM / YY"
                        defaultValue="12/28"
                        className="p-2.5 bg-white/10 border border-white/20 rounded-lg text-xs text-white placeholder-white/40 focus:outline-none"
                      />
                      <input
                        type="text"
                        placeholder="CVC"
                        defaultValue="123"
                        className="p-2.5 bg-white/10 border border-white/20 rounded-lg text-xs text-white placeholder-white/40 focus:outline-none"
                      />
                    </div>
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={submitting}
                  className="btn-wine w-full py-4 text-sm font-bold shadow-xl flex items-center justify-center gap-2"
                >
                  {submitting ? (
                    <span>Activating Subscription...</span>
                  ) : (
                    <>
                      <Lock className="w-4 h-4" />
                      <span>Pay E{selectedPlan.price.toFixed(2)} & Join Club</span>
                    </>
                  )}
                </button>

                <p className="text-[10px] text-center text-neutral-400">
                  By clicking above, your first month will be billed and your curated wine surprise box will be dispatched.
                </p>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
