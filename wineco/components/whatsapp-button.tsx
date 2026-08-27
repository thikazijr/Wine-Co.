'use client';

import React from 'react';
import { MessageCircle } from 'lucide-react';

export function WhatsAppButton() {
  const phoneNumber = '26878381971';
  const defaultMessage = encodeURIComponent(
    'Hello Wine & Co. Eswatini! I would like to enquire about your wine selection and delivery.'
  );

  return (
    <a
      href={`https://wa.me/${phoneNumber}?text=${defaultMessage}`}
      target="_blank"
      rel="noopener noreferrer"
      className="fixed bottom-6 right-6 z-40 bg-[#25D366] hover:bg-[#20ba59] text-white p-3.5 rounded-full shadow-2xl transition-all duration-300 hover:scale-110 flex items-center gap-2 group border-2 border-white/20"
      aria-label="Chat on WhatsApp"
    >
      <MessageCircle className="w-6 h-6 fill-current" />
      <span className="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-300 ease-in-out text-sm font-semibold pr-1">
        Chat with Sommelier
      </span>
    </a>
  );
}
