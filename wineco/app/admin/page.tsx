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
  ShieldCheck,
  Search,
  RefreshCw,
  Phone,
  Hourglass,
  CheckCheck,
  QrCode,
} from 'lucide-react';
import { INITIAL_WINES } from '@/lib/mock-data';
import { Wine, Subscriber, PortalLoginLog } from '@/lib/types';

// Derive an 8-char token from order number
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
  const [activeTab, setActiveTab] = useState<'overview' | 'wines' | 'orders' | 'subscriptions' | 'logins' | 'staff'>('overview');
  const [orderFilter, setOrderFilter] = useState<'pending' | 'done'>('pending');

  // Wine inventory state
  const [winesList, setWinesList] = useState<Wine[]>(INITIAL_WINES);
  const [isAddWineOpen, setIsAddWineOpen] = useState(false);
  const [editingWine, setEditingWine] = useState<Wine | null>(null);
  const [wineSearch, setWineSearch] = useState('');

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

  // Orders state
  const [ordersList, setOrdersList] = useState([
    {
      id: 17,
      orderNumber: 'ORD-20260821-5155',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      phone: '+268 7686 9104',
      total: 1145.4,
      itemsCount: 3,
      status: 'completed',
      paymentMethod: 'cash',
      date: '2026-08-21',
    },
    {
      id: 16,
      orderNumber: 'ORD-20260724-8731',
      customerName: 'Lihle Mfundo Mbhamali',
      email: 'mfundombhamaly@gmail.com',
      phone: '+268 7612 3456',
      total: 446.75,
      itemsCount: 1,
      status: 'completed',
      paymentMethod: 'bank_transfer',
      date: '2026-07-24',
    },
    {
      id: 13,
      orderNumber: 'ORD-20260722-3587',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      phone: '+268 7686 9104',
      total: 700.0,
      itemsCount: 2,
      status: 'processing',
      paymentMethod: 'cash',
      date: '2026-07-22',
    },
    {
      id: 6,
      orderNumber: 'ORD-20260705-8769',
      customerName: 'Mbhamaly Mfundo',
      email: 'mfundombhamaly@gmail.com',
      phone: '+268 7612 3456',
      total: 2987.7,
      itemsCount: 6,
      status: 'completed',
      paymentMethod: 'bank_transfer',
      date: '2026-07-05',
    },
    {
      id: 5,
      orderNumber: 'ORD-20260630-1042',
      customerName: 'Siphiwo Sethu Thikazi',
      email: 'siphiwosethuthikazi@gmail.com',
      phone: '+268 7838 1971',
      total: 1230.0,
      itemsCount: 2,
      status: 'completed',
      paymentMethod: 'bank_transfer',
      date: '2026-06-30',
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
    {
      id: 'sub_5',
      fullName: 'Mandla Dube',
      email: 'mandla.dube@swazi.net',
      phone: '+268 7602 1199',
      planName: 'Grand Reserve Society',
      price: 4999,
      status: 'active',
      paymentMethod: 'Bank EFT',
      startDate: '2026-08-18',
      city: 'Mbabane',
    },
  ]);

  // Real-time portal login logs
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
    {
      id: 'log_4',
      email: 'staff@wineco.sz',
      role: 'staff',
      timestamp: '2026-09-02 16:40:05',
      status: 'success',
      ip: '197.234.210.8 (Manzini)',
    },
    {
      id: 'log_5',
      email: 'mfundombhamaly@gmail.com',
      role: 'member',
      timestamp: '2026-09-02 11:22:18',
      status: 'success',
      ip: '102.134.88.55 (Manzini)',
    },
  ]);

  const [staffList] = useState([
    { id: 1, name: 'Siphiwo Sethu Thikazi', email: 'siphiwosethuthikazi@gmail.com', role: 'Super Admin', status: 'Active' },
    { id: 2, name: 'Administrator', email: 'admin@wineco.sz', role: 'Admin', status: 'Active' },
    { id: 3, name: 'Phumelele Dlamini', email: 'phumelele@wineco.sz', role: 'Manager', status: 'Active' },
    { id: 4, name: 'Lihle Mbhamali', email: 'lihle@wineco.sz', role: 'Sommelier', status: 'Active' },
  ]);

  // Fetch real-time subscribers & portal logins from API on mount
  const fetchRealtimeData = async () => {
    try {
      const subRes = await fetch('/api/subscriptions');
      const subData = await subRes.json();
      if (subData.success && subData.subscribers) {
        setSubscribersList(subData.subscribers);
      }

      const logRes = await fetch('/api/portal-login');
      const logData = await logRes.json();
      if (logData.success && logData.logs) {
        setLoginLogs(logData.logs);
      }
    } catch (e) {
      console.warn('Real-time sync error (fallback active):', e);
    }
  };

  useEffect(() => {
    fetchRealtimeData();
  }, []);

  // Subscriptions calculations
  const activeSubs = subscribersList.filter((s) => s.status === 'active');
  const cancelledSubs = subscribersList.filter((s) => s.status === 'cancelled');
  const monthlyRecurringRevenue = activeSubs.reduce((sum, s) => sum + s.price, 0);

  // Toggle Subscription Status (Activate / Cancel)
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
      console.error('Failed to update subscription status on server:', e);
    }
  };

  // Inventory calculations
  const totalStockBottles = winesList.reduce((sum, w) => sum + (Number(w.stock_quantity) || 0), 0);
  const totalInventoryRetailValue = winesList.reduce((sum, w) => sum + (w.price * (w.stock_quantity || 0)), 0);
  const lowStockWines = winesList.filter((w) => w.stock_quantity <= 10);

  // Gross Revenue calculations
  const grossInventoryRevenue = ordersList
    .filter((o) => o.status !== 'cancelled')
    .reduce((sum, o) => sum + o.total, 0);
  const totalBottlesSold = ordersList
    .filter((o) => o.status !== 'cancelled')
    .reduce((sum, o) => sum + (o.itemsCount || 1), 0);
  const averageOrderValue = ordersList.length > 0 ? grossInventoryRevenue / ordersList.length : 0;
  const totalGrossCombinedRevenue = grossInventoryRevenue + monthlyRecurringRevenue;

  // Quick Stock adjustments
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

  // Add / Insert New Stock
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

  // Edit Wine Modal submit
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

  // Mark order as Done
  const markOrderDone = (orderId: number) => {
    setOrdersList((prev) =>
      prev.map((o) => (o.id === orderId ? { ...o, status: 'completed' } : o))
    );
  };

  const filteredWines = winesList.filter(
    (w) =>
      w.name.toLowerCase().includes(wineSearch.toLowerCase()) ||
      w.variety.toLowerCase().includes(wineSearch.toLowerCase()) ||
      w.origin.toLowerCase().includes(wineSearch.toLowerCase())
  );

  const pendingOrders = ordersList.filter(
    (o) => o.status === 'processing' || o.status === 'pending' || o.status === 'payment_pending'
  );
  const doneOrders = ordersList.filter(
    (o) => o.status === 'completed' || o.status === 'cancelled'
  );
  const displayedOrders = orderFilter === 'pending' ? pendingOrders : doneOrders;

  return (
    <div className="min-h-screen bg-[#f5efe8] flex flex-col text-neutral-800 text-xs">
      {/* Top Admin Header */}
      <header className="bg-[#150d0e] text-white px-6 py-4 border-b-2 border-[#c9a03d] flex items-center justify-between shadow-lg">
        <div className="flex items-center gap-4">
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 bg-[#1a0f0f] rounded-xl p-0.5 border-2 border-[#c9a03d] flex items-center justify-center shadow-md">
              <Image src="/wines/logo1.jpg" alt="Logo" width={36} height={36} className="rounded-lg object-cover" />
            </div>
            <div>
              <span className="font-serif font-bold text-lg text-white block leading-tight">Wine & Co.</span>
              <span className="text-[9px] uppercase font-bold text-[#c9a03d] tracking-widest block">
                Executive Portal
              </span>
            </div>
          </Link>
          <span className="hidden sm:inline-block text-[10px] bg-[#c9a03d]/20 text-[#c9a03d] border border-[#c9a03d]/40 px-2.5 py-0.5 rounded-full font-bold">
            Real-Time DB Sync Active
          </span>
        </div>

        <div className="flex items-center gap-4">
          <button
            onClick={fetchRealtimeData}
            className="flex items-center gap-1 bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-xl transition-all"
            title="Refresh DB data"
          >
            <RefreshCw className="w-3.5 h-3.5 text-[#c9a03d]" />
            <span className="hidden sm:inline">Refresh Data</span>
          </button>
          <div className="text-right hidden sm:block">
            <span className="text-white/90 block font-bold">Siphiwo Sethu Thikazi</span>
            <span className="text-[#c9a03d] text-[10px] font-semibold">Super Admin • Eswatini</span>
          </div>
          <Link
            href="/"
            className="flex items-center gap-1.5 bg-white/10 hover:bg-[#722f37] text-white px-3.5 py-2 rounded-xl transition-colors font-medium border border-white/10"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Storefront</span>
          </Link>
        </div>
      </header>

      {/* Main Admin Dashboard */}
      <div className="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        {/* Navigation Tabs */}
        <div className="flex flex-wrap gap-2 bg-white p-2 rounded-2xl border border-[#e8e0d8] shadow-sm">
          <button
            onClick={() => setActiveTab('overview')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'overview'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <LayoutDashboard className="w-4 h-4" />
            <span>Overview</span>
          </button>

          <button
            onClick={() => setActiveTab('wines')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'wines'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <WineIcon className="w-4 h-4" />
            <span>Inventory Management ({totalStockBottles} Bottles)</span>
          </button>

          <button
            onClick={() => setActiveTab('subscriptions')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'subscriptions'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <Crown className="w-4 h-4 text-[#c9a03d]" />
            <span>Subscriptions ({activeSubs.length} Active)</span>
          </button>

          <button
            onClick={() => setActiveTab('orders')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'orders'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <ShoppingBag className="w-4 h-4" />
            <span>
              Sales & Orders
              {pendingOrders.length > 0 && (
                <span className="ml-1.5 bg-amber-500 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full">
                  {pendingOrders.length}
                </span>
              )}
            </span>
          </button>

          <button
            onClick={() => setActiveTab('logins')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'logins'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <Mail className="w-4 h-4 text-emerald-600" />
            <span>Real-Time Logins & VIP Leads</span>
          </button>

          <button
            onClick={() => setActiveTab('staff')}
            className={`px-4 py-2 rounded-xl font-bold transition-all flex items-center gap-2 ${
              activeTab === 'staff'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <Users className="w-4 h-4" />
            <span>Staff Roles</span>
          </button>
        </div>

        {/* ==================== TAB 1: OVERVIEW & REVENUE ==================== */}
        {activeTab === 'overview' && (
          <div className="space-y-6">
            {/* Metric KPI Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              {/* Gross Revenue Card */}
              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="font-bold uppercase tracking-wider text-neutral-500 text-[10px]">
                    Gross Inventory Sales
                  </span>
                  <DollarSign className="w-5 h-5 text-[#1a6b3c]" />
                </div>
                <span className="text-2xl font-bold text-[#1a6b3c] block">
                  E{grossInventoryRevenue.toFixed(2)}
                </span>
                <p className="text-[11px] text-neutral-500">
                  From {totalBottlesSold} bottles delivered across {ordersList.length} orders
                </p>
              </div>

              {/* Recurring Subscriptions Revenue */}
              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="font-bold uppercase tracking-wider text-neutral-500 text-[10px]">
                    Active Subscriptions (MRR)
                  </span>
                  <Crown className="w-5 h-5 text-[#c9a03d]" />
                </div>
                <span className="text-2xl font-bold text-[#c9a03d] block">
                  E{monthlyRecurringRevenue.toFixed(2)}
                </span>
                <p className="text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                  <CheckCircle2 className="w-3.5 h-3.5" /> {activeSubs.length} Active monthly members
                </p>
              </div>

              {/* Total Stock Bottles */}
              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="font-bold uppercase tracking-wider text-neutral-500 text-[10px]">
                    Available Stock
                  </span>
                  <WineIcon className="w-5 h-5 text-[#722f37]" />
                </div>
                <span className="text-2xl font-bold text-[#2c1a1a] block">
                  {totalStockBottles} Bottles
                </span>
                <p className="text-[11px] text-neutral-500">
                  Total Valuation: <strong className="text-[#722f37]">E{totalInventoryRetailValue.toFixed(2)}</strong>
                </p>
              </div>

              {/* Combined Total Gross Revenue */}
              <div className="bg-white p-6 rounded-3xl border-2 border-[#c9a03d]/50 shadow-sm space-y-2 bg-gradient-to-br from-white to-[#faf6f0]">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="font-bold uppercase tracking-wider text-[#722f37] text-[10px]">
                    Combined Gross Revenue
                  </span>
                  <TrendingUp className="w-5 h-5 text-[#1a6b3c]" />
                </div>
                <span className="text-2xl font-extrabold text-[#1a6b3c] block">
                  E{totalGrossCombinedRevenue.toFixed(2)}
                </span>
                <p className="text-[11px] text-neutral-600">
                  Avg Order: E{averageOrderValue.toFixed(2)} • +18.4% MoM
                </p>
              </div>
            </div>

            {/* Quick Actions & Stock Alerts */}
            {lowStockWines.length > 0 && (
              <div className="bg-amber-50 border border-amber-300 rounded-2xl p-4 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <AlertTriangle className="w-5 h-5 text-amber-700 shrink-0" />
                  <div>
                    <strong className="text-amber-900 block font-bold text-xs">
                      Low Stock Alert: {lowStockWines.length} wine(s) running low in cellar!
                    </strong>
                    <span className="text-amber-700 text-[11px]">
                      {lowStockWines.map((w) => `${w.name} (${w.stock_quantity} left)`).join(', ')}
                    </span>
                  </div>
                </div>
                <button
                  onClick={() => setActiveTab('wines')}
                  className="bg-amber-700 hover:bg-amber-800 text-white font-bold px-3 py-1.5 rounded-xl transition-colors text-xs"
                >
                  Restock Now
                </button>
              </div>
            )}

            {/* Pending Orders Alert */}
            {pendingOrders.length > 0 && (
              <div className="bg-blue-50 border border-blue-300 rounded-2xl p-4 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <Hourglass className="w-5 h-5 text-blue-700 shrink-0" />
                  <div>
                    <strong className="text-blue-900 block font-bold text-xs">
                      {pendingOrders.length} Pending Order(s) Awaiting Fulfilment
                    </strong>
                    <span className="text-blue-700 text-[11px]">
                      {pendingOrders.map((o) => o.orderNumber).join(', ')}
                    </span>
                  </div>
                </div>
                <button
                  onClick={() => { setActiveTab('orders'); setOrderFilter('pending'); }}
                  className="bg-blue-700 hover:bg-blue-800 text-white font-bold px-3 py-1.5 rounded-xl transition-colors text-xs"
                >
                  Manage Pending
                </button>
              </div>
            )}

            {/* Subscriptions Snapshot & Recent Sales */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Active Subscriptions Snapshot */}
              <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b pb-3">
                  <h3 className="font-serif font-bold text-base text-[#2c1a1a]">VIP Wine Club Members</h3>
                  <button onClick={() => setActiveTab('subscriptions')} className="text-[#c9a03d] font-bold hover:underline text-xs">
                    Manage All ({subscribersList.length}) →
                  </button>
                </div>
                <div className="divide-y divide-[#f0ece8]">
                  {subscribersList.slice(0, 4).map((sub) => (
                    <div key={sub.id} className="py-3 flex items-center justify-between">
                      <div>
                        <strong className="text-[#2c1a1a] block">{sub.fullName}</strong>
                        <span className="text-neutral-500 text-[11px]">{sub.email} • {sub.planName}</span>
                      </div>
                      <div className="flex items-center gap-3">
                        <span className="font-bold text-[#1a6b3c]">E{sub.price}/mo</span>
                        <button
                          onClick={() => toggleSubscriptionStatus(sub.id, sub.status)}
                          className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition-colors ${
                            sub.status === 'active'
                              ? 'bg-emerald-100 text-emerald-800 hover:bg-red-100 hover:text-red-800'
                              : 'bg-red-100 text-red-800 hover:bg-emerald-100 hover:text-emerald-800'
                          }`}
                          title="Click to toggle active/cancel"
                        >
                          {sub.status}
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Real-time Email Access & Portal Logins */}
              <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b pb-3">
                  <h3 className="font-serif font-bold text-base text-[#2c1a1a] flex items-center gap-2">
                    <Mail className="w-4 h-4 text-emerald-600" />
                    <span>Real-Time Portal Logins</span>
                  </h3>
                  <button onClick={() => setActiveTab('logins')} className="text-[#722f37] font-bold hover:underline text-xs">
                    View All ({loginLogs.length}) →
                  </button>
                </div>
                <div className="divide-y divide-[#f0ece8]">
                  {loginLogs.slice(0, 4).map((log) => (
                    <div key={log.id} className="py-3 flex items-center justify-between">
                      <div>
                        <strong className="text-[#2c1a1a] block font-mono text-[11px]">{log.email}</strong>
                        <span className="text-neutral-500 text-[10px] flex items-center gap-1">
                          <Clock className="w-3 h-3 text-[#c9a03d]" /> {log.timestamp} • {log.ip || 'Eswatini'}
                        </span>
                      </div>
                      <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#722f37]/10 text-[#722f37] uppercase">
                        {log.role}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ==================== TAB 2: WINE INVENTORY (ADD, EDIT, STOCK LEVELS) ==================== */}
        {activeTab === 'wines' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Wine Cellar Inventory</h3>
                <p className="text-xs text-neutral-500">
                  Total Stock: <strong className="text-[#1a6b3c]">{totalStockBottles} Bottles</strong> across {winesList.length} labels • Valuation: <strong className="text-[#722f37]">E{totalInventoryRetailValue.toFixed(2)}</strong>
                </p>
              </div>

              <div className="flex items-center gap-3 w-full sm:w-auto">
                <div className="relative flex-1 sm:w-64">
                  <Search className="w-3.5 h-3.5 text-neutral-400 absolute left-3 top-3" />
                  <input
                    type="text"
                    placeholder="Search wine, variety, origin..."
                    value={wineSearch}
                    onChange={(e) => setWineSearch(e.target.value)}
                    className="w-full pl-8 pr-3 py-2 bg-[#faf6f0] border border-[#e8e0d8] rounded-xl text-xs"
                  />
                </div>
                <button
                  onClick={() => setIsAddWineOpen(true)}
                  className="bg-[#722f37] hover:bg-[#59232a] text-white px-4 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-md shrink-0 transition-colors"
                >
                  <Plus className="w-4 h-4" />
                  <span>Insert New Stock</span>
                </button>
              </div>
            </div>

            {/* Insert New Stock Form Modal */}
            {isAddWineOpen && (
              <form onSubmit={handleAddWine} className="p-6 rounded-2xl bg-[#faf6f0] border-2 border-[#c9a03d] space-y-4 animate-in fade-in">
                <div className="flex justify-between items-center border-b border-[#e8e0d8] pb-2">
                  <h4 className="font-serif font-bold text-[#722f37] text-sm flex items-center gap-2">
                    <WineIcon className="w-4 h-4 text-[#c9a03d]" />
                    <span>Insert New Wine Stock into Cellar</span>
                  </h4>
                  <button type="button" onClick={() => setIsAddWineOpen(false)} className="text-neutral-400 hover:text-neutral-700">
                    <XCircle className="w-5 h-5" />
                  </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block font-bold mb-1 text-[#2c1a1a]">Wine Brand & Name *</label>
                    <input
                      type="text"
                      required
                      value={newWine.name}
                      onChange={(e) => setNewWine({ ...newWine, name: e.target.value })}
                      placeholder="e.g. Tenuta San Guido Sassicaia"
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-semibold"
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
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl"
                    />
                  </div>
                  <div>
                    <label className="block font-bold mb-1 text-[#2c1a1a]">Origin / Region *</label>
                    <input
                      type="text"
                      required
                      value={newWine.origin}
                      onChange={(e) => setNewWine({ ...newWine, origin: e.target.value })}
                      placeholder="e.g. Stellenbosch, South Africa"
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div>
                    <label className="block font-bold mb-1 text-[#2c1a1a]">Price (E / SZL) *</label>
                    <input
                      type="number"
                      required
                      min="1"
                      step="0.01"
                      value={newWine.price}
                      onChange={(e) => setNewWine({ ...newWine, price: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-bold text-[#1a6b3c]"
                    />
                  </div>
                  <div>
                    <label className="block font-bold mb-1 text-[#2c1a1a]">Initial Stock (Bottles) *</label>
                    <input
                      type="number"
                      required
                      min="1"
                      value={newWine.stock_quantity}
                      onChange={(e) => setNewWine({ ...newWine, stock_quantity: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-bold"
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
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl"
                    />
                  </div>
                  <div>
                    <label className="block font-bold mb-1 text-[#2c1a1a]">Bottle Image Path</label>
                    <select
                      value={newWine.image_url}
                      onChange={(e) => setNewWine({ ...newWine, image_url: e.target.value })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl"
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

                <div className="flex justify-end gap-2 pt-2">
                  <button
                    type="button"
                    onClick={() => setIsAddWineOpen(false)}
                    className="px-4 py-2 rounded-xl bg-neutral-200 text-neutral-700 font-semibold"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="bg-[#722f37] hover:bg-[#59232a] text-white font-bold px-6 py-2 rounded-xl shadow-md">
                    Insert Stock into Inventory
                  </button>
                </div>
              </form>
            )}

            {/* Edit Wine Modal */}
            {editingWine && (
              <form onSubmit={handleSaveWineEdit} className="p-6 rounded-2xl bg-[#faf6f0] border-2 border-blue-500 space-y-4 animate-in fade-in">
                <div className="flex justify-between items-center border-b border-[#e8e0d8] pb-2">
                  <h4 className="font-serif font-bold text-blue-900 text-sm flex items-center gap-2">
                    <Edit2 className="w-4 h-4 text-blue-600" />
                    <span>Edit Wine & Stock Level: {editingWine.name}</span>
                  </h4>
                  <button type="button" onClick={() => setEditingWine(null)} className="text-neutral-400 hover:text-neutral-700">
                    <XCircle className="w-5 h-5" />
                  </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block font-bold mb-1">Wine Name</label>
                    <input
                      type="text"
                      required
                      value={editingWine.name}
                      onChange={(e) => setEditingWine({ ...editingWine, name: e.target.value })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-bold"
                    />
                  </div>
                  <div>
                    <label className="block font-bold mb-1">Price (E / SZL)</label>
                    <input
                      type="number"
                      required
                      step="0.01"
                      value={editingWine.price}
                      onChange={(e) => setEditingWine({ ...editingWine, price: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-bold text-[#1a6b3c]"
                    />
                  </div>
                  <div>
                    <label className="block font-bold mb-1">Stock Level (Bottles Available)</label>
                    <input
                      type="number"
                      required
                      min="0"
                      value={editingWine.stock_quantity}
                      onChange={(e) => setEditingWine({ ...editingWine, stock_quantity: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-xl font-bold text-[#722f37]"
                    />
                  </div>
                </div>

                <div className="flex justify-end gap-2 pt-2">
                  <button
                    type="button"
                    onClick={() => setEditingWine(null)}
                    className="px-4 py-2 rounded-xl bg-neutral-200 text-neutral-700 font-semibold"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-xl shadow-md">
                    Update Wine & Stock
                  </button>
                </div>
              </form>
            )}

            {/* Inventory Table with Inline Stock Adjusters */}
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Wine</th>
                    <th className="p-3">Variety</th>
                    <th className="p-3">Origin</th>
                    <th className="p-3">Vintage</th>
                    <th className="p-3">Price</th>
                    <th className="p-3">Available Stock</th>
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
                          <strong className="text-[#2c1a1a] block">{w.name}</strong>
                          <span className="text-[10px] text-neutral-400">ID: #{w.id}</span>
                        </div>
                      </td>
                      <td className="p-3 text-neutral-600 font-medium">{w.variety}</td>
                      <td className="p-3 text-neutral-500">{w.origin}</td>
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
                        {/* Quick inline stock buttons */}
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
                            className="px-1.5 py-0.5 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-[10px] font-semibold text-neutral-600 ml-1"
                            title="Add 1 case (+6 bottles)"
                          >
                            +6 (Case)
                          </button>
                        </div>
                      </td>
                      <td className="p-3 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => setEditingWine(w)}
                            className="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                            title="Edit details"
                          >
                            <Edit2 className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteWine(w.id)}
                            className="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            title="Delete Wine"
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
          </div>
        )}

        {/* ==================== TAB 3: SUBSCRIPTIONS (ACTIVATE / CANCEL / LIVE COUNT) ==================== */}
        {activeTab === 'subscriptions' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <Crown className="w-5 h-5 text-[#c9a03d]" />
                  <span>Wine Club & VIP Subscriptions</span>
                </h3>
                <p className="text-xs text-neutral-500">
                  Manage membership tiers, authorize access, and activate or cancel subscriptions
                </p>
              </div>

              <div className="flex items-center gap-2">
                <span className="bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5">
                  <CheckCircle2 className="w-4 h-4" />
                  <span>{activeSubs.length} Active Members</span>
                </span>
                <span className="bg-neutral-100 text-neutral-600 px-3 py-1.5 rounded-xl font-bold text-xs">
                  {cancelledSubs.length} Cancelled
                </span>
              </div>
            </div>

            {/* Tier Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-xs font-bold text-[#722f37] block">Essential Elegance</strong>
                <span className="text-xl font-extrabold text-[#1a6b3c]">
                  {subscribersList.filter((s) => s.planName.includes('Elegance') && s.status === 'active').length} Active
                </span>
                <p className="text-[10px] text-neutral-500 mt-0.5">E499 / month • 2 bottles</p>
              </div>

              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-xs font-bold text-[#722f37] block">Vineyard Voyager</strong>
                <span className="text-xl font-extrabold text-[#1a6b3c]">
                  {subscribersList.filter((s) => s.planName.includes('Voyager') && s.status === 'active').length} Active
                </span>
                <p className="text-[10px] text-neutral-500 mt-0.5">E999 / month • 4 bottles</p>
              </div>

              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-xs font-bold text-[#722f37] block">Luxury Reserve</strong>
                <span className="text-xl font-extrabold text-[#1a6b3c]">
                  {subscribersList.filter((s) => s.planName.includes('Reserve') && s.status === 'active').length} Active
                </span>
                <p className="text-[10px] text-neutral-500 mt-0.5">E1,999 / month • 6 bottles</p>
              </div>

              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-xs font-bold text-[#722f37] block">Monthly Club Revenue</strong>
                <span className="text-xl font-extrabold text-[#c9a03d]">
                  E{monthlyRecurringRevenue.toFixed(2)}
                </span>
                <p className="text-[10px] text-emerald-700 font-semibold mt-0.5">Recurring MRR</p>
              </div>
            </div>

            {/* Subscribers Table with Activate / Cancel */}
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Subscriber</th>
                    <th className="p-3">Plan Tier</th>
                    <th className="p-3">Monthly Rate</th>
                    <th className="p-3">Payment Method</th>
                    <th className="p-3">Start Date</th>
                    <th className="p-3">Current Status</th>
                    <th className="p-3 text-right">Subscription Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {subscribersList.map((sub) => (
                    <tr key={sub.id} className="hover:bg-[#faf6f0] transition-colors">
                      <td className="p-3">
                        <strong className="text-[#2c1a1a] block font-semibold">{sub.fullName}</strong>
                        <span className="text-neutral-500 font-mono text-[11px]">{sub.email}</span>
                        {sub.phone && <span className="text-neutral-400 block text-[10px]">{sub.phone}</span>}
                      </td>
                      <td className="p-3 font-semibold text-[#722f37]">{sub.planName}</td>
                      <td className="p-3 font-bold text-[#1a6b3c]">E{sub.price.toFixed(2)}/mo</td>
                      <td className="p-3 capitalize">{sub.paymentMethod}</td>
                      <td className="p-3 text-neutral-500">{sub.startDate}</td>
                      <td className="p-3">
                        <span
                          className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase ${
                            sub.status === 'active'
                              ? 'bg-emerald-100 text-emerald-800'
                              : 'bg-red-100 text-red-800'
                          }`}
                        >
                          {sub.status}
                        </span>
                      </td>
                      <td className="p-3 text-right">
                        {sub.status === 'active' ? (
                          <button
                            onClick={() => toggleSubscriptionStatus(sub.id, 'active')}
                            className="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-xl font-bold text-xs transition-colors inline-flex items-center gap-1"
                            title="Cancel this subscription"
                          >
                            <XCircle className="w-3.5 h-3.5" />
                            <span>Cancel Subscription</span>
                          </button>
                        ) : (
                          <button
                            onClick={() => toggleSubscriptionStatus(sub.id, 'cancelled')}
                            className="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-xl font-bold text-xs transition-colors inline-flex items-center gap-1"
                            title="Re-activate this subscription"
                          >
                            <CheckCircle2 className="w-3.5 h-3.5" />
                            <span>Re-Activate</span>
                          </button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* ==================== TAB 4: ORDERS — PENDING / DONE ==================== */}
        {activeTab === 'orders' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Customer Sales & Orders</h3>
                <p className="text-xs text-neutral-500">
                  Gross Sales: <strong className="text-[#1a6b3c]">E{grossInventoryRevenue.toFixed(2)}</strong> across {ordersList.length} orders
                </p>
              </div>

              <div className="bg-[#faf6f0] px-4 py-2 rounded-xl border border-[#e8e0d8] text-xs">
                <span className="text-neutral-500 mr-2">Average Order Value:</span>
                <strong className="text-[#722f37]">E{averageOrderValue.toFixed(2)}</strong>
              </div>
            </div>

            {/* Pending / Done Sub-Tabs */}
            <div className="flex gap-2">
              <button
                onClick={() => setOrderFilter('pending')}
                className={`flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs transition-all border-2 ${
                  orderFilter === 'pending'
                    ? 'bg-amber-500 text-white border-amber-500 shadow-md'
                    : 'bg-amber-50 text-amber-700 border-amber-200 hover:border-amber-400'
                }`}
              >
                <Hourglass className="w-3.5 h-3.5" />
                <span>Pending Orders</span>
                <span className={`text-[10px] font-extrabold px-1.5 py-0.5 rounded-full ${
                  orderFilter === 'pending' ? 'bg-white/30 text-white' : 'bg-amber-500 text-white'
                }`}>
                  {pendingOrders.length}
                </span>
              </button>

              <button
                onClick={() => setOrderFilter('done')}
                className={`flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs transition-all border-2 ${
                  orderFilter === 'done'
                    ? 'bg-emerald-600 text-white border-emerald-600 shadow-md'
                    : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:border-emerald-400'
                }`}
              >
                <CheckCheck className="w-3.5 h-3.5" />
                <span>Done Orders</span>
                <span className={`text-[10px] font-extrabold px-1.5 py-0.5 rounded-full ${
                  orderFilter === 'done' ? 'bg-white/30 text-white' : 'bg-emerald-600 text-white'
                }`}>
                  {doneOrders.length}
                </span>
              </button>
            </div>

            {displayedOrders.length === 0 ? (
              <div className="text-center py-10 text-neutral-400">
                <ShoppingBag className="w-10 h-10 mx-auto mb-3 opacity-30" />
                <p className="font-semibold">
                  {orderFilter === 'pending' ? 'No pending orders — all clear! 🎉' : 'No completed orders yet.'}
                </p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-xs text-left">
                  <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                    <tr>
                      <th className="p-3">Order Ref</th>
                      <th className="p-3">Customer</th>
                      <th className="p-3">Driver Phone</th>
                      <th className="p-3">Date</th>
                      <th className="p-3">Items</th>
                      <th className="p-3">Total</th>
                      <th className="p-3">Payment</th>
                      <th className="p-3">Token</th>
                      <th className="p-3">Status</th>
                      {orderFilter === 'pending' && <th className="p-3 text-right">Action</th>}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#f0ece8]">
                    {displayedOrders.map((o) => (
                      <tr
                        key={o.id}
                        className={`transition-colors ${
                          orderFilter === 'pending'
                            ? 'hover:bg-amber-50/60 bg-amber-50/20'
                            : 'hover:bg-emerald-50/40'
                        }`}
                      >
                        <td className="p-3 font-mono font-bold text-[#722f37]">{o.orderNumber}</td>
                        <td className="p-3">
                          <strong className="text-[#2c1a1a] block">{o.customerName}</strong>
                          <span className="text-neutral-500">{o.email}</span>
                        </td>
                        <td className="p-3">
                          {o.phone ? (
                            <a
                              href={`tel:${o.phone}`}
                              className="flex items-center gap-1 text-[#722f37] font-semibold hover:underline"
                            >
                              <Phone className="w-3 h-3" />
                              {o.phone}
                            </a>
                          ) : (
                            <span className="text-neutral-400">—</span>
                          )}
                        </td>
                        <td className="p-3 text-neutral-500">{o.date}</td>
                        <td className="p-3 font-semibold">{o.itemsCount} bottle(s)</td>
                        <td className="p-3 font-bold text-[#1a6b3c] text-sm">E{o.total.toFixed(2)}</td>
                        <td className="p-3 uppercase text-[10px] font-bold text-neutral-600">
                          {o.paymentMethod.replace(/_/g, ' ')}
                        </td>
                        <td className="p-3">
                          <span className="font-mono font-bold text-[#722f37] bg-[#722f37]/10 px-2 py-0.5 rounded-lg text-[11px] flex items-center gap-1 w-max">
                            <QrCode className="w-3 h-3" />
                            {generateOrderToken(o.orderNumber)}
                          </span>
                        </td>
                        <td className="p-3">
                          <span
                            className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase ${
                              o.status === 'completed'
                                ? 'bg-emerald-100 text-emerald-800'
                                : o.status === 'processing'
                                ? 'bg-amber-100 text-amber-800'
                                : 'bg-blue-100 text-blue-800'
                            }`}
                          >
                            {o.status}
                          </span>
                        </td>
                        {orderFilter === 'pending' && (
                          <td className="p-3 text-right">
                            <button
                              onClick={() => markOrderDone(o.id)}
                              className="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-xl font-bold text-xs transition-colors inline-flex items-center gap-1"
                              title="Mark this order as delivered & done"
                            >
                              <CheckCheck className="w-3.5 h-3.5" />
                              <span>Mark as Done</span>
                            </button>
                          </td>
                        )}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        )}

        {/* ==================== TAB 5: REAL-TIME PORTAL LOGINS & VIP LEADS ==================== */}
        {activeTab === 'logins' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a] flex items-center gap-2">
                  <Mail className="w-5 h-5 text-emerald-600" />
                  <span>Real-Time Portal Access & VIP Registered Emails</span>
                </h3>
                <p className="text-xs text-neutral-500">
                  Live email capture stream when users log into portals or register for VIP Member Lounge access
                </p>
              </div>

              <button
                onClick={fetchRealtimeData}
                className="btn-wine text-xs px-3.5 py-1.5 flex items-center gap-1.5 shadow"
              >
                <RefreshCw className="w-3.5 h-3.5" />
                <span>Sync Live Logs</span>
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <span className="text-[10px] font-bold uppercase text-neutral-500 block">Total Portal Sign-ins</span>
                <span className="text-xl font-extrabold text-[#722f37] block mt-1">{loginLogs.length} Logins</span>
                <span className="text-[10px] text-emerald-700 font-semibold">Real-time authentication logging</span>
              </div>

              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <span className="text-[10px] font-bold uppercase text-neutral-500 block">Registered VIP Member Emails</span>
                <span className="text-xl font-extrabold text-[#c9a03d] block mt-1">{subscribersList.length} Emails</span>
                <span className="text-[10px] text-neutral-600">Stored in database registry</span>
              </div>

              <div className="bg-[#faf6f0] p-4 rounded-2xl border border-[#e8e0d8]">
                <span className="text-[10px] font-bold uppercase text-neutral-500 block">Lead Conversion Rate</span>
                <span className="text-xl font-extrabold text-[#1a6b3c] block mt-1">
                  {loginLogs.length > 0 ? ((activeSubs.length / loginLogs.length) * 100).toFixed(1) : 100}%
                </span>
                <span className="text-[10px] text-emerald-700 font-semibold">Active VIP subscriber ratio</span>
              </div>
            </div>

            {/* Real-time Email Logs Table */}
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Registered User Email</th>
                    <th className="p-3">Portal Role</th>
                    <th className="p-3">Timestamp</th>
                    <th className="p-3">Access Location / IP</th>
                    <th className="p-3">Auth Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {loginLogs.map((log) => (
                    <tr key={log.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3 font-mono font-bold text-[#722f37] text-xs">
                        <div className="flex items-center gap-2">
                          <Mail className="w-3.5 h-3.5 text-neutral-400" />
                          <span>{log.email}</span>
                        </div>
                      </td>
                      <td className="p-3">
                        <span
                          className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase ${
                            log.role === 'admin'
                              ? 'bg-purple-100 text-purple-800'
                              : log.role === 'staff'
                              ? 'bg-blue-100 text-blue-800'
                              : 'bg-[#c9a03d]/20 text-[#722f37]'
                          }`}
                        >
                          {log.role}
                        </span>
                      </td>
                      <td className="p-3 text-neutral-500 flex items-center gap-1.5">
                        <Clock className="w-3 h-3 text-[#c9a03d]" />
                        <span>{log.timestamp}</span>
                      </td>
                      <td className="p-3 text-neutral-600">{log.ip || '127.0.0.1 (Eswatini)'}</td>
                      <td className="p-3">
                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase flex items-center gap-1 w-max">
                          <CheckCircle2 className="w-3 h-3" /> Success
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* ==================== TAB 6: STAFF ROLES ==================== */}
        {activeTab === 'staff' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Staff & Sommelier Accounts</h3>
                <p className="text-xs text-neutral-500">Manage administrator and manager role permissions</p>
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Staff Name</th>
                    <th className="p-3">Email Address</th>
                    <th className="p-3">Role</th>
                    <th className="p-3">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {staffList.map((s) => (
                    <tr key={s.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3 font-bold text-[#2c1a1a]">{s.name}</td>
                      <td className="p-3 font-mono text-[#722f37]">{s.email}</td>
                      <td className="p-3 font-semibold text-neutral-700">{s.role}</td>
                      <td className="p-3">
                        <span className="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                          {s.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
