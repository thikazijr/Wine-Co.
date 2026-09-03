'use client';

import React, { Suspense, useEffect, useState, useRef } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { CheckCircle2, ArrowRight, QrCode, Smartphone } from 'lucide-react';

// Generate an 8-char uppercase token from the order number
function generateOrderToken(orderNumber: string): string {
  let hash = 0;
  for (let i = 0; i < orderNumber.length; i++) {
    const char = orderNumber.charCodeAt(i);
    hash = (hash << 5) - hash + char;
    hash |= 0;
  }
  const base = Math.abs(hash).toString(36).toUpperCase().padStart(8, '0');
  return base.slice(0, 8);
}

function QRCodeCanvas({ value }: { value: string }) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (typeof window === 'undefined') return;
    import('qrcode').then((QRCode) => {
      if (canvasRef.current) {
        QRCode.toCanvas(canvasRef.current, value, {
          width: 180,
          margin: 2,
          color: { dark: '#2c1a1a', light: '#faf6f0' },
        });
      }
    });
  }, [value]);

  return (
    <canvas
      ref={canvasRef}
      className="rounded-xl border-2 border-[#c9a03d]/40"
      style={{ maxWidth: '180px', maxHeight: '180px' }}
    />
  );
}

function OrderConfirmationContent() {
  const searchParams = useSearchParams();
  const orderNumber = searchParams.get('order') || 'ORD-ESW-10492';
  const [orderDetails, setOrderDetails] = useState<any>(null);

  useEffect(() => {
    try {
      const stored = localStorage.getItem('wineco_last_order');
      if (stored) {
        setOrderDetails(JSON.parse(stored));
      }
    } catch (e) {
      console.error(e);
    }
  }, []);

  const orderToken = generateOrderToken(orderNumber);
  const qrValue = `WINECO|${orderNumber}|${orderToken}|${orderDetails?.customerName || ''}|E${orderDetails?.grandTotal?.toFixed(2) || '0.00'}`;

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8">
      {/* Success Badge Banner */}
      <div className="bg-white rounded-3xl p-8 md:p-12 border-2 border-emerald-500 shadow-xl text-center space-y-4 relative overflow-hidden">
        <div className="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full mx-auto flex items-center justify-center shadow-inner">
          <CheckCircle2 className="w-12 h-12 stroke-[2.5]" />
        </div>

        <div className="space-y-2">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
            Order Successfully Placed
          </span>
          <h1 className="text-3xl md:text-4xl font-serif font-bold text-[#2c1a1a]">
            Thank You for Your Order!
          </h1>
          <p className="text-sm text-neutral-600 max-w-lg mx-auto">
            Your wine order has been received and is being carefully packaged in our cellar.
            A WhatsApp confirmation has been sent to{' '}
            <strong className="text-[#722f37]">
              {orderDetails?.customerPhone || 'your phone number'}
            </strong>
            .
          </p>
        </div>

        <div className="inline-block bg-[#faf6f0] border border-[#e8e0d8] rounded-2xl p-4 text-xs space-y-1">
          <span className="text-neutral-500 block">Your Order Reference Number:</span>
          <span className="text-xl font-bold font-mono text-[#722f37]">{orderNumber}</span>
        </div>
      </div>

      {/* 🎯 Delivery Verification QR Code Card */}
      <div className="bg-white rounded-3xl p-6 md:p-8 border-2 border-[#c9a03d] shadow-lg space-y-6">
        <div className="flex items-center gap-3 border-b border-[#e8e0d8] pb-4">
          <div className="w-10 h-10 bg-[#c9a03d]/15 rounded-xl flex items-center justify-center">
            <QrCode className="w-5 h-5 text-[#c9a03d]" />
          </div>
          <div>
            <h2 className="text-lg font-serif font-bold text-[#2c1a1a]">
              Delivery Verification Pass
            </h2>
            <p className="text-xs text-neutral-500">
              Show this to the Wine & Co delivery driver to receive your order
            </p>
          </div>
        </div>

        <div className="flex flex-col md:flex-row items-center gap-8">
          {/* QR Code */}
          <div className="flex flex-col items-center gap-3 shrink-0">
            <QRCodeCanvas value={qrValue} />
            <span className="text-[10px] text-neutral-400 font-mono">Scan to verify order</span>
          </div>

          {/* Order Token & Info */}
          <div className="flex-1 space-y-4">
            <div className="bg-[#faf6f0] rounded-2xl p-5 border border-[#e8e0d8] space-y-3">
              <div>
                <span className="text-[10px] font-bold text-neutral-500 uppercase tracking-wider block mb-1">
                  Delivery Token
                </span>
                <span className="text-4xl font-extrabold font-mono text-[#722f37] tracking-widest block">
                  {orderToken}
                </span>
              </div>

              <div className="grid grid-cols-2 gap-3 text-xs text-neutral-600 border-t border-[#e8e0d8] pt-3">
                <div>
                  <span className="font-bold text-[#2c1a1a] block">Order Ref</span>
                  <span className="font-mono text-[11px]">{orderNumber}</span>
                </div>
                <div>
                  <span className="font-bold text-[#2c1a1a] block">Recipient</span>
                  <span>{orderDetails?.customerName || '—'}</span>
                </div>
                <div>
                  <span className="font-bold text-[#2c1a1a] block">Total Amount</span>
                  <span className="font-bold text-[#1a6b3c]">
                    E{orderDetails?.grandTotal?.toFixed(2) || '—'}
                  </span>
                </div>
                <div>
                  <span className="font-bold text-[#2c1a1a] block">Payment</span>
                  <span className="capitalize">
                    {orderDetails?.paymentMethod?.replace(/_/g, ' ') || '—'}
                  </span>
                </div>
              </div>
            </div>

            <div className="flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs">
              <Smartphone className="w-4 h-4 text-amber-700 shrink-0 mt-0.5" />
              <div>
                <p className="font-bold text-amber-900">How to use your Delivery Pass:</p>
                <ol className="text-amber-800 mt-1 space-y-0.5 list-decimal list-inside">
                  <li>Keep this page open on your phone when the driver arrives.</li>
                  <li>Show the QR code or quote your <strong>Token: {orderToken}</strong></li>
                  <li>The driver will scan/verify before handing over your wine.</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Order Details & Summary Card */}
      {orderDetails && (
        <div className="bg-white rounded-3xl p-6 md:p-8 border border-[#f0ece8] shadow-sm space-y-6">
          <h2 className="text-lg font-serif font-bold text-[#2c1a1a] border-b border-[#f0ece8] pb-3">
            Order Invoice Summary
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-neutral-600">
            <div>
              <strong className="text-[#2c1a1a] block mb-1">Customer & Delivery:</strong>
              <p className="font-semibold text-[#722f37]">{orderDetails.customerName}</p>
              <p>{orderDetails.customerAddress}</p>
              <p>{orderDetails.city}, Eswatini</p>
              <p className="mt-1">📞 {orderDetails.customerPhone}</p>
            </div>

            <div>
              <strong className="text-[#2c1a1a] block mb-1">Payment Method:</strong>
              <p className="font-semibold text-[#1a6b3c] capitalize">
                {orderDetails.paymentMethod.replace(/_/g, ' ')}
              </p>
              <p className="text-neutral-500 text-[11px] mt-1">
                Status: Pending verification upon delivery or EFT clearance
              </p>
            </div>
          </div>

          {/* Ordered Items Table */}
          <div className="border-t border-[#f0ece8] pt-4 space-y-3">
            <h3 className="text-xs font-bold text-[#2c1a1a] uppercase tracking-wider">
              Items ({orderDetails.items?.length})
            </h3>
            <div className="divide-y divide-[#f0ece8] text-xs">
              {orderDetails.items?.map((item: any, idx: number) => (
                <div key={idx} className="py-2.5 flex items-center justify-between">
                  <div>
                    <span className="font-semibold text-[#2c1a1a]">{item.product_name}</span>
                    <span className="text-neutral-500 ml-2">× {item.quantity}</span>
                  </div>
                  <span className="font-bold text-[#1a6b3c]">
                    E{(item.price * item.quantity).toFixed(2)}
                  </span>
                </div>
              ))}
            </div>

            <div className="border-t border-[#f0ece8] pt-3 text-xs space-y-1.5 text-neutral-600">
              <div className="flex justify-between">
                <span>Subtotal</span>
                <span>E{orderDetails.subtotal?.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span>Delivery</span>
                <span>{orderDetails.deliveryFee === 0 ? 'FREE' : `E${orderDetails.deliveryFee?.toFixed(2)}`}</span>
              </div>
              <div className="flex justify-between font-bold text-sm text-[#2c1a1a] pt-2 border-t border-[#f0ece8]">
                <span>Total Amount</span>
                <span className="text-[#1a6b3c] text-lg">E{orderDetails.grandTotal?.toFixed(2)}</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Next Steps & Support Contact */}
      <div className="bg-[#faf6f0] rounded-3xl p-6 border border-[#e8e0d8] flex flex-col md:flex-row items-center justify-between gap-6">
        <div className="space-y-1 text-xs text-neutral-600 text-center md:text-left">
          <h4 className="font-bold text-[#722f37] text-sm">Need immediate assistance with this order?</h4>
          <p>Contact our concierge sommelier directly via WhatsApp or phone.</p>
        </div>

        <div className="flex flex-wrap gap-3">
          <a
            href={`https://wa.me/26878381971?text=Hello%20Wine%20%26%20Co!%20Enquiry%20regarding%20my%20order%20${orderNumber}`}
            target="_blank"
            rel="noopener noreferrer"
            className="btn-gold text-xs px-5 py-2.5 shadow-md"
          >
            WhatsApp Support
          </a>

          <Link href="/shop" className="btn-wine text-xs px-5 py-2.5 shadow-md">
            <span>Continue Shopping</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </div>
  );
}

export default function OrderConfirmationPage() {
  return (
    <Suspense fallback={
      <div className="max-w-4xl mx-auto px-4 py-16 text-center">
        <div className="w-16 h-16 bg-emerald-100 rounded-full mx-auto flex items-center justify-center mb-4 animate-pulse">
          <CheckCircle2 className="w-8 h-8 text-emerald-400" />
        </div>
        <p className="text-neutral-500 text-sm">Loading your order details…</p>
      </div>
    }>
      <OrderConfirmationContent />
    </Suspense>
  );
}
