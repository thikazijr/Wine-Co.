'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { X, Plus, Minus, Trash2, ShoppingBag, ArrowRight } from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export function CartDrawer() {
  const {
    cart,
    isDrawerOpen,
    setIsDrawerOpen,
    updateQuantity,
    removeFromCart,
    subtotal,
    deliveryFee,
    grandTotal,
  } = useCart();

  if (!isDrawerOpen) return null;

  return (
    <div className="fixed inset-0 z-50 overflow-hidden">
      {/* Backdrop */}
      <div
        onClick={() => setIsDrawerOpen(false)}
        className="absolute inset-0 bg-black/60 backdrop-blur-xs transition-opacity animate-in fade-in"
      />

      <div className="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div className="w-screen max-w-md bg-white shadow-2xl flex flex-col border-l border-[#c9a03d]/30 animate-in slide-in-from-right duration-300">
          {/* Header */}
          <div className="p-6 bg-[#1a0f0f] text-white flex items-center justify-between border-b border-[#c9a03d]">
            <div className="flex items-center gap-2.5">
              <ShoppingBag className="w-5 h-5 text-[#c9a03d]" />
              <h2 className="text-lg font-serif font-bold text-white tracking-wide">
                Your Shopping Bag
              </h2>
            </div>
            <div className="flex items-center gap-2">
              {cart.length > 0 && (
                <button
                  onClick={clearCart}
                  className="text-xs font-semibold text-red-300 hover:text-white hover:bg-red-900/40 px-2.5 py-1.5 rounded-lg border border-red-500/30 transition-all flex items-center gap-1.5"
                  title="Empty entire shopping bag"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                  <span>Empty Bag</span>
                </button>
              )}
              <button
                onClick={() => setIsDrawerOpen(false)}
                className="p-1.5 rounded-full hover:bg-white/10 text-white/80 hover:text-white transition-colors"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
          </div>

          {/* Items List */}
          <div className="flex-1 overflow-y-auto p-6 space-y-4">
            {cart.length === 0 ? (
              <div className="h-full flex flex-col items-center justify-center text-center p-8 space-y-4">
                <div className="w-20 h-20 rounded-full bg-[#faf6f0] border border-[#e8e0d8] flex items-center justify-center text-[#722f37]">
                  <ShoppingBag className="w-10 h-10 stroke-[1.5]" />
                </div>
                <div>
                  <h3 className="text-lg font-serif font-bold text-[#2c1a1a]">
                    Your bag is empty
                  </h3>
                  <p className="text-sm text-neutral-500 mt-1">
                    Explore our curated wines, gifts, and monthly surprise boxes.
                  </p>
                </div>
                <Link
                  href="/shop"
                  onClick={() => setIsDrawerOpen(false)}
                  className="btn-wine text-sm mt-2"
                >
                  Browse Wines
                </Link>
              </div>
            ) : (
              cart.map((item) => (
                <div
                  key={`${item.product_type}_${item.product_id}`}
                  className="flex gap-4 p-4 rounded-2xl bg-[#faf6f0] border border-[#f0ece8] hover:border-[#c9a03d]/40 transition-colors"
                >
                  <div className="relative w-18 h-20 bg-white rounded-xl overflow-hidden shrink-0 p-1 border border-[#e8e0d8] flex items-center justify-center">
                    <Image
                      src={item.image_url || '/wines/margaux.jpg'}
                      alt={item.product_name}
                      width={64}
                      height={80}
                      className="object-contain max-h-full"
                    />
                  </div>

                  <div className="flex-1 flex flex-col justify-between min-w-0">
                    <div>
                      <div className="flex items-start justify-between gap-2">
                        <h4 className="text-sm font-semibold text-[#2c1a1a] break-words leading-snug">
                          {item.product_name}
                        </h4>
                        <button
                          onClick={() => removeFromCart(item.product_id, item.product_type)}
                          className="text-neutral-400 hover:text-red-600 transition-colors p-1"
                          title="Remove item"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                      <p className="text-xs text-neutral-500 capitalize">
                        {item.product_type}
                      </p>
                    </div>

                    <div className="flex items-center justify-between mt-2 pt-2 border-t border-[#e8e0d8]">
                      <span className="text-sm font-bold text-[#1a6b3c]">
                        E{(item.price * item.quantity).toFixed(2)}
                      </span>

                      {/* Quantity Controls */}
                      <div className="flex items-center border border-[#d8d0c8] rounded-lg bg-white overflow-hidden">
                        <button
                          onClick={() =>
                            updateQuantity(item.product_id, item.product_type, item.quantity - 1)
                          }
                          className="p-1 px-2 hover:bg-neutral-100 text-neutral-600"
                        >
                          <Minus className="w-3 h-3" />
                        </button>
                        <span className="px-2 text-xs font-semibold text-[#2c1a1a]">
                          {item.quantity}
                        </span>
                        <button
                          onClick={() =>
                            updateQuantity(item.product_id, item.product_type, item.quantity + 1)
                          }
                          className="p-1 px-2 hover:bg-neutral-100 text-neutral-600"
                        >
                          <Plus className="w-3 h-3" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>

          {/* Footer Summary */}
          {cart.length > 0 && (
            <div className="p-6 bg-[#faf6f0] border-t border-[#e8e0d8] space-y-4">
              <div className="space-y-2 text-sm text-neutral-600">
                <div className="flex justify-between">
                  <span>Subtotal</span>
                  <span className="font-semibold text-[#2c1a1a]">E{subtotal.toFixed(2)}</span>
                </div>
                <div className="flex justify-between">
                  <span>Delivery</span>
                  <span className="font-semibold text-[#2c1a1a]">
                    {deliveryFee === 0 ? (
                      <span className="text-[#1a6b3c] font-bold">FREE (Over E600)</span>
                    ) : (
                      `E${deliveryFee.toFixed(2)}`
                    )}
                  </span>
                </div>
                <div className="flex justify-between text-base font-bold text-[#2c1a1a] pt-2 border-t border-[#e8e0d8]">
                  <span>Total</span>
                  <span className="text-xl text-[#1a6b3c]">E{grandTotal.toFixed(2)}</span>
                </div>
              </div>

              <div className="space-y-2 pt-2">
                <Link
                  href="/checkout"
                  onClick={() => setIsDrawerOpen(false)}
                  className="btn-wine w-full text-center text-sm py-3.5 flex items-center justify-center gap-2 shadow-lg"
                >
                  <span>Proceed to Checkout</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>

                <Link
                  href="/cart"
                  onClick={() => setIsDrawerOpen(false)}
                  className="block text-center text-xs text-neutral-500 hover:text-[#722f37] font-medium py-1"
                >
                  View Full Cart & Summary
                </Link>

                <button
                  type="button"
                  onClick={clearCart}
                  className="w-full text-center text-xs text-red-600 hover:text-red-800 hover:underline py-1 flex items-center justify-center gap-1.5 transition-colors"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                  <span>Empty Shopping Bag</span>
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
