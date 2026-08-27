# 🍷 Wine & Co. Eswatini — Next.js 14 E-Commerce Platform

A production-ready e-commerce platform for Wine & Co. Eswatini, built with **Next.js 14 App Router, Tailwind CSS, Supabase (PostgreSQL), and Stripe payments**.

---

## ✨ Features

- 🍇 **Curated Wine Cellar**: Filter by grape variety, origin, vintage, and search.
- 📦 **Monthly Surprise Wine Club**: 4 membership tiers with recurring subscription checkout.
- 🧀 **Epicurean Food Pairings**: Artisan cheeses, chocolates, and biltong platters.
- 🎁 **Corporate Luxury Gifting**: Executive wooden cases and bespoke enquiry quote generator.
- 🧺 **Celebration Gift Hampers**: Full gift basket listings with direct shopping bag integration.
- 📖 **Boutique Magazine**: Interactive embedded online reader + E45 instant PDF download.
- 🛒 **Persistent Shopping Bag**: Slide-out cart drawer + full order review & free delivery calculator.
- 💳 **Flexible Payment Gateway**: Cash on Delivery, Direct Bank Electronic Transfer (EFT), & Stripe Secure SSL Card checkout.
- 📧 **Automated Transactional Emails**: Gmail SMTP order confirmations and welcome notices.
- 🛡️ **Age Gate & Legal Compliance**: 18+ verification modal with persistent local storage.
- 💬 **WhatsApp Sommelier Concierge**: Instant direct chat widget (+268 7838 1971).
- 👔 **Staff & Admin Dashboard**: Real-time sales stats, wine inventory CRUD, orders fulfilment status manager.

---

## 🚀 Quick Start (Local Development)

```bash
# 1. Navigate to the project directory
cd wineco

# 2. Install dependencies
npm install

# 3. Start development server
npm run dev
```

Open [http://localhost:3000](http://localhost:3000) in your browser.

---

## 🗄️ Database Setup (Supabase)

1. Create a project at [supabase.com](https://supabase.com).
2. Open the **SQL Editor** tab in your Supabase Dashboard.
3. Copy and paste the contents of `../supabase_migration.sql` and click **Run**.
4. Copy your **Project URL** and **Anon Key** from `Project Settings > API` into your `.env.local` file:

```env
NEXT_PUBLIC_SUPABASE_URL=https://your-project.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=your-anon-key
```

---

## 💳 Stripe Payment Setup

1. Sign in to your [Stripe Dashboard](https://dashboard.stripe.com).
2. Copy your **Publishable Key** and **Secret Key** from `Developers > API keys`.
3. Add them to `.env.local`:

```env
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
```

---

## 📧 Email Configuration (Gmail SMTP)

The app comes preconfigured with Gmail SMTP. To send emails using your own Gmail address:
1. Enable **2-Step Verification** on your Google account.
2. Generate an **App Password** at `myaccount.google.com/apppasswords`.
3. Add the 16-character code to `.env.local`:

```env
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
ADMIN_EMAIL=siphiwosethuthikazi@gmail.com
```

---

## 🌐 Deploy to Vercel via GitHub

1. Initialize git and push to GitHub:
   ```bash
   git init
   git add .
   git commit -m "Initial commit - Wine & Co. Next.js platform"
   git remote add origin https://github.com/your-username/wine-co.git
   git push -u origin main
   ```
2. Go to [vercel.com](https://vercel.com) and click **Add New > Project**.
3. Import your `wine-co` GitHub repository.
4. Paste the environment variables from `.env.example` into the Vercel project settings.
5. Click **Deploy**! 🚀
