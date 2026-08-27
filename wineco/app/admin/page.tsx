'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import {
  LayoutDashboard,
  Wine as WineIcon,
  ShoppingBag,
  Users,
  Crown,
  BookOpen,
  Plus,
  Trash2,
  Edit,
  CheckCircle,
  Clock,
  XCircle,
  LogOut,
  ArrowLeft,
  DollarSign,
  TrendingUp,
  Package,
} from 'lucide-react';
import { INITIAL_WINES, INITIAL_SUBSCRIPTIONS } from '@/lib/mock-data';
import { Wine } from '@/lib/types';

export default function AdminPage() {
  const [activeTab, setActiveTab] = useState<'overview' | 'wines' | 'orders' | 'subscriptions' | 'staff'>('overview');
  const [winesList, setWinesList] = useState<Wine[]>(INITIAL_WINES);
  const [isAddWineOpen, setIsAddWineOpen] = useState(false);
  const [newWine, setNewWine] = useState<Partial<Wine>>({
    name: '',
    variety: 'Cabernet Sauvignon',
    origin: 'South Africa',
    price: 350,
    stock_quantity: 12,
    vintage: 2021,
    description: '',
    image_url: '/wines/kanonkop.jpg',
  });

  const [ordersList, setOrdersList] = useState([
    {
      id: 17,
      orderNumber: 'ORD-20260821-5155',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      total: 1145.4,
      status: 'pending',
      paymentMethod: 'cash',
      date: '2026-08-21',
    },
    {
      id: 16,
      orderNumber: 'ORD-20260724-8731',
      customerName: 'Lihle Mfundo Mbhamali',
      email: 'mfundombhamaly@gmail.com',
      total: 446.75,
      status: 'pending',
      paymentMethod: 'bank_transfer',
      date: '2026-07-24',
    },
    {
      id: 13,
      orderNumber: 'ORD-20260722-3587',
      customerName: 'Phumelele Dlamini',
      email: 'phumza19952010@gmail.com',
      total: 363.95,
      status: 'processing',
      paymentMethod: 'cash',
      date: '2026-07-22',
    },
    {
      id: 6,
      orderNumber: 'ORD-20260705-8769',
      customerName: 'Mbhamaly Mfundo',
      email: 'mfundombhamaly@gmail.com',
      total: 2987.7,
      status: 'completed',
      paymentMethod: 'bank_transfer',
      date: '2026-07-05',
    },
  ]);

  const [staffList, setStaffList] = useState([
    { id: 1, name: 'Siphiwo Sethu Thikazi', email: 'siphiwosethuthikazi@gmail.com', role: 'Super Admin', status: 'Active' },
    { id: 2, name: 'Administrator', email: 'admin@wineco.co.sz', role: 'Admin', status: 'Active' },
    { id: 3, name: 'Phumelele Dlamini', email: 'phumelele@wineco.co.sz', role: 'Manager', status: 'Active' },
    { id: 4, name: 'Lihle Mbhamali', email: 'lihle@wineco.co.sz', role: 'Manager', status: 'Active' },
  ]);

  const handleAddWine = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newWine.name) return;

    const added: Wine = {
      id: winesList.length + 1,
      name: newWine.name || '',
      variety: newWine.variety || 'Red Blend',
      origin: newWine.origin || 'South Africa',
      price: Number(newWine.price) || 0,
      stock_quantity: Number(newWine.stock_quantity) || 0,
      vintage: Number(newWine.vintage) || 2021,
      description: newWine.description || '',
      featured: false,
      in_stock: true,
      image_url: newWine.image_url || '/wines/kanonkop.jpg',
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
      image_url: '/wines/kanonkop.jpg',
    });
  };

  const handleUpdateOrderStatus = (orderId: number, status: string) => {
    setOrdersList((prev) =>
      prev.map((o) => (o.id === orderId ? { ...o, status } : o))
    );
  };

  const handleDeleteWine = (id: number) => {
    if (confirm('Delete this wine from inventory?')) {
      setWinesList((prev) => prev.filter((w) => w.id !== id));
    }
  };

  const totalRevenue = ordersList.reduce((sum, o) => sum + o.total, 0);

  return (
    <div className="min-h-screen bg-[#f5efe8] flex flex-col">
      {/* Top Admin Header */}
      <header className="bg-[#1a0f0f] text-white px-6 py-4 border-b-2 border-[#c9a03d] flex items-center justify-between shadow-lg">
        <div className="flex items-center gap-4">
          <Link href="/" className="flex items-center gap-2 group">
            <div className="w-9 h-9 bg-[#722f37] rounded-lg p-1 border border-[#c9a03d] flex items-center justify-center">
              <Image src="/wines/logo.jpg" alt="Logo" width={32} height={32} className="rounded" />
            </div>
            <div>
              <span className="font-serif font-bold text-lg text-white block">Wine & Co.</span>
              <span className="text-[9px] uppercase font-bold text-[#c9a03d] tracking-widest block -mt-1">
                Admin Portal
              </span>
            </div>
          </Link>
          <span className="text-xs bg-[#c9a03d]/20 text-[#c9a03d] border border-[#c9a03d]/40 px-2.5 py-0.5 rounded-full font-semibold">
            v2.0 Next.js
          </span>
        </div>

        <div className="flex items-center gap-4">
          <div className="text-right hidden sm:block text-xs">
            <span className="text-white/80 block font-semibold">Siphiwo Sethu Thikazi</span>
            <span className="text-[#c9a03d] text-[11px]">Super Admin</span>
          </div>
          <Link
            href="/"
            className="flex items-center gap-1.5 text-xs bg-white/10 hover:bg-white/20 text-white px-3.5 py-2 rounded-xl transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Customer View</span>
          </Link>
        </div>
      </header>

      {/* Main Admin Dashboard */}
      <div className="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        {/* Navigation Tabs */}
        <div className="flex flex-wrap gap-2 bg-white p-2 rounded-2xl border border-[#e8e0d8] shadow-sm">
          <button
            onClick={() => setActiveTab('overview')}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
              activeTab === 'overview'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <LayoutDashboard className="w-4 h-4" />
            <span>Dashboard Overview</span>
          </button>

          <button
            onClick={() => setActiveTab('wines')}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
              activeTab === 'wines'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <WineIcon className="w-4 h-4" />
            <span>Wine Inventory ({winesList.length})</span>
          </button>

          <button
            onClick={() => setActiveTab('orders')}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
              activeTab === 'orders'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <ShoppingBag className="w-4 h-4" />
            <span>Orders ({ordersList.length})</span>
          </button>

          <button
            onClick={() => setActiveTab('subscriptions')}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
              activeTab === 'subscriptions'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <Crown className="w-4 h-4" />
            <span>Wine Club Members</span>
          </button>

          <button
            onClick={() => setActiveTab('staff')}
            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
              activeTab === 'staff'
                ? 'bg-[#722f37] text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            }`}
          >
            <Users className="w-4 h-4" />
            <span>Staff Accounts</span>
          </button>
        </div>

        {/* Tab 1: Overview */}
        {activeTab === 'overview' && (
          <div className="space-y-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="text-xs font-bold uppercase tracking-wider text-neutral-500">
                    Total Revenue
                  </span>
                  <DollarSign className="w-5 h-5 text-[#1a6b3c]" />
                </div>
                <span className="text-2xl font-bold text-[#1a6b3c]">
                  E{totalRevenue.toFixed(2)}
                </span>
                <p className="text-[11px] text-emerald-700 flex items-center gap-1 font-semibold">
                  <TrendingUp className="w-3.5 h-3.5" /> +18.4% this month
                </p>
              </div>

              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="text-xs font-bold uppercase tracking-wider text-neutral-500">
                    Active Orders
                  </span>
                  <ShoppingBag className="w-5 h-5 text-[#722f37]" />
                </div>
                <span className="text-2xl font-bold text-[#2c1a1a]">
                  {ordersList.length} Orders
                </span>
                <p className="text-[11px] text-neutral-500">2 awaiting courier dispatch</p>
              </div>

              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="text-xs font-bold uppercase tracking-wider text-neutral-500">
                    Wine Inventory
                  </span>
                  <WineIcon className="w-5 h-5 text-[#c9a03d]" />
                </div>
                <span className="text-2xl font-bold text-[#2c1a1a]">
                  {winesList.reduce((sum, w) => sum + w.stock_quantity, 0)} Bottles
                </span>
                <p className="text-[11px] text-amber-700 font-semibold">Across 12 estates</p>
              </div>

              <div className="bg-white p-6 rounded-3xl border border-[#e8e0d8] shadow-sm space-y-2">
                <div className="flex justify-between items-center text-[#722f37]">
                  <span className="text-xs font-bold uppercase tracking-wider text-neutral-500">
                    Club Members
                  </span>
                  <Crown className="w-5 h-5 text-[#c9a03d]" />
                </div>
                <span className="text-2xl font-bold text-[#2c1a1a]">14 Active</span>
                <p className="text-[11px] text-emerald-700 font-semibold">100% renewal rate</p>
              </div>
            </div>

            {/* Recent Orders Overview */}
            <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-4">
              <h3 className="text-base font-serif font-bold text-[#2c1a1a]">Recent Cellar Orders</h3>
              <div className="overflow-x-auto">
                <table className="w-full text-xs text-left">
                  <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                    <tr>
                      <th className="p-3">Order Ref</th>
                      <th className="p-3">Customer</th>
                      <th className="p-3">Date</th>
                      <th className="p-3">Amount</th>
                      <th className="p-3">Payment</th>
                      <th className="p-3">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#f0ece8]">
                    {ordersList.slice(0, 4).map((o) => (
                      <tr key={o.id} className="hover:bg-[#faf6f0]">
                        <td className="p-3 font-mono font-bold text-[#722f37]">{o.orderNumber}</td>
                        <td className="p-3 font-semibold text-[#2c1a1a]">{o.customerName}</td>
                        <td className="p-3 text-neutral-500">{o.date}</td>
                        <td className="p-3 font-bold text-[#1a6b3c]">E{o.total.toFixed(2)}</td>
                        <td className="p-3 uppercase text-[10px] font-bold text-neutral-600">
                          {o.paymentMethod.replace(/_/g, ' ')}
                        </td>
                        <td className="p-3">
                          <span
                            className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase ${
                              o.status === 'completed'
                                ? 'bg-emerald-100 text-emerald-800'
                                : o.status === 'processing'
                                ? 'bg-blue-100 text-blue-800'
                                : 'bg-amber-100 text-amber-800'
                            }`}
                          >
                            {o.status}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* Tab 2: Wine Inventory CRUD */}
        {activeTab === 'wines' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div className="flex items-center justify-between border-b border-[#e8e0d8] pb-4">
              <div>
                <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Wine Inventory</h3>
                <p className="text-xs text-neutral-500">Manage estate bottles, vintages, pricing, and stock levels</p>
              </div>

              <button
                onClick={() => setIsAddWineOpen(true)}
                className="btn-wine text-xs px-4 py-2.5 shadow-md flex items-center gap-1.5"
              >
                <Plus className="w-4 h-4" />
                <span>Add New Wine</span>
              </button>
            </div>

            {/* Add Wine Modal */}
            {isAddWineOpen && (
              <form onSubmit={handleAddWine} className="p-6 rounded-2xl bg-[#faf6f0] border border-[#c9a03d] space-y-4 text-xs">
                <h4 className="font-bold text-[#722f37] text-sm">Add New Wine to Cellar</h4>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block font-semibold mb-1">Wine Name</label>
                    <input
                      type="text"
                      required
                      value={newWine.name}
                      onChange={(e) => setNewWine({ ...newWine, name: e.target.value })}
                      placeholder="e.g. Tenuta San Guido Sassicaia"
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                  <div>
                    <label className="block font-semibold mb-1">Variety</label>
                    <input
                      type="text"
                      required
                      value={newWine.variety}
                      onChange={(e) => setNewWine({ ...newWine, variety: e.target.value })}
                      placeholder="e.g. Cabernet Sauvignon"
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                  <div>
                    <label className="block font-semibold mb-1">Origin / Country</label>
                    <input
                      type="text"
                      required
                      value={newWine.origin}
                      onChange={(e) => setNewWine({ ...newWine, origin: e.target.value })}
                      placeholder="e.g. Bolgheri, Italy"
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block font-semibold mb-1">Price (E / SZL)</label>
                    <input
                      type="number"
                      required
                      value={newWine.price}
                      onChange={(e) => setNewWine({ ...newWine, price: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                  <div>
                    <label className="block font-semibold mb-1">Initial Stock (Bottles)</label>
                    <input
                      type="number"
                      required
                      value={newWine.stock_quantity}
                      onChange={(e) => setNewWine({ ...newWine, stock_quantity: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                  <div>
                    <label className="block font-semibold mb-1">Vintage Year</label>
                    <input
                      type="number"
                      value={newWine.vintage}
                      onChange={(e) => setNewWine({ ...newWine, vintage: Number(e.target.value) })}
                      className="w-full p-2.5 bg-white border border-[#d8d0c8] rounded-lg"
                    />
                  </div>
                </div>

                <div className="flex justify-end gap-2 pt-2">
                  <button
                    type="button"
                    onClick={() => setIsAddWineOpen(false)}
                    className="px-4 py-2 rounded-lg bg-neutral-200 text-neutral-700"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="btn-wine text-xs px-6 py-2">
                    Save to Cellar
                  </button>
                </div>
              </form>
            )}

            {/* Inventory Table */}
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Wine</th>
                    <th className="p-3">Variety</th>
                    <th className="p-3">Origin</th>
                    <th className="p-3">Vintage</th>
                    <th className="p-3">Price</th>
                    <th className="p-3">Stock</th>
                    <th className="p-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {winesList.map((w) => (
                    <tr key={w.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3 flex items-center gap-3">
                        <Image
                          src={w.image_url}
                          alt={w.name}
                          width={28}
                          height={36}
                          className="object-contain rounded"
                        />
                        <span className="font-bold text-[#2c1a1a]">{w.name}</span>
                      </td>
                      <td className="p-3 text-neutral-600">{w.variety}</td>
                      <td className="p-3 text-neutral-500">{w.origin}</td>
                      <td className="p-3 font-semibold">{w.vintage}</td>
                      <td className="p-3 font-bold text-[#1a6b3c]">E{w.price.toFixed(2)}</td>
                      <td className="p-3">
                        <span
                          className={`px-2 py-0.5 rounded font-bold ${
                            w.stock_quantity <= 5
                              ? 'bg-red-100 text-red-700'
                              : 'bg-emerald-100 text-emerald-700'
                          }`}
                        >
                          {w.stock_quantity} bottles
                        </span>
                      </td>
                      <td className="p-3 text-right">
                        <button
                          onClick={() => handleDeleteWine(w.id)}
                          className="p-1.5 text-neutral-400 hover:text-red-600 transition-colors"
                          title="Delete Wine"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Tab 3: Orders Manager */}
        {activeTab === 'orders' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div>
              <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Customer Orders</h3>
              <p className="text-xs text-neutral-500">Update fulfilment status and assign receipt tracking numbers</p>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-[#faf6f0] text-neutral-600 border-b border-[#e8e0d8]">
                  <tr>
                    <th className="p-3">Order Ref</th>
                    <th className="p-3">Customer</th>
                    <th className="p-3">Amount</th>
                    <th className="p-3">Payment</th>
                    <th className="p-3">Status</th>
                    <th className="p-3">Update Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#f0ece8]">
                  {ordersList.map((o) => (
                    <tr key={o.id} className="hover:bg-[#faf6f0]">
                      <td className="p-3 font-mono font-bold text-[#722f37]">{o.orderNumber}</td>
                      <td className="p-3">
                        <strong className="text-[#2c1a1a] block">{o.customerName}</strong>
                        <span className="text-neutral-500">{o.email}</span>
                      </td>
                      <td className="p-3 font-bold text-[#1a6b3c]">E{o.total.toFixed(2)}</td>
                      <td className="p-3 uppercase text-[10px] font-bold text-neutral-600">
                        {o.paymentMethod.replace(/_/g, ' ')}
                      </td>
                      <td className="p-3">
                        <span
                          className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase ${
                            o.status === 'completed'
                              ? 'bg-emerald-100 text-emerald-800'
                              : o.status === 'processing'
                              ? 'bg-blue-100 text-blue-800'
                              : o.status === 'cancelled'
                              ? 'bg-red-100 text-red-800'
                              : 'bg-amber-100 text-amber-800'
                          }`}
                        >
                          {o.status}
                        </span>
                      </td>
                      <td className="p-3">
                        <select
                          value={o.status}
                          onChange={(e) => handleUpdateOrderStatus(o.id, e.target.value)}
                          className="p-1.5 rounded-lg border border-[#d8d0c8] bg-white text-xs font-semibold"
                        >
                          <option value="pending">Pending</option>
                          <option value="processing">Processing</option>
                          <option value="shipped">Shipped</option>
                          <option value="completed">Completed</option>
                          <option value="cancelled">Cancelled</option>
                        </select>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Tab 4: Wine Club Members */}
        {activeTab === 'subscriptions' && (
          <div className="bg-white rounded-3xl p-6 border border-[#e8e0d8] shadow-sm space-y-6">
            <div>
              <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">Wine Club Members</h3>
              <p className="text-xs text-neutral-500">Active monthly surprise box subscribers</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="bg-[#faf6f0] p-5 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-sm font-bold text-[#722f37] block">Essential Elegance</strong>
                <span className="text-2xl font-bold text-[#1a6b3c]">6 Members</span>
                <p className="text-xs text-neutral-500 mt-1">E499 / month • 2 bottles</p>
              </div>

              <div className="bg-[#faf6f0] p-5 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-sm font-bold text-[#722f37] block">Vineyard Voyager</strong>
                <span className="text-2xl font-bold text-[#1a6b3c]">5 Members</span>
                <p className="text-xs text-neutral-500 mt-1">E999 / month • 4 bottles</p>
              </div>

              <div className="bg-[#faf6f0] p-5 rounded-2xl border border-[#e8e0d8]">
                <strong className="text-sm font-bold text-[#722f37] block">Luxury Reserve</strong>
                <span className="text-2xl font-bold text-[#1a6b3c]">3 Members</span>
                <p className="text-xs text-neutral-500 mt-1">E1,999 / month • 6 bottles</p>
              </div>
            </div>
          </div>
        )}

        {/* Tab 5: Staff Accounts */}
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
