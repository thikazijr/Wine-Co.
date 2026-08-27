'use client';

import React, { useState } from 'react';
import { Mail, Phone, MapPin, Send, CheckCircle2, Clock } from 'lucide-react';
import { useCart } from '@/lib/cart-context';

export default function ContactPage() {
  const { showToast } = useCart();
  const [submitted, setSubmitted] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: 'Wine Selection Enquiry',
    message: '',
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
    showToast('Your message has been sent to our sommelier team!');
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Header Banner */}
      <div className="bg-gradient-to-br from-[#1a0f0f] via-[#33181c] to-[#12080a] rounded-3xl p-8 md:p-12 text-white border-2 border-[#c9a03d]/40 shadow-xl">
        <div className="max-w-2xl space-y-3">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
            Customer Concierge
          </span>
          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Get in Touch with Wine & Co.
          </h1>
          <p className="text-sm md:text-base text-white/80">
            Have a question about a vintage, corporate gifting, or wine club membership?
            Our certified sommeliers are here to assist you.
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {/* Contact Info Cards */}
        <div className="lg:col-span-5 space-y-6">
          <div className="bg-white rounded-3xl p-6 border border-[#f0ece8] shadow-sm space-y-4">
            <h3 className="text-lg font-serif font-bold text-[#722f37]">Direct Contacts</h3>

            <div className="space-y-4 text-xs">
              <div className="flex items-start gap-3">
                <div className="p-2.5 rounded-xl bg-[#faf6f0] text-[#722f37] shrink-0 border border-[#e8e0d8]">
                  <Phone className="w-4 h-4" />
                </div>
                <div>
                  <strong className="block text-neutral-800 text-sm">Telephone / WhatsApp</strong>
                  <a href="tel:+26878381971" className="text-neutral-600 hover:text-[#722f37]">
                    +268 7838 1971 / +268 7686 9104
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="p-2.5 rounded-xl bg-[#faf6f0] text-[#722f37] shrink-0 border border-[#e8e0d8]">
                  <Mail className="w-4 h-4" />
                </div>
                <div>
                  <strong className="block text-neutral-800 text-sm">Email Address</strong>
                  <a
                    href="mailto:siphiwosethuthikazi@gmail.com"
                    className="text-neutral-600 hover:text-[#722f37]"
                  >
                    siphiwosethuthikazi@gmail.com
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="p-2.5 rounded-xl bg-[#faf6f0] text-[#722f37] shrink-0 border border-[#e8e0d8]">
                  <MapPin className="w-4 h-4" />
                </div>
                <div>
                  <strong className="block text-neutral-800 text-sm">Cellar Delivery Hub</strong>
                  <p className="text-neutral-600">Mbabane • Manzini • Ezulwini, Eswatini</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="p-2.5 rounded-xl bg-[#faf6f0] text-[#722f37] shrink-0 border border-[#e8e0d8]">
                  <Clock className="w-4 h-4" />
                </div>
                <div>
                  <strong className="block text-neutral-800 text-sm">Operating Hours</strong>
                  <p className="text-neutral-600">Monday - Saturday: 08:30 - 18:00</p>
                  <p className="text-neutral-600">Sunday Delivery: 10:00 - 15:00</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Contact Form */}
        <div className="lg:col-span-7 bg-white rounded-3xl p-8 border border-[#f0ece8] shadow-lg space-y-6">
          <h2 className="text-2xl font-serif font-bold text-[#2c1a1a]">Send Us a Message</h2>

          {submitted ? (
            <div className="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center space-y-3">
              <CheckCircle2 className="w-12 h-12 text-emerald-600 mx-auto" />
              <h3 className="text-lg font-bold text-emerald-900">Message Delivered</h3>
              <p className="text-xs text-emerald-700">
                Thank you for contacting Wine & Co. A sommelier will respond to your email shortly.
              </p>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4 text-xs">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Your Name</label>
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="Siphiwo Thikazi"
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Email Address</label>
                  <input
                    type="email"
                    required
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="siphiwo@example.com"
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Phone Number</label>
                  <input
                    type="tel"
                    required
                    value={formData.phone}
                    onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="+268 7838 1971"
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                  />
                </div>

                <div>
                  <label className="block font-semibold text-[#2c1a1a] mb-1">Subject</label>
                  <select
                    value={formData.subject}
                    onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                    className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl font-medium focus:outline-none focus:border-[#722f37]"
                  >
                    <option value="Wine Selection Enquiry">Wine Selection Enquiry</option>
                    <option value="Wine Club / Subscription">Wine Club / Subscription</option>
                    <option value="Corporate Gifting Enquiry">Corporate Gifting Enquiry</option>
                    <option value="Order Tracking & Delivery">Order Tracking & Delivery</option>
                    <option value="General Feedback">General Feedback</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block font-semibold text-[#2c1a1a] mb-1">Message</label>
                <textarea
                  required
                  rows={4}
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  placeholder="How can we assist your wine journey today?..."
                  className="w-full p-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl focus:outline-none focus:border-[#722f37]"
                />
              </div>

              <button
                type="submit"
                className="btn-wine w-full py-3.5 text-sm font-bold shadow-lg flex items-center justify-center gap-2"
              >
                <Send className="w-4 h-4" />
                <span>Send Message</span>
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}
