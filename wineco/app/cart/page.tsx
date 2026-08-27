'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ShoppingBag, ArrowRight, Trash2, Plus, Minus, ArrowLeft, ShieldCheck } from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export default function CartPage() {
  const { cart, updateQuantity, removeFromCart, clearCart, subtotal, deliveryFee, grandTotal } =
    useCart();

  if (cart.length === 0) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center space-y-6">
        <div className="w-24 h-24 rounded-full bg-white border-2 border-[#c9a03d] mx-auto flex items-center justify-center text-[#722f37] shadow-lg">
          <ShoppingBag className="w-12 h-12 stroke-[1.5]" />
        </div>

        <div className="space-y-2">
          <h1 className="text-3xl font-serif font-bold text-[#2c1a1a]">Your Shopping Bag is Empty</h1>
          <p className="text-sm text-neutral-500 max-w-md mx-auto">
            Discover our sommelier-curated collection of fine wines, artisan pairings, and gift hampers.
          </p>
        </div>

        <div>
          <Link href="/shop" className="btn-wine text-sm py-3 px-8 shadow-md">
            <span>Explore Wine Cellar</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <div className="flex items-center justify-between border-b border-[#f0ece8] pb-4">
        <div>
          <h1 className="text-3xl font-serif font-bold text-[#2c1a1a]">Shopping Bag</h1>
          <p className="text-xs text-neutral-500 mt-0.5">Review items before proceeding to checkout</p>
        </div>

        <button
          onClick={clearCart}
          className="text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1"
        >
          <Trash2 className="w-3.5 h-3.5" />
          <span>Clear Bag</span>
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {/* Cart Items Table */}
        <div className="lg:col-span-8 bg-white rounded-3xl p-6 border border-[#f0ece8] shadow-sm space-y-4">
          <div className="divide-y divide-[#f0ece8]">
            {cart.map((item) => (
              <div
                key={`${item.product_type}_${item.product_id}`}
                className="py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 first:pt-0 last:pb-0"
              >
                <div className="flex items-center gap-4">
                  <div className="relative w-20 h-24 bg-[#faf6f0] rounded-xl overflow-hidden shrink-0 p-1 border border-[#f0ece8] flex items-center justify-center">
                    <Image
                      src={item.image_url || '/wines/margaux.jpg'}
                      alt={item.product_name}
                      width={70}
                      height={90}
                      className="object-contain max-h-full"
                    />
                  </div>

                  <div>
                    <span className="text-[10px] font-bold uppercase tracking-wider text-[#c9a03d]">
                      {item.product_type}
                    </span>
                    <h3 className="text-base font-serif font-bold text-[#2c1a1a]">
                      {item.product_name}
                    </h3>
                    <p className="text-xs text-neutral-500">
                      Unit: E{item.price.toFixed(2)}
                    </p>
                  </div>
                </div>

                <div className="flex items-center justify-between w-full sm:w-auto gap-6">
                  {/* Quantity */}
                  <div className="flex items-center border border-[#d8d0c8] rounded-xl bg-[#faf6f0] overflow-hidden">
                    <button
                      onClick={() =>
                        updateQuantity(item.product_id, item.product_type, item.quantity - 1)
                      }
                      className="p-2 hover:bg-neutral-200 text-neutral-700"
                    >
                      <Minus className="w-3.5 h-3.5" />
                    </button>
                    <span className="px-3 text-xs font-bold text-[#2c1a1a]">
                      {item.quantity}
                    </span>
                    <button
                      onClick={() =>
                        updateQuantity(item.product_id, item.product_type, item.quantity + 1)
                      }
                      className="p-2 hover:bg-neutral-200 text-neutral-700"
                    >
                      <Plus className="w-3.5 h-3.5" />
                    </button>
                  </div>

                  {/* Line Total */}
                  <span className="text-base font-bold text-[#1a6b3c] min-w-20 text-right">
                    E{(item.price * item.quantity).toFixed(2)}
                  </span>

                  {/* Remove Button */}
                  <button
                    onClick={() => removeFromCart(item.product_id, item.product_type)}
                    className="p-2 text-neutral-400 hover:text-red-600 rounded-lg transition-colors"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Order Summary Card */}
        <div className="lg:col-span-4 bg-white rounded-3xl p-6 border border-[#f0ece8] shadow-lg space-y-6 sticky top-24">
          <h2 className="text-lg font-serif font-bold text-[#2c1a1a] border-b border-[#f0ece8] pb-3">
            Order Summary
          </h2>

          <div className="space-y-3 text-xs text-neutral-600">
            <div className="flex justify-between">
              <span>Items Subtotal</span>
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
            {deliveryFee > 0 && (
              <p className="text-[11px] text-[#c9a03d] italic bg-[#faf6f0] p-2 rounded-lg border border-[#f0ece8]">
                Add E{(600 - subtotal).toFixed(2)} more to qualify for Free Delivery!
              </p>
            )}
            <div className="flex justify-between text-base font-bold text-[#2c1a1a] pt-3 border-t border-[#f0ece8]">
              <span>Grand Total</span>
              <span className="text-2xl text-[#1a6b3c]">E{grandTotal.toFixed(2)}</span>
            </div>
          </div>

          <div className="space-y-3 pt-2">
            <Link
              href="/checkout"
              className="btn-wine w-full py-4 text-sm font-bold shadow-xl flex items-center justify-center gap-2"
            >
              <span>Proceed to Checkout</span>
              <ArrowRight className="w-4 h-4" />
            </Link>

            <Link
              href="/shop"
              className="btn-outline-wine w-full text-xs py-2.5 flex items-center justify-center gap-2"
            >
              <ArrowLeft className="w-3.5 h-3.5" />
              <span>Continue Shopping</span>
            </Link>
          </div>

          <div className="text-[11px] text-neutral-500 pt-2 flex items-center gap-2">
            <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
            <span>Secure checkout with Stripe SSL or Cash on Delivery</span>
          </div>
        </div>
      </div>
    </div>
  );
}
