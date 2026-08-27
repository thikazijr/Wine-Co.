'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import {
  CreditCard,
  Truck,
  Building2,
  Lock,
  CheckCircle2,
  ShieldCheck,
  ArrowRight,
  ShoppingBag,
} from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export default function CheckoutPage() {
  const router = useRouter();
  const { cart, subtotal, deliveryFee, grandTotal, clearCart, showToast } = useCart();
  const [submitting, setSubmitting] = useState(false);

  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    phone: '',
    address: '',
    city: 'Mbabane',
    paymentMethod: 'cash_on_delivery',
    notes: '',
  });

  if (cart.length === 0) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center space-y-4">
        <h2 className="text-2xl font-serif font-bold text-[#2c1a1a]">Your bag is empty</h2>
        <p className="text-sm text-neutral-500">Please add wines to your bag before checking out.</p>
        <button onClick={() => router.push('/shop')} className="btn-wine text-xs px-6 py-2.5">
          Browse Wine Cellar
        </button>
      </div>
    );
  }

  const handleSubmitOrder = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    const orderNumber = `ORD-${Date.now().toString().slice(-6)}-${Math.floor(1000 + Math.random() * 9000)}`;

    try {
      const res = await fetch('/api/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          orderNumber,
          customerName: formData.fullName,
          customerEmail: formData.email,
          customerPhone: formData.phone,
          customerAddress: formData.address,
          city: formData.city,
          paymentMethod: formData.paymentMethod,
          items: cart,
          subtotal,
          deliveryFee,
          grandTotal,
          notes: formData.notes,
        }),
      });

      const data = await res.json();

      // Store in localStorage for confirmation display
      const orderSummary = {
        orderNumber,
        customerName: formData.fullName,
        customerEmail: formData.email,
        customerPhone: formData.phone,
        customerAddress: formData.address,
        city: formData.city,
        paymentMethod: formData.paymentMethod,
        items: cart,
        subtotal,
        deliveryFee,
        grandTotal,
        date: new Date().toISOString(),
      };

      localStorage.setItem('wineco_last_order', JSON.stringify(orderSummary));
      clearCart();
      showToast(`Order ${orderNumber} placed successfully!`);
      router.push(`/order-confirmation?order=${orderNumber}`);
    } catch (err) {
      console.error('Order placement fallback', err);
      // Fallback local persistence
      const orderSummary = {
        orderNumber,
        customerName: formData.fullName,
        customerEmail: formData.email,
        customerPhone: formData.phone,
        customerAddress: formData.address,
        city: formData.city,
        paymentMethod: formData.paymentMethod,
        items: cart,
        subtotal,
        deliveryFee,
        grandTotal,
        date: new Date().toISOString(),
      };

      localStorage.setItem('wineco_last_order', JSON.stringify(orderSummary));
      clearCart();
      router.push(`/order-confirmation?order=${orderNumber}`);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
      <div className="border-b border-[#f0ece8] pb-4">
        <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
          Secure Step 2 of 2
        </span>
        <h1 className="text-3xl md:text-4xl font-serif font-bold text-[#2c1a1a] mt-1">
          Delivery & Payment Checkout
        </h1>
      </div>

      <form onSubmit={handleSubmitOrder}>
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          {/* Left: Customer Info & Payment */}
          <div className="lg:col-span-7 space-y-8">
            {/* Delivery Address Details */}
            <div className="bg-white rounded-3xl p-6 md:p-8 border border-[#f0ece8] shadow-sm space-y-4">
              <div className="flex items-center gap-2 border-b border-[#f0ece8] pb-3 text-[#722f37]">
                <Truck className="w-5 h-5" />
                <h2 className="text-lg font-serif font-bold text-[#2c1a1a]">
                  1. Delivery Details
                </h2>
              </div>

              <div className="space-y-4 text-xs">
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
                    className="w-full p-3.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
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
                      className="w-full p-3.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
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
                      className="w-full p-3.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                    />
                  </div>
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">
                    Street & House / Office Address in Eswatini <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    required
                    rows={2}
                    value={formData.address}
                    onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                    placeholder="Plot / House number, Street name, Complex name..."
                    className="w-full p-3.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">City / Region</label>
                  <select
                    value={formData.city}
                    onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                    className="w-full p-3.5 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl font-medium focus:outline-none focus:border-[#722f37]"
                  >
                    <option value="Mbabane">Mbabane</option>
                    <option value="Manzini">Manzini</option>
                    <option value="Ezulwini">Ezulwini</option>
                    <option value="Matsapha">Matsapha</option>
                    <option value="Nhlangano">Nhlangano</option>
                    <option value="Siteki">Siteki</option>
                    <option value="Piggs Peak">Piggs Peak</option>
                  </select>
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">
                    Special Delivery Instructions (Optional)
                  </label>
                  <input
                    type="text"
                    value={formData.notes}
                    onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                    placeholder="e.g. Leave at reception or call upon gate arrival..."
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>
              </div>
            </div>

            {/* Payment Method Selector */}
            <div className="bg-white rounded-3xl p-6 md:p-8 border border-[#f0ece8] shadow-sm space-y-5">
              <div className="flex items-center gap-2 border-b border-[#f0ece8] pb-3 text-[#722f37]">
                <CreditCard className="w-5 h-5" />
                <h2 className="text-lg font-serif font-bold text-[#2c1a1a]">
                  2. Select Payment Method
                </h2>
              </div>

              <div className="space-y-3 text-xs">
                {/* Option 1: Cash on Delivery */}
                <label
                  onClick={() => setFormData({ ...formData, paymentMethod: 'cash_on_delivery' })}
                  className={`flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                    formData.paymentMethod === 'cash_on_delivery'
                      ? 'border-[#722f37] bg-[#faf6f0]'
                      : 'border-[#e8e0d8] hover:border-[#c9a03d]'
                  }`}
                >
                  <input
                    type="radio"
                    name="paymentMethod"
                    checked={formData.paymentMethod === 'cash_on_delivery'}
                    onChange={() => {}}
                    className="mt-1 text-[#722f37] focus:ring-[#722f37]"
                  />
                  <div>
                    <strong className="block text-sm font-bold text-[#2c1a1a]">
                      💵 Cash on Delivery
                    </strong>
                    <span className="text-neutral-500">
                      Pay with cash or local POS card terminal when our courier hands over your wine.
                    </span>
                  </div>
                </label>

                {/* Option 2: Bank Transfer (EFT) */}
                <label
                  onClick={() => setFormData({ ...formData, paymentMethod: 'bank_transfer' })}
                  className={`flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                    formData.paymentMethod === 'bank_transfer'
                      ? 'border-[#722f37] bg-[#faf6f0]'
                      : 'border-[#e8e0d8] hover:border-[#c9a03d]'
                  }`}
                >
                  <input
                    type="radio"
                    name="paymentMethod"
                    checked={formData.paymentMethod === 'bank_transfer'}
                    onChange={() => {}}
                    className="mt-1 text-[#722f37] focus:ring-[#722f37]"
                  />
                  <div className="space-y-2 flex-1">
                    <strong className="block text-sm font-bold text-[#2c1a1a]">
                      🏦 Direct Bank Electronic Transfer (EFT)
                    </strong>
                    <span className="text-neutral-500 block">
                      Transfer directly to Wine & Co. Standard Bank / FNB Eswatini account.
                    </span>

                    {formData.paymentMethod === 'bank_transfer' && (
                      <div className="bg-white p-3.5 rounded-xl border border-[#e8e0d8] text-[11px] text-neutral-700 space-y-1">
                        <p className="font-bold text-[#722f37]">Wine & Co. Banking Details:</p>
                        <p>Bank: Standard Bank Eswatini</p>
                        <p>Account Name: Wine & Co. Eswatini</p>
                        <p>Account No: 9110004928172</p>
                        <p>Branch Code: 660164</p>
                        <p className="text-emerald-700 font-semibold pt-1">
                          Use your Phone Number as payment reference.
                        </p>
                      </div>
                    )}
                  </div>
                </label>

                {/* Option 3: Stripe Card Skeleton */}
                <label
                  onClick={() => setFormData({ ...formData, paymentMethod: 'stripe' })}
                  className={`flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                    formData.paymentMethod === 'stripe'
                      ? 'border-[#722f37] bg-[#faf6f0]'
                      : 'border-[#e8e0d8] hover:border-[#c9a03d]'
                  }`}
                >
                  <input
                    type="radio"
                    name="paymentMethod"
                    checked={formData.paymentMethod === 'stripe'}
                    onChange={() => {}}
                    className="mt-1 text-[#722f37] focus:ring-[#722f37]"
                  />
                  <div className="space-y-2 flex-1">
                    <div className="flex items-center justify-between">
                      <strong className="text-sm font-bold text-[#2c1a1a] flex items-center gap-1.5">
                        <Lock className="w-3.5 h-3.5 text-[#c9a03d]" />
                        <span>Credit / Debit Card (Stripe SSL)</span>
                      </strong>
                      <span className="text-[10px] bg-[#c9a03d] text-[#1a1a2e] font-bold px-2 py-0.5 rounded-full">
                        Instant Confirmation
                      </span>
                    </div>
                    <span className="text-neutral-500 block">
                      Pay securely with Visa, Mastercard, or International Cards.
                    </span>

                    {formData.paymentMethod === 'stripe' && (
                      <div className="bg-[#1a0f0f] text-white p-4 rounded-xl border border-[#c9a03d]/40 space-y-2.5">
                        <input
                          type="text"
                          placeholder="Card Number •••• •••• •••• ••••"
                          defaultValue="4242 •••• •••• 4242"
                          className="w-full p-2.5 bg-white/10 border border-white/20 rounded-lg text-xs text-white placeholder-white/40 focus:outline-none"
                        />
                        <div className="grid grid-cols-2 gap-2">
                          <input
                            type="text"
                            placeholder="MM/YY"
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
                    )}
                  </div>
                </label>
              </div>
            </div>
          </div>

          {/* Right: Order Summary Sidebar */}
          <div className="lg:col-span-5 bg-white rounded-3xl p-6 md:p-8 border border-[#f0ece8] shadow-xl space-y-6 sticky top-24">
            <h2 className="text-lg font-serif font-bold text-[#2c1a1a] border-b border-[#f0ece8] pb-3">
              Order Review ({cart.length} items)
            </h2>

            <div className="max-h-60 overflow-y-auto space-y-3 divide-y divide-[#f0ece8] pr-1">
              {cart.map((item) => (
                <div
                  key={`${item.product_type}_${item.product_id}`}
                  className="pt-3 first:pt-0 flex items-center justify-between gap-3 text-xs"
                >
                  <div className="flex items-center gap-3">
                    <div className="relative w-12 h-14 bg-[#faf6f0] rounded-lg overflow-hidden shrink-0 border border-[#e8e0d8] flex items-center justify-center p-0.5">
                      <Image
                        src={item.image_url || '/wines/margaux.jpg'}
                        alt={item.product_name}
                        width={40}
                        height={50}
                        className="object-contain max-h-full"
                      />
                    </div>
                    <div>
                      <strong className="text-[#2c1a1a] block truncate max-w-[170px]">
                        {item.product_name}
                      </strong>
                      <span className="text-neutral-500">Qty: {item.quantity}</span>
                    </div>
                  </div>

                  <span className="font-bold text-[#1a6b3c]">
                    E{(item.price * item.quantity).toFixed(2)}
                  </span>
                </div>
              ))}
            </div>

            <div className="space-y-2 text-xs text-neutral-600 border-t border-[#f0ece8] pt-4">
              <div className="flex justify-between">
                <span>Subtotal</span>
                <span className="font-semibold text-[#2c1a1a]">E{subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span>Doorstep Delivery</span>
                <span className="font-semibold text-[#2c1a1a]">
                  {deliveryFee === 0 ? (
                    <span className="text-[#1a6b3c] font-bold">FREE</span>
                  ) : (
                    `E${deliveryFee.toFixed(2)}`
                  )}
                </span>
              </div>
              <div className="flex justify-between text-base font-bold text-[#2c1a1a] pt-3 border-t border-[#f0ece8]">
                <span>Total Due</span>
                <span className="text-2xl text-[#1a6b3c]">E{grandTotal.toFixed(2)}</span>
              </div>
            </div>

            <button
              type="submit"
              disabled={submitting}
              className="btn-wine w-full py-4 text-sm font-bold shadow-2xl flex items-center justify-center gap-2"
            >
              {submitting ? (
                <span>Placing Your Order...</span>
              ) : (
                <>
                  <Lock className="w-4 h-4" />
                  <span>Place Order • E{grandTotal.toFixed(2)}</span>
                </>
              )}
            </button>

            <div className="pt-2 text-[11px] text-neutral-500 space-y-1 text-center">
              <p className="flex items-center justify-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Encrypted Order Processing & Confirmation Email</span>
              </p>
              <p>Adult signature (18+) required upon doorstep delivery.</p>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
}
