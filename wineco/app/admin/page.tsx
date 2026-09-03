'use client';

import React, { useState, useEffect } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import {
  LayoutDashboard,
  Wine as WineIcon,
  ShoppingBag,
  Users,
  Crown,
  Plus,
  Trash2,
  Edit2,
  CheckCircle2,
  XCircle,
  ArrowLeft,
  DollarSign,
  TrendingUp,
  Package,
  AlertTriangle,
  Mail,
  Clock,
  Search,
  RefreshCw,
  Phone,
  Hourglass,
  CheckCheck,
  QrCode,
  MapPin,
  Truck,
  SlidersHorizontal,
  X,
  ShieldCheck,
  ChevronRight,
  ExternalLink,
} from 'lucide-react';
import { INITIAL_WINES } from '@/lib/mock-data';
import { Wine, Subscriber, PortalLoginLog } from '@/lib/types';
import { AdminOrder } from '@/app/api/orders/route';

// Derive an 8-char verification token from order number
function generateOrderToken(orderNumber: string): string {
  let hash = 0;
  for (let i = 0; i < orderNumber.length; i++) {
    const char = orderNumber.charCodeAt(i);
    hash = (hash << 5) - hash + char;
    hash |= 0;
  }
  return Math.abs(hash).toString(36).toUpperCase().padStart(8, '0').slice(0, 8);
}

export default function AdminPage() {
  // Modal states for clean, non-scrolling UI
  const [isInventoryModalOpen, setIsInventoryModalOpen] = useState(false);
  const [isOrdersModalOpen, setIsOrdersModalOpen] = useState(false);
  const [isVipModalOpen, setIsVipModalOpen] = useState(false);
  const [isLogsModalOpen, setIsLogsModalOpen] = useState(false);
  const [isAddWineOpen, setIsAddWineOpen] = useState(false);
  const [editingWine, setEditingWine] = useState<Wine | null>(null);

  // Filter states
  const [wineSearch, setWineSearch] = useState('');
  const [showLowStockOnly, setShowLowStockOnly] = useState(false);
  const [orderFilter, setOrderFilter] = useState<'pending' | 'completed'>('pending');

  // Inventory state
  const [winesList, setWinesList] = useState<Wine[]>(INITIAL_WINES);
  const [newWine, setNewWine] = useState<Partial<Wine>>({
    name: '',
    variety: 'Cabernet Sauvignon',
    origin: 'South Africa',
    price: 350,
    stock_quantity: 12,
    vintage: 2021,
    description: '',
    image_url: '/wines/kanonkop.png',
  });

  // Orders state (synced with /api/orders)
  const [ordersList, setOrdersList] = useState<AdminOrder[]>([
    {
      id: 'ord_vip_1',
      orderNumber: 'ORD-VIP-918273',
      customerName: 'Siphiwo Sethu Thikazi',
      email: 'siphiwosethuthikazi@gmail.com',
      phone: '+268 7838 1971',
      address: 'Plot 412, Thembelihle',
      city: 'Mbabane',
      total: 1999.0,
      itemsCount: 6,
      itemsDescription: 'Luxury Reserve Box (6 bottles) - Monthly VIP Delivery',
      orderType: 'vip_box',
      status: 'pending',
      paymentMethod: 'Bank EFT',
      date: '2026-09-03',
      notes: 'VIP Club Member Box - First unboxing delivery with sommelier notes',
    },
    {
      id: 17,
      orderNumber: 'ORD-20260821-5155',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      phone: '+268 7686 9104',
      address: 'Gables Complex, Suite 12',
      city: 'Ezulwini',
      total: 1145.4,
      itemsCount: 3,
      itemsDescription: '2x Kanonkop Pinotage, 1x Cloudy Bay Sauvignon',
      orderType: 'standard',
      status: 'completed',
      paymentMethod: 'cash',
      date: '2026-08-21',
    },
    {
      id: 13,
      orderNumber: 'ORD-20260722-3587',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      phone: '+268 7686 9104',
      address: 'Ezulwini Valley Road',
      city: 'Ezulwini',
      total: 700.0,
      itemsCount: 2,
      itemsDescription: '2x Meerlust Rubicon',
      orderType: 'standard',
      status: 'pending',
      paymentMethod: 'cash',
      date: '2026-07-22',
    },
  ]);

  // Subscriptions state
  const [subscribersList, setSubscribersList] = useState<Subscriber[]>([
    {
      id: 'sub_1',
      fullName: 'Siphiwo Sethu Thikazi',
      email: 'siphiwosethuthikazi@gmail.com',
      phone: '+268 7838 1971',
      planName: 'Luxury Reserve Box',
      price: 1999,
      status: 'active',
      paymentMethod: 'Bank EFT',
      startDate: '2026-07-01',
      city: 'Mbabane',
      address: 'Plot 412, Thembelihle',
    },
    {
      id: 'sub_2',
      fullName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      phone: '+268 7686 9104',
      planName: 'Vineyard Voyager Box',
      price: 999,
      status: 'active',
      paymentMethod: 'MTN MoMo',
      startDate: '2026-07-15',
      city: 'Ezulwini',
      address: 'Gables Complex, Suite 12',
    },
    {
      id: 'sub_3',
      fullName: 'Lihle Mfundo Mbhamali',
      email: 'mfundombhamaly@gmail.com',
      phone: '+268 7612 3456',
      planName: 'Essential Elegance Box',
      price: 499,
      status: 'active',
      paymentMethod: 'Card',
      startDate: '2026-08-01',
      city: 'Manzini',
      address: 'Tubungu Estate, House 8',
    },
    {
      id: 'sub_4',
      fullName: 'Nokwanda Simelane',
      email: 'nokwanda.s@outlook.com',
      phone: '+268 7945 8821',
      planName: 'Vineyard Voyager Box',
      price: 999,
      status: 'cancelled',
      paymentMethod: 'Bank EFT',
      startDate: '2026-06-10',
      city: 'Matsapha',
    },
  ]);

  // Portal login logs
  const [loginLogs, setLoginLogs] = useState<PortalLoginLog[]>([
    {
      id: 'log_1',
      email: 'admin@wineco.sz',
      role: 'admin',
      timestamp: '2026-09-03 08:45:12',
      status: 'success',
      ip: '197.234.221.14 (Mbabane)',
    },
    {
      id: 'log_2',
      email: 'winecoeswatini@yahoo.com',
      role: 'admin',
      timestamp: '2026-09-03 08:30:20',
      status: 'success',
      ip: '197.234.221.14 (Mbabane)',
    },
    {
      id: 'log_3',
      email: 'phumza19952010@gmail.com',
      role: 'member',
      timestamp: '2026-09-02 19:15:40',
      status: 'success',
      ip: '102.134.88.92 (Ezulwini)',
    },
  ]);

  const [staffList] = useState([
    { id: 1, name: 'Siphiwo Sethu Thikazi', email: 'siphiwosethuthikazi@gmail.com', role: 'Super Admin', status: 'Active' },
    { id: 2, name: 'Administrator', email: 'admin@wineco.sz', role: 'Admin', status: 'Active' },
    { id: 3, name: 'Phumelele Dlamini', email: 'phumelele@wineco.sz', role: 'Manager', status: 'Active' },
    { id: 4, name: 'Lihle Mbhamali', email: 'lihle@wineco.sz', role: 'Sommelier', status: 'Active' },
  ]);

  // Fetch real-time data from APIs
  const fetchRealtimeData = async () => {
    try {
      // 1. Fetch live orders (includes VIP box deliveries)
      const ordRes = await fetch('/api/orders');
      const ordData = await ordRes.json();
      if (ordData.success && ordData.orders) {
        setOrdersList(ordData.orders);
      }

      // 2. Fetch subscribers
      const subRes = await fetch('/api/subscriptions');
      const subData = await subRes.json();
      if (subData.success && subData.subscribers) {
        setSubscribersList(subData.subscribers);
      }

      // 3. Fetch portal logins
      const logRes = await fetch('/api/portal-login');
      const logData = await logRes.json();
      if (logData.success && logData.logs) {
        setLoginLogs(logData.logs);
      }
    } catch (e) {
      console.warn('Real-time sync error (memory fallback active):', e);
    }
  };

  useEffect(() => {
    fetchRealtimeData();
  }, []);

  // Stock management helpers
  const adjustStock = (wineId: number, delta: number) => {
    setWinesList((prev) =>
      prev.map((w) => {
        if (w.id === wineId) {
          const newQty = Math.max(0, (w.stock_quantity || 0) + delta);
          return { ...w, stock_quantity: newQty, in_stock: newQty > 0 };
        }
        return w;
      })
    );
  };

  const handleAddWine = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newWine.name) return;

    const added: Wine = {
      id: Date.now(),
      name: newWine.name || '',
      variety: newWine.variety || 'Red Blend',
      origin: newWine.origin || 'South Africa',
      price: Number(newWine.price) || 0,
      stock_quantity: Number(newWine.stock_quantity) || 0,
      vintage: Number(newWine.vintage) || 2021,
      description: newWine.description || 'Exclusive cellar selection.',
      featured: false,
      in_stock: Number(newWine.stock_quantity) > 0,
      image_url: newWine.image_url || '/wines/kanonkop.png',
    };

    setWinesList([added, ...winesList]);
    setIsAddWineOpen(false);
    setNewWine({
      name: '',
      variety: 'Cabernet Sauvignon',
      origin: 'South Africa',
      price: 350,
      stock_quantity: 12,
      vintage: 2021,
      description: '',
      image_url: '/wines/kanonkop.png',
    });
  };

  const handleSaveWineEdit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingWine) return;

    setWinesList((prev) =>
      prev.map((w) => (w.id === editingWine.id ? { ...editingWine, in_stock: editingWine.stock_quantity > 0 } : w))
    );
    setEditingWine(null);
  };

  const handleDeleteWine = (id: number) => {
    if (confirm('Delete this wine from inventory?')) {
      setWinesList((prev) => prev.filter((w) => w.id !== id));
    }
  };

  // Toggle Subscription Status
  const toggleSubscriptionStatus = async (subId: string | number, currentStatus: string) => {
    const newStatus = currentStatus === 'active' ? 'cancelled' : 'active';
    setSubscribersList((prev) =>
      prev.map((s) => (s.id === subId ? { ...s, status: newStatus } : s))
    );

    try {
      await fetch('/api/subscriptions', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: subId, status: newStatus }),
      });
    } catch (e) {
      console.error('Failed to update subscription status:', e);
    }
  };

  // Mark order as Delivered / Done
  const markOrderDone = async (orderId: string | number, orderNumber: string) => {
    setOrdersList((prev) =>
      prev.map((o) => (o.id === orderId || o.orderNumber === orderNumber ? { ...o, status: 'completed' } : o))
    );

    try {
      await fetch('/api/orders', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, orderNumber, status: 'completed' }),
      });
    } catch (e) {
      console.error('Failed to mark order completed on server:', e);
    }
  };

  // Calculations
  const activeSubs = subscribersList.filter((s) => s.status === 'active');
  const monthlyRecurringRevenue = activeSubs.reduce((sum, s) => sum + s.price, 0);

  const totalStockBottles = winesList.reduce((sum, w) => sum + (Number(w.stock_quantity) || 0), 0);
  const totalInventoryRetailValue = winesList.reduce((sum, w) => sum + (w.price * (w.stock_quantity || 0)), 0);
  const lowStockWines = winesList.filter((w) => w.stock_quantity <= 10);

  const pendingOrders = ordersList.filter((o) => o.status === 'pending' || o.status === 'processing');
  const completedOrders = ordersList.filter((o) => o.status === 'completed');
  const vipPendingOrders = pendingOrders.filter((o) => o.orderType === 'vip_box');

  const grossSalesRevenue = ordersList
    .filter((o) => o.status !== 'cancelled')
    .reduce((sum, o) => sum + o.total, 0);
  const totalGrossCombinedRevenue = grossSalesRevenue + monthlyRecurringRevenue;

  const filteredWines = winesList.filter((w) => {
    const matchesSearch =
      w.name.toLowerCase().includes(wineSearch.toLowerCase()) ||
      w.variety.toLowerCase().includes(wineSearch.toLowerCase()) ||
      w.origin.toLowerCase().includes(wineSearch.toLowerCase());
    const matchesLowStock = showLowStockOnly ? w.stock_quantity <= 10 : true;
    return matchesSearch && matchesLowStock;
  });

  return (
    <div className="min-h-screen bg-[#f7f3ee] text-neutral-800 flex flex-col font-sans text-xs">
      {/* ==================== 1. TOP EXECUTIVE HEADER ==================== */}
      <header className="bg-[#150d0e] text-white px-5 sm:px-8 py-3.5 border-b-2 border-[#c9a03d] shadow-lg sticky top-0 z-30">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-3.5">
            <Link href="/" className="flex items-center gap-2.5 group">
              <div className="w-9 h-9 bg-[#1a0f0f] rounded-xl p-0.5 border-2 border-[#c9a03d] flex items-center justify-center shadow-md">
                <Image src="/wines/logo1.jpg" alt="Logo" width={32} height={32} className="rounded-lg object-cover" />
              </div>
              <div>
                <span className="font-serif font-bold text-base text-white block leading-tight">Wine & Co.</span>
                <span className="text-[9px] uppercase font-bold text-[#c9a03d] tracking-widest block">
                  Executive Command Portal
                </span>
              </div>
            </Link>
            <span className="hidden md:inline-flex items-center gap-1 text-[10px] bg-emerald-950/60 text-emerald-300 border border-emerald-500/40 px-2.5 py-0.5 rounded-full font-bold">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              Live Sync Active
            </span>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={fetchRealtimeData}
              className="flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-xl transition-all text-xs font-semibold"
              title="Refresh all real-time data"
            >
              <RefreshCw className="w-3.5 h-3.5 text-[#c9a03d]" />
              <span className="hidden sm:inline">Sync Live</span>
            </button>
            <div className="text-right hidden sm:block">
              <span className="text-white/90 block font-bold text-xs">Siphiwo Sethu Thikazi</span>
              <span className="text-[#c9a03d] text-[10px] font-semibold">Super Admin • Eswatini</span>
            </div>
            <Link
              href="/"
              className="flex items-center gap-1 bg-white/10 hover:bg-[#722f37] text-white px-3 py-1.5 rounded-xl transition-colors font-medium border border-white/10 text-xs"
            >
              <ArrowLeft className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">Storefront</span>
            </Link>
          </div>
        </div>
      </header>

      {/* ==================== 2. QUICK ACTION COMMAND BAR ==================== */}
      <div className="bg-white border-b border-[#e8e0d8] shadow-xs px-5 sm:px-8 py-3">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
          <div className="flex items-center gap-2">
            <span className="text-[11px] font-bold text-neutral-400 uppercase tracking-wider hidden md:inline-block mr-1">
              Quick Actions:
            </span>

            {/* 1. INVENTORY CONTROL BUTTON (Opens Pop-up Modal) */}
            <button
              onClick={() => setIsInventoryModalOpen(true)}
              className="bg-[#722f37] hover:bg-[#59232a] text-white px-4 py-2 rounded-xl font-bold flex items-center gap-2 shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98]"
            >
              <WineIcon className="w-4 h-4 text-[#c9a03d]" />
              <span>Inventory Control</span>
              <span className="bg-[#c9a03d] text-[#1a0f0f] text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                {totalStockBottles} Btls
              </span>
            </button>

            {/* 2. PENDING ORDERS / DISPATCH BUTTON */}
            <button
              onClick={() => { setOrderFilter('pending'); setIsOrdersModalOpen(true); }}
              className={`px-4 py-2 rounded-xl font-bold flex items-center gap-2 transition-all shadow-sm ${
                pendingOrders.length > 0
                  ? 'bg-amber-500 hover:bg-amber-600 text-white ring-2 ring-amber-400/40'
                  : 'bg-neutral-100 hover:bg-neutral-200 text-neutral-700'
              }`}
            >
              <Truck className="w-4 h-4" />
              <span>Pending Deliveries</span>
              {pendingOrders.length > 0 && (
                <span className="bg-white text-amber-700 text-[10px] font-extrabold px-2 py-0.5 rounded-full animate-bounce">
                  {pendingOrders.length}
                </span>
              )}
            </button>

            {/* 3. VIP CLUB MEMBERS BUTTON */}
            <button
              onClick={() => setIsVipModalOpen(true)}
              className="bg-[#faf6f0] hover:bg-[#f2eae0] text-[#722f37] border border-[#e8ddd0] px-3.5 py-2 rounded-xl font-bold flex items-center gap-2 transition-colors"
            >
              <Crown className="w-4 h-4 text-[#c9a03d]" />
              <span>VIP Wine Club</span>
              <span className="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                {activeSubs.length}
              </span>
            </button>
          </div>

          <div className="flex items-center gap-2">
            {/* Quick Add Wine Button */}
            <button
              onClick={() => setIsAddWineOpen(true)}
              className="bg-emerald-700 hover:bg-emerald-800 text-white px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-xs transition-colors"
            >
              <Plus className="w-4 h-4" />
              <span>Insert Stock</span>
            </button>

            {/* View Order History */}
            <button
              onClick={() => { setOrderFilter('completed'); setIsOrdersModalOpen(true); }}
              className="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-3 py-2 rounded-xl font-semibold flex items-center gap-1.5 transition-colors"
            >
              <ShoppingBag className="w-3.5 h-3.5" />
              <span>Order Archive</span>
            </button>

            {/* Portal Logins */}
            <button
              onClick={() => setIsLogsModalOpen(true)}
              className="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-3 py-2 rounded-xl font-semibold flex items-center gap-1.5 transition-colors"
              title="View login logs and staff accounts"
            >
              <Mail className="w-3.5 h-3.5 text-neutral-500" />
              <span className="hidden sm:inline">Access Logs</span>
            </button>
          </div>
        </div>
      </div>

      {/* ==================== 3. COMPACT EXECUTIVE DASHBOARD ==================== */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-5 sm:px-8 py-6 space-y-6">
        
        {/* KPI CARDS ROW */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {/* Gross Revenue */}
          <div className="bg-white p-5 rounded-2xl border border-[#e8e0d8] shadow-xs space-y-1">
            <div className="flex justify-between items-center text-neutral-500 text-[10px] uppercase font-bold tracking-wider">
              <span>Total Revenue</span>
              <DollarSign className="w-4 h-4 text-[#1a6b3c]" />
            </div>
            <span className="text-2xl font-extrabold text-[#1a6b3c] block">
              E{totalGrossCombinedRevenue.toFixed(2)}
            </span>
            <p className="text-[10px] text-neutral-500">
              E{grossSalesRevenue.toFixed(2)} Sales • E{monthlyRecurringRevenue.toFixed(2)} MRR
            </p>
          </div>

          {/* Pending Deliveries & VIP Boxes */}
          <div
            onClick={() => { setOrderFilter('pending'); setIsOrdersModalOpen(true); }}
            className={`p-5 rounded-2xl border transition-all cursor-pointer space-y-1 ${
              pendingOrders.length > 0
                ? 'bg-amber-50/70 border-amber-300 hover:border-amber-400 shadow-xs'
                : 'bg-white border-[#e8e0d8]'
            }`}
          >
            <div className="flex justify-between items-center text-[10px] uppercase font-bold tracking-wider text-amber-900">
              <span>Pending Deliveries</span>
              <Hourglass className="w-4 h-4 text-amber-600" />
            </div>
            <div className="flex items-baseline gap-2">
              <span className="text-2xl font-extrabold text-amber-900 block">
                {pendingOrders.length} Orders
              </span>
              {vipPendingOrders.length > 0 && (
                <span className="text-[10px] font-extrabold text-[#722f37] bg-[#c9a03d]/20 px-2 py-0.5 rounded-full">
                  👑 {vipPendingOrders.length} VIP Box{vipPendingOrders.length > 1 ? 'es' : ''}
                </span>
              )}
            </div>
            <p className="text-[10px] text-amber-700 font-semibold flex items-center gap-1">
              <span>Click to review & dispatch</span>
              <ChevronRight className="w-3 h-3" />
            </p>
          </div>

          {/* VIP Wine Club MRR */}
          <div
            onClick={() => setIsVipModalOpen(true)}
            className="bg-white p-5 rounded-2xl border border-[#e8e0d8] shadow-xs space-y-1 cursor-pointer hover:border-[#c9a03d] transition-colors"
          >
            <div className="flex justify-between items-center text-neutral-500 text-[10px] uppercase font-bold tracking-wider">
              <span>VIP Members Club</span>
              <Crown className="w-4 h-4 text-[#c9a03d]" />
            </div>
            <span className="text-2xl font-extrabold text-[#c9a03d] block">
              {activeSubs.length} Active Members
            </span>
            <p className="text-[10px] text-emerald-700 font-semibold">
              E{monthlyRecurringRevenue.toFixed(2)}/mo recurring revenue
            </p>
          </div>

          {/* Available Stock Summary */}
          <div
            onClick={() => setIsInventoryModalOpen(true)}
            className="bg-white p-5 rounded-2xl border border-[#e8e0d8] shadow-xs space-y-1 cursor-pointer hover:border-[#722f37] transition-colors"
          >
            <div className="flex justify-between items-center text-neutral-500 text-[10px] uppercase font-bold tracking-wider">
              <span>Cellar Inventory</span>
              <WineIcon className="w-4 h-4 text-[#722f37]" />
            </div>
            <span className="text-2xl font-extrabold text-[#2c1a1a] block">
              {totalStockBottles} Bottles
            </span>
            <p className="text-[10px] text-neutral-500">
              Valuation: <strong className="text-[#722f37]">E{totalInventoryRetailValue.toFixed(2)}</strong> across {winesList.length} labels
            </p>
          </div>
        </div>

        {/* LOW STOCK ALERT BANNER (If any) */}
        {lowStockWines.length > 0 && (
          <div className="bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 flex items-center justify-between shadow-xs">
            <div className="flex items-center gap-3">
              <AlertTriangle className="w-5 h-5 text-amber-700 shrink-0" />
              <div>
                <strong className="text-amber-900 block font-bold text-xs">
                  Low Stock Cellar Warning: {lowStockWines.length} wine(s) have 10 or fewer bottles remaining!
                </strong>
                <span className="text-amber-700 text-[11px]">
                  {lowStockWines.map((w) => `${w.name} (${w.stock_quantity} left)`).join(' • ')}
                </span>
              </div>
            </div>
            <button
              onClick={() => { setShowLowStockOnly(true); setIsInventoryModalOpen(true); }}
              className="bg-amber-700 hover:bg-amber-800 text-white font-bold px-3.5 py-1.5 rounded-xl transition-colors text-xs shrink-0 shadow-xs"
            >
              Manage Low Stock
            </button>
          </div>
        )}

        {/* TWO-COLUMN EXECUTIVE WORKSPACE */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
          
          {/* LEFT 7 COLS: IMMEDIATE PENDING DELIVERIES & VIP BOX DISPATCH */}
          <div className="lg:col-span-7 bg-white rounded-3xl p-5 sm:p-6 border border-[#e8e0d8] shadow-xs space-y-4">
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-3">
              <div>
                <h3 className="font-serif font-bold text-base text-[#2c1a1a] flex items-center gap-2">
                  <Truck className="w-4 h-4 text-[#722f37]" />
                  <span>Immediate Pending Deliveries</span>
                  {pendingOrders.length > 0 && (
                    <span className="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                      {pendingOrders.length}
                    </span>
                  )}
                </h3>
                <p className="text-[11px] text-neutral-500">
                  Customer wine orders and VIP Member boxes awaiting delivery driver dispatch
                </p>
              </div>

              <button
                onClick={() => { setOrderFilter('pending'); setIsOrdersModalOpen(true); }}
                className="text-[#722f37] hover:underline font-bold text-[11px] flex items-center gap-1"
              >
                <span>View Full Queue</span>
                <ChevronRight className="w-3.5 h-3.5" />
              </button>
            </div>

            {pendingOrders.length === 0 ? (
              <div className="text-center py-8 text-neutral-400 space-y-2">
                <CheckCircle2 className="w-8 h-8 mx-auto text-emerald-500 opacity-60" />
                <p className="font-semibold text-xs text-neutral-600">All orders and VIP boxes have been delivered! 🎉</p>
                <p className="text-[10px] text-neutral-400">New customer orders and VIP subscriptions will appear here automatically.</p>
              </div>
            ) : (
              <div className="space-y-3">
                {pendingOrders.slice(0, 4).map((order) => (
                  <div
                    key={order.id}
                    className={`p-4 rounded-2xl border transition-all ${
                      order.orderType === 'vip_box'
                        ? 'bg-gradient-to-r from-[#faf6f0] to-[#fff9f0] border-[#c9a03d]/60 shadow-xs'
                        : 'bg-[#faf6f0] border-[#e8e0d8]'
                    }`}
                  >
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-black/5 pb-2.5">
                      <div className="flex items-center gap-2">
                        {order.orderType === 'vip_box' ? (
                          <span className="bg-[#c9a03d] text-[#1a0f0f] font-extrabold text-[9px] px-2 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                            <Crown className="w-3 h-3" /> VIP Member Box Delivery
                          </span>
                        ) : (
                          <span className="bg-[#722f37]/10 text-[#722f37] font-bold text-[9px] px-2 py-0.5 rounded-full uppercase">
                            Standard Cellar Order
                          </span>
                        )}
                        <span className="font-mono font-bold text-[#722f37] text-[11px]">{order.orderNumber}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className="font-bold text-[#1a6b3c] text-sm">E{order.total.toFixed(2)}</span>
                        <span className="text-neutral-400 text-[10px]">• {order.date}</span>
                      </div>
                    </div>

                    <div className="py-2.5 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                      <div>
                        <strong className="text-[#2c1a1a] block font-bold text-xs">{order.customerName}</strong>
                        <span className="text-neutral-500 text-[11px] block">{order.itemsDescription || `${order.itemsCount} bottle(s)`}</span>
                        {order.address && (
                          <span className="text-neutral-600 text-[10px] flex items-center gap-1 mt-1">
                            <MapPin className="w-3 h-3 text-red-600 shrink-0" />
                            <span>{order.address}, {order.city || 'Eswatini'}</span>
                          </span>
                        )}
                      </div>

                      <div className="flex flex-col sm:items-end justify-between gap-2">
                        {order.phone ? (
                          <a
                            href={`tel:${order.phone}`}
                            className="inline-flex items-center gap-1 text-[#722f37] font-bold hover:underline text-xs bg-white px-2.5 py-1 rounded-lg border border-[#e8ddd0]"
                          >
                            <Phone className="w-3 h-3 text-[#1a6b3c]" />
                            <span>Call Driver: {order.phone}</span>
                          </a>
                        ) : (
                          <span className="text-neutral-400 text-[11px]">{order.email}</span>
                        )}

                        <button
                          onClick={() => markOrderDone(order.id, order.orderNumber)}
                          className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-1.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition-colors self-start sm:self-auto"
                        >
                          <CheckCheck className="w-3.5 h-3.5" />
                          <span>Mark as Delivered</span>
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* RIGHT 5 COLS: QUICK INVENTORY SNAPSHOT & VIP SIGNUPS */}
          <div className="lg:col-span-5 space-y-6">
            
            {/* Quick Inventory Widget */}
            <div className="bg-white rounded-3xl p-5 border border-[#e8e0d8] shadow-xs space-y-3">
              <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-2.5">
                <h3 className="font-serif font-bold text-sm text-[#2c1a1a] flex items-center gap-1.5">
                  <WineIcon className="w-4 h-4 text-[#722f37]" />
                  <span>Cellar Stock Monitor</span>
                </h3>
                <button
                  onClick={() => setIsInventoryModalOpen(true)}
                  className="btn-wine text-[10px] px-3 py-1 font-bold rounded-lg"
                >
                  Open Control Modal →
                </button>
              </div>

              <div className="divide-y divide-[#f0ece8]">
                {winesList.slice(0, 4).map((w) => (
                  <div key={w.id} className="py-2.5 flex items-center justify-between">
                    <div className="flex items-center gap-2.5">
                      <Image
                        src={w.image_url}
                        alt={w.name}
                        width={24}
                        height={32}
                        className="object-contain rounded"
                      />
                      <div>
                        <strong className="text-[#2c1a1a] block font-semibold text-xs leading-tight">{w.name}</strong>
                        <span className="text-[10px] text-neutral-400">{w.variety} • {w.vintage}</span>
                      </div>
                    </div>

                    <div className="flex items-center gap-2">
                      <span className={`px-2 py-0.5 rounded-full font-bold text-[9px] uppercase ${
                        w.stock_quantity <= 10 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'
                      }`}>
                        {w.stock_quantity} left
                      </span>
                      <div className="flex items-center gap-1">
                        <button
                          onClick={() => adjustStock(w.id, -1)}
                          className="w-5 h-5 rounded bg-neutral-100 hover:bg-red-50 hover:text-red-600 font-bold flex items-center justify-center text-[10px]"
                          title="Reduce 1"
                        >
                          -
                        </button>
                        <button
                          onClick={() => adjustStock(w.id, 1)}
                          className="w-5 h-5 rounded bg-neutral-100 hover:bg-emerald-50 hover:text-emerald-600 font-bold flex items-center justify-center text-[10px]"
                          title="Add 1"
                        >
                          +
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* VIP Club Snapshot */}
            <div className="bg-white rounded-3xl p-5 border border-[#e8e0d8] shadow-xs space-y-3">
              <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-2.5">
                <h3 className="font-serif font-bold text-sm text-[#2c1a1a] flex items-center gap-1.5">
                  <Crown className="w-4 h-4 text-[#c9a03d]" />
                  <span>VIP Club Members ({activeSubs.length})</span>
                </h3>
                <button
                  onClick={() => setIsVipModalOpen(true)}
                  className="text-[#c9a03d] hover:underline font-bold text-[11px]"
                >
                  Manage Roster →
                </button>
              </div>

              <div className="divide-y divide-[#f0ece8]">
                {subscribersList.slice(0, 3).map((sub) => (
                  <div key={sub.id} className="py-2 flex items-center justify-between">
                    <div>
                      <strong className="text-[#2c1a1a] block font-semibold text-xs">{sub.fullName}</strong>
                      <span className="text-neutral-500 text-[10px]">{sub.planName} • E{sub.price}/mo</span>
                    </div>
                    <button
                      onClick={() => toggleSubscriptionStatus(sub.id, sub.status)}
                      className={`px-2 py-0.5 rounded-full text-[9px] font-bold uppercase transition-colors ${
                        sub.status === 'active'
                          ? 'bg-emerald-100 text-emerald-800 hover:bg-red-100 hover:text-red-800'
                          : 'bg-red-100 text-red-800 hover:bg-emerald-100 hover:text-emerald-800'
                      }`}
                    >
                      {sub.status}
                    </button>
                  </div>
                ))}
              </div>
            </div>

          </div>
        </div>
      </main>

      {/* ========================================================================= */}
      {/* POPUP MODAL 1: INVENTORY CONTROL (Full Stock Manager)                    */}
      {/* ========================================================================= */}
      {isInventoryModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-5xl w-full p-6 sm:p-8 shadow-2xl border-2 border-[#722f37] flex flex-col max-h-[90vh]">
            
            {/* Modal Header */}
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4 mb-4">
              <div>
                <h2 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <WineIcon className="w-5 h-5 text-[#722f37]" />
                  <span>Cellar Inventory Control</span>
                </h2>
                <p className="text-xs text-neutral-500 mt-0.5">
                  Total Valuation: <strong className="text-[#722f37]">E{totalInventoryRetailValue.toFixed(2)}</strong> across <strong className="text-[#1a6b3c]">{totalStockBottles} Bottles</strong>
                </p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => setIsAddWineOpen(true)}
                  className="bg-emerald-700 hover:bg-emerald-800 text-white px-3.5 py-1.5 rounded-xl font-bold flex items-center gap-1 shadow-xs text-xs"
                >
                  <Plus className="w-3.5 h-3.5" />
                  <span>Insert New Wine</span>
                </button>
                <button
                  onClick={() => { setIsInventoryModalOpen(false); setShowLowStockOnly(false); }}
                  className="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>
            </div>

            {/* Filter & Search Toolbar */}
            <div className="flex flex-col sm:flex-row items-center justify-between gap-3 mb-4">
              <div className="relative w-full sm:w-80">
                <Search className="w-3.5 h-3.5 text-neutral-400 absolute left-3 top-2.5" />
                <input
                  type="text"
                  placeholder="Search wine brand, variety, origin..."
                  value={wineSearch}
                  onChange={(e) => setWineSearch(e.target.value)}
                  className="w-full pl-8 pr-3 py-2 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl text-xs focus:outline-none focus:border-[#722f37]"
                />
              </div>

              <div className="flex items-center gap-2 w-full sm:w-auto">
                <button
                  onClick={() => setShowLowStockOnly(!showLowStockOnly)}
                  className={`px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors ${
                    showLowStockOnly
                      ? 'bg-amber-500 text-white shadow-xs'
                      : 'bg-[#faf6f0] text-neutral-700 border border-[#e8e0d8] hover:bg-neutral-100'
                  }`}
                >
                  <AlertTriangle className="w-3.5 h-3.5" />
                  <span>Show Low Stock Only ({lowStockWines.length})</span>
                </button>
              </div>
            </div>

            {/* Wine Inventory Table */}
            <div className="flex-1 overflow-y-auto border border-[#e8e0d8] rounded-2xl">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8] sticky top-0 z-10">
                  <tr>
                    <th className="p-3">Wine</th>
                    <th className="p-3">Variety</th>
                    <th className="p-3">Vintage</th>
                    <th className="p-3">Price</th>
                    <th className="p-3">Stock Level</th>
                    <th className="p-3">Quick Stock Update</th>
                    <th className="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {filteredWines.map((w) => (
                    <tr key={w.id} className="hover:bg-[#faf6f0] transition-colors">
                      <td className="p-3 flex items-center gap-3">
                        <Image
                          src={w.image_url}
                          alt={w.name}
                          width={28}
                          height={36}
                          className="object-contain rounded"
                        />
                        <div>
                          <strong className="text-[#2c1a1a] block font-bold">{w.name}</strong>
                          <span className="text-[10px] text-neutral-400">{w.origin}</span>
                        </div>
                      </td>
                      <td className="p-3 text-neutral-600 font-medium">{w.variety}</td>
                      <td className="p-3 font-semibold">{w.vintage}</td>
                      <td className="p-3 font-bold text-[#1a6b3c]">E{w.price.toFixed(2)}</td>
                      <td className="p-3">
                        <span
                          className={`px-2.5 py-1 rounded-full font-bold text-[10px] uppercase ${
                            w.stock_quantity === 0
                              ? 'bg-red-100 text-red-800'
                              : w.stock_quantity <= 10
                              ? 'bg-amber-100 text-amber-800'
                              : 'bg-emerald-100 text-emerald-800'
                          }`}
                        >
                          {w.stock_quantity} bottles {w.stock_quantity === 0 ? '(Out of Stock)' : w.stock_quantity <= 10 ? '(Low)' : ''}
                        </span>
                      </td>
                      <td className="p-3">
                        <div className="flex items-center gap-1.5">
                          <button
                            onClick={() => adjustStock(w.id, -1)}
                            className="w-6 h-6 rounded-lg bg-white border border-[#d8d0c8] hover:bg-red-50 hover:text-red-600 font-bold flex items-center justify-center text-xs transition-colors"
                            title="Decrement stock by 1"
                          >
                            -
                          </button>
                          <span className="w-8 text-center font-bold text-[#2c1a1a]">{w.stock_quantity}</span>
                          <button
                            onClick={() => adjustStock(w.id, 1)}
                            className="w-6 h-6 rounded-lg bg-white border border-[#d8d0c8] hover:bg-emerald-50 hover:text-emerald-600 font-bold flex items-center justify-center text-xs transition-colors"
                            title="Increment stock by 1"
                          >
                            +
                          </button>
                          <button
                            onClick={() => adjustStock(w.id, 6)}
                            className="px-2 py-0.5 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-[10px] font-semibold text-neutral-700 ml-1"
                            title="Add 1 case (+6 bottles)"
                          >
                            +6 Case
                          </button>
                        </div>
                      </td>
                      <td className="p-3 text-right">
                        <div className="flex items-center justify-end gap-1.5">
                          <button
                            onClick={() => setEditingWine(w)}
                            className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                            title="Edit wine details"
                          >
                            <Edit2 className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteWine(w.id)}
                            className="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Delete from cellar"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Modal Footer */}
            <div className="mt-4 flex justify-end">
              <button
                onClick={() => { setIsInventoryModalOpen(false); setShowLowStockOnly(false); }}
                className="btn-wine text-xs px-5 py-2 font-bold rounded-xl"
              >
                Done / Close Control Window
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* POPUP MODAL 2: ORDERS & DELIVERIES (Pending Queue & Full Archive)        */}
      {/* ========================================================================= */}
      {isOrdersModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-5xl w-full p-6 sm:p-8 shadow-2xl border-2 border-amber-500 flex flex-col max-h-[90vh]">
            
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4 mb-4">
              <div>
                <h2 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <Truck className="w-5 h-5 text-amber-600" />
                  <span>Orders & Dispatch Management</span>
                </h2>
                <p className="text-xs text-neutral-500 mt-0.5">
                  {orderFilter === 'pending'
                    ? `${pendingOrders.length} pending deliveries awaiting driver fulfillment`
                    : `${completedOrders.length} completed order archive`}
                </p>
              </div>

              <button
                onClick={() => setIsOrdersModalOpen(false)}
                className="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Sub-Tabs: Pending vs Completed */}
            <div className="flex gap-2 mb-4">
              <button
                onClick={() => setOrderFilter('pending')}
                className={`flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-xs transition-all ${
                  orderFilter === 'pending'
                    ? 'bg-amber-500 text-white shadow-sm'
                    : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'
                }`}
              >
                <Hourglass className="w-3.5 h-3.5" />
                <span>Pending Deliveries</span>
                <span className="bg-white/30 text-white text-[10px] px-1.5 py-0.2 rounded-full">
                  {pendingOrders.length}
                </span>
              </button>

              <button
                onClick={() => setOrderFilter('completed')}
                className={`flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-xs transition-all ${
                  orderFilter === 'completed'
                    ? 'bg-emerald-700 text-white shadow-sm'
                    : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'
                }`}
              >
                <CheckCheck className="w-3.5 h-3.5" />
                <span>Completed Orders</span>
                <span className="bg-white/30 text-white text-[10px] px-1.5 py-0.2 rounded-full">
                  {completedOrders.length}
                </span>
              </button>
            </div>

            {/* Orders Table */}
            <div className="flex-1 overflow-y-auto border border-[#e8e0d8] rounded-2xl">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8] sticky top-0 z-10">
                  <tr>
                    <th className="p-3">Order Ref / Type</th>
                    <th className="p-3">Customer</th>
                    <th className="p-3">Driver Contact</th>
                    <th className="p-3">Delivery Address</th>
                    <th className="p-3">Items / Selection</th>
                    <th className="p-3">Total</th>
                    <th className="p-3">Token</th>
                    <th className="p-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {(orderFilter === 'pending' ? pendingOrders : completedOrders).map((order) => (
                    <tr key={order.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3">
                        <span className="font-mono font-bold text-[#722f37] block">{order.orderNumber}</span>
                        {order.orderType === 'vip_box' ? (
                          <span className="inline-flex items-center gap-1 bg-[#c9a03d]/20 text-[#722f37] text-[9px] font-extrabold px-1.5 py-0.5 rounded-full mt-1">
                            <Crown className="w-2.5 h-2.5 text-[#c9a03d]" /> VIP Box
                          </span>
                        ) : (
                          <span className="text-[10px] text-neutral-400 capitalize">{order.paymentMethod}</span>
                        )}
                      </td>
                      <td className="p-3">
                        <strong className="text-[#2c1a1a] block">{order.customerName}</strong>
                        <span className="text-neutral-500 font-mono text-[10px]">{order.email}</span>
                      </td>
                      <td className="p-3">
                        {order.phone ? (
                          <a
                            href={`tel:${order.phone}`}
                            className="text-[#722f37] font-semibold hover:underline flex items-center gap-1"
                          >
                            <Phone className="w-3 h-3 text-[#1a6b3c]" />
                            {order.phone}
                          </a>
                        ) : (
                          <span className="text-neutral-400">—</span>
                        )}
                      </td>
                      <td className="p-3">
                        <span className="text-neutral-700 block">{order.address || 'Standard Delivery'}</span>
                        <span className="text-neutral-400 text-[10px]">{order.city || 'Eswatini'}</span>
                      </td>
                      <td className="p-3 text-neutral-600 font-medium">
                        {order.itemsDescription || `${order.itemsCount} bottle(s)`}
                      </td>
                      <td className="p-3 font-bold text-[#1a6b3c] text-sm">
                        E{order.total.toFixed(2)}
                      </td>
                      <td className="p-3">
                        <span className="font-mono font-bold text-[#722f37] bg-[#722f37]/10 px-2 py-0.5 rounded-lg text-[10px] flex items-center gap-1 w-max">
                          <QrCode className="w-3 h-3" />
                          {generateOrderToken(order.orderNumber)}
                        </span>
                      </td>
                      <td className="p-3 text-right">
                        {order.status !== 'completed' ? (
                          <button
                            onClick={() => markOrderDone(order.id, order.orderNumber)}
                            className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs flex items-center gap-1 shadow-xs transition-colors ml-auto"
                          >
                            <CheckCheck className="w-3.5 h-3.5" />
                            <span>Mark Delivered</span>
                          </button>
                        ) : (
                          <span className="inline-flex items-center gap-1 text-emerald-700 font-bold text-[11px]">
                            <CheckCircle2 className="w-3.5 h-3.5" /> Done
                          </span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-4 flex justify-end">
              <button
                onClick={() => setIsOrdersModalOpen(false)}
                className="btn-wine text-xs px-5 py-2 font-bold rounded-xl"
              >
                Close Window
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* POPUP MODAL 3: VIP CLUB MEMBERS (Tiers & Roster)                         */}
      {/* ========================================================================= */}
      {isVipModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl border-2 border-[#c9a03d] flex flex-col max-h-[90vh]">
            
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4 mb-4">
              <div>
                <h2 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <Crown className="w-5 h-5 text-[#c9a03d]" />
                  <span>VIP Wine Club Member Registry</span>
                </h2>
                <p className="text-xs text-neutral-500 mt-0.5">
                  Active Recurring Revenue: <strong className="text-[#1a6b3c]">E{monthlyRecurringRevenue.toFixed(2)}/mo</strong> ({activeSubs.length} Active Members)
                </p>
              </div>

              <button
                onClick={() => setIsVipModalOpen(false)}
                className="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Subscribers Table */}
            <div className="flex-1 overflow-y-auto border border-[#e8e0d8] rounded-2xl">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8] sticky top-0 z-10">
                  <tr>
                    <th className="p-3">VIP Member</th>
                    <th className="p-3">Box Tier</th>
                    <th className="p-3">Monthly Rate</th>
                    <th className="p-3">Delivery Address</th>
                    <th className="p-3">Status</th>
                    <th className="p-3 text-right">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {subscribersList.map((sub) => (
                    <tr key={sub.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3">
                        <strong className="text-[#2c1a1a] block font-semibold">{sub.fullName}</strong>
                        <span className="text-neutral-500 font-mono text-[10px]">{sub.email}</span>
                        {sub.phone && <span className="text-neutral-400 block text-[10px]">{sub.phone}</span>}
                      </td>
                      <td className="p-3 font-semibold text-[#722f37]">{sub.planName}</td>
                      <td className="p-3 font-bold text-[#1a6b3c]">E{sub.price.toFixed(2)}/mo</td>
                      <td className="p-3 text-neutral-600 text-[11px]">
                        {sub.address ? `${sub.address}, ${sub.city}` : sub.city || 'Eswatini'}
                      </td>
                      <td className="p-3">
                        <span
                          className={`px-2.5 py-1 rounded-full text-[9px] font-bold uppercase ${
                            sub.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                          }`}
                        >
                          {sub.status}
                        </span>
                      </td>
                      <td className="p-3 text-right">
                        <button
                          onClick={() => toggleSubscriptionStatus(sub.id, sub.status)}
                          className={`px-3 py-1.5 rounded-xl font-bold text-xs transition-colors ${
                            sub.status === 'active'
                              ? 'bg-red-50 hover:bg-red-100 text-red-700 border border-red-200'
                              : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200'
                          }`}
                        >
                          {sub.status === 'active' ? 'Cancel Sub' : 'Re-Activate'}
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="mt-4 flex justify-end">
              <button
                onClick={() => setIsVipModalOpen(false)}
                className="btn-wine text-xs px-5 py-2 font-bold rounded-xl"
              >
                Close Registry
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* POPUP MODAL 4: INSERT NEW WINE STOCK                                      */}
      {/* ========================================================================= */}
      {isAddWineOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <form
            onSubmit={handleAddWine}
            className="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border-2 border-[#c9a03d] space-y-4 max-h-[90vh] overflow-y-auto"
          >
            <div className="flex justify-between items-center border-b border-[#e8e0d8] pb-3">
              <h3 className="font-serif font-bold text-[#722f37] text-base flex items-center gap-2">
                <WineIcon className="w-5 h-5 text-[#c9a03d]" />
                <span>Insert New Wine Stock into Cellar</span>
              </h3>
              <button type="button" onClick={() => setIsAddWineOpen(false)} className="text-neutral-400 hover:text-neutral-700">
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Wine Name *</label>
                <input
                  type="text"
                  required
                  value={newWine.name}
                  onChange={(e) => setNewWine({ ...newWine, name: e.target.value })}
                  placeholder="e.g. Tenuta Sassicaia"
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-semibold text-xs"
                />
              </div>
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Grape Variety *</label>
                <input
                  type="text"
                  required
                  value={newWine.variety}
                  onChange={(e) => setNewWine({ ...newWine, variety: e.target.value })}
                  placeholder="e.g. Cabernet Sauvignon"
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl text-xs"
                />
              </div>
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Origin / Region *</label>
                <input
                  type="text"
                  required
                  value={newWine.origin}
                  onChange={(e) => setNewWine({ ...newWine, origin: e.target.value })}
                  placeholder="e.g. Stellenbosch, SA"
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl text-xs"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-4 gap-3">
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Price (E / SZL) *</label>
                <input
                  type="number"
                  required
                  min="1"
                  step="0.01"
                  value={newWine.price}
                  onChange={(e) => setNewWine({ ...newWine, price: Number(e.target.value) })}
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-bold text-[#1a6b3c] text-xs"
                />
              </div>
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Stock Quantity *</label>
                <input
                  type="number"
                  required
                  min="1"
                  value={newWine.stock_quantity}
                  onChange={(e) => setNewWine({ ...newWine, stock_quantity: Number(e.target.value) })}
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-bold text-xs"
                />
              </div>
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Vintage Year</label>
                <input
                  type="number"
                  min="1990"
                  max="2030"
                  value={newWine.vintage}
                  onChange={(e) => setNewWine({ ...newWine, vintage: Number(e.target.value) })}
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl text-xs"
                />
              </div>
              <div>
                <label className="block font-bold mb-1 text-[#2c1a1a]">Bottle Image</label>
                <select
                  value={newWine.image_url}
                  onChange={(e) => setNewWine({ ...newWine, image_url: e.target.value })}
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl text-xs"
                >
                  <option value="/wines/kanonkop.png">Kanonkop Pinotage PNG</option>
                  <option value="/wines/cloudybay.png">Cloudy Bay Sauvignon PNG</option>
                  <option value="/wines/opusone.png">Opus One Cabernet PNG</option>
                  <option value="/wines/margaux.png">Château Margaux PNG</option>
                  <option value="/wines/meerlust.png">Meerlust Rubicon PNG</option>
                  <option value="/wines/grand-vin-bordeaux-medoc.png">Grand Vin Bordeaux PNG</option>
                  <option value="/wines/the-reserve-red-blend.png">The Reserve Red Blend PNG</option>
                  <option value="/wines/franschhoek-cellar-sauvignon-blanc.png">Franschhoek Cellar PNG</option>
                </select>
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-3 border-t border-[#e8e0d8]">
              <button
                type="button"
                onClick={() => setIsAddWineOpen(false)}
                className="px-4 py-2 rounded-xl bg-neutral-200 text-neutral-700 font-semibold text-xs"
              >
                Cancel
              </button>
              <button type="submit" className="btn-wine text-xs px-6 py-2 rounded-xl font-bold shadow-md">
                Insert Stock into Cellar
              </button>
            </div>
          </form>
        </div>
      )}

      {/* ========================================================================= */}
      {/* POPUP MODAL 5: EDIT WINE STOCK                                            */}
      {/* ========================================================================= */}
      {editingWine && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <form
            onSubmit={handleSaveWineEdit}
            className="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border-2 border-blue-500 space-y-4"
          >
            <div className="flex justify-between items-center border-b border-[#e8e0d8] pb-3">
              <h3 className="font-serif font-bold text-blue-900 text-sm flex items-center gap-2">
                <Edit2 className="w-4 h-4 text-blue-600" />
                <span>Edit Stock Level: {editingWine.name}</span>
              </h3>
              <button type="button" onClick={() => setEditingWine(null)} className="text-neutral-400 hover:text-neutral-700">
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="space-y-3">
              <div>
                <label className="block font-bold mb-1">Wine Name</label>
                <input
                  type="text"
                  required
                  value={editingWine.name}
                  onChange={(e) => setEditingWine({ ...editingWine, name: e.target.value })}
                  className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-bold text-xs"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1">Price (E / SZL)</label>
                  <input
                    type="number"
                    required
                    step="0.01"
                    value={editingWine.price}
                    onChange={(e) => setEditingWine({ ...editingWine, price: Number(e.target.value) })}
                    className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-bold text-[#1a6b3c] text-xs"
                  />
                </div>
                <div>
                  <label className="block font-bold mb-1">Stock Quantity</label>
                  <input
                    type="number"
                    required
                    min="0"
                    value={editingWine.stock_quantity}
                    onChange={(e) => setEditingWine({ ...editingWine, stock_quantity: Number(e.target.value) })}
                    className="w-full p-2.5 bg-[#faf6f0] border border-[#d8d0c8] rounded-xl font-bold text-[#722f37] text-xs"
                  />
                </div>
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-3 border-t border-[#e8e0d8]">
              <button
                type="button"
                onClick={() => setEditingWine(null)}
                className="px-4 py-2 rounded-xl bg-neutral-200 text-neutral-700 font-semibold text-xs"
              >
                Cancel
              </button>
              <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl text-xs shadow-md">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      )}

      {/* ========================================================================= */}
      {/* POPUP MODAL 6: ACCESS LOGS & STAFF ROLES                                  */}
      {/* ========================================================================= */}
      {isLogsModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl border-2 border-neutral-700 flex flex-col max-h-[90vh]">
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4 mb-4">
              <div>
                <h2 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <Mail className="w-5 h-5 text-emerald-600" />
                  <span>Real-Time Logins & Staff Accounts</span>
                </h2>
                <p className="text-xs text-neutral-500 mt-0.5">Live security access trail and administrator staff roster</p>
              </div>

              <button
                onClick={() => setIsLogsModalOpen(false)}
                className="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 flex-1 overflow-y-auto">
              {/* Login Stream */}
              <div className="border border-[#e8e0d8] rounded-2xl p-4 space-y-3">
                <h4 className="font-bold text-xs text-[#722f37] border-b pb-2">Recent Portal Sign-ins</h4>
                <div className="divide-y divide-[#f0ece8]">
                  {loginLogs.map((log) => (
                    <div key={log.id} className="py-2 flex items-center justify-between">
                      <div>
                        <strong className="text-[#2c1a1a] block font-mono text-[11px]">{log.email}</strong>
                        <span className="text-neutral-400 text-[10px] flex items-center gap-1">
                          <Clock className="w-3 h-3 text-[#c9a03d]" /> {log.timestamp} • {log.ip || 'Eswatini'}
                        </span>
                      </div>
                      <span className="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-[#722f37]/10 text-[#722f37]">
                        {log.role}
                      </span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Staff Roster */}
              <div className="border border-[#e8e0d8] rounded-2xl p-4 space-y-3">
                <h4 className="font-bold text-xs text-[#722f37] border-b pb-2">Staff & Sommelier Accounts</h4>
                <div className="divide-y divide-[#f0ece8]">
                  {staffList.map((s) => (
                    <div key={s.id} className="py-2 flex items-center justify-between">
                      <div>
                        <strong className="text-[#2c1a1a] block font-semibold text-xs">{s.name}</strong>
                        <span className="text-neutral-400 text-[10px] font-mono">{s.email}</span>
                      </div>
                      <span className="text-[10px] font-bold text-[#722f37] bg-neutral-100 px-2 py-0.5 rounded-lg">
                        {s.role}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            <div className="mt-4 flex justify-end">
              <button
                onClick={() => setIsLogsModalOpen(false)}
                className="btn-wine text-xs px-5 py-2 font-bold rounded-xl"
              >
                Close Logs
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
