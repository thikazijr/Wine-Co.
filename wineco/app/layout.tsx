import type { Metadata } from 'next';
import './globals.css';
import { CartProvider } from '@/lib/cart-context';
import { Navbar } from '@/components/navbar';
import { Footer } from '@/components/footer';
import { CartDrawer } from '@/components/cart-drawer';
import { AgeGate } from '@/components/age-gate';
import { WhatsAppButton } from '@/components/whatsapp-button';
import { ToastContainer } from '@/components/toast-container';

export const metadata: Metadata = {
  title: 'Wine & Co. Eswatini | Premium Wine Club & Curated Cellar',
  description:
    'Discover curated fine wines, monthly surprise wine boxes, gourmet food pairings, and corporate luxury gifting delivered across Eswatini.',
  keywords:
    'wine club eswatini, wine delivery mbabane, fine wines manzini, south african wines eswatini, wine gift baskets',
  icons: {
    icon: [
      { url: '/favicon.ico' },
      { url: '/favicon.png', type: 'image/png' },
    ],
    apple: '/favicon.png',
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="scroll-smooth">
      <body className="min-h-screen flex flex-col bg-[#faf6f0] text-[#2c1a1a] antialiased">
        <CartProvider>
          <AgeGate />
          <Navbar />
          <CartDrawer />
          <main className="flex-1">{children}</main>
          <Footer />
          <WhatsAppButton />
          <ToastContainer />
        </CartProvider>
      </body>
    </html>
  );
}
