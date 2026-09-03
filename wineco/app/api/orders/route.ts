import { NextResponse } from 'next/server';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';

export interface AdminOrder {
  id: string | number;
  orderNumber: string;
  customerName: string;
  email: string;
  phone: string;
  address?: string;
  city?: string;
  total: number;
  itemsCount: number;
  itemsDescription?: string;
  orderType: 'standard' | 'vip_box';
  status: 'pending' | 'processing' | 'completed' | 'cancelled';
  paymentMethod: string;
  date: string;
  notes?: string;
}

// In-memory orders store initialized with initial orders & VIP box deliveries
export let ordersStore: AdminOrder[] = [
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
    id: 16,
    orderNumber: 'ORD-20260724-8731',
    customerName: 'Lihle Mfundo Mbhamali',
    email: 'mfundombhamaly@gmail.com',
    phone: '+268 7612 3456',
    address: 'Tubungu Estate, House 8',
    city: 'Matsapha',
    total: 446.75,
    itemsCount: 1,
    itemsDescription: '1x Franschhoek Cellar Sauvignon Blanc',
    orderType: 'standard',
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
  {
    id: 6,
    orderNumber: 'ORD-20260705-8769',
    customerName: 'Mbhamaly Mfundo',
    email: 'mfundombhamaly@gmail.com',
    phone: '+268 7612 3456',
    address: 'Coates Valley',
    city: 'Manzini',
    total: 2987.7,
    itemsCount: 6,
    itemsDescription: 'Cellar Selection Case',
    orderType: 'standard',
    status: 'completed',
    paymentMethod: 'bank_transfer',
    date: '2026-07-05',
  },
];

export function addOrderToStore(order: AdminOrder) {
  ordersStore = [order, ...ordersStore];
}

export async function GET() {
  try {
    if (isSupabaseConfigured && supabase) {
      try {
        const { data, error } = await supabase
          .from('orders')
          .select('*')
          .order('created_at', { ascending: false });

        if (!error && data && data.length > 0) {
          const dbOrders: AdminOrder[] = data.map((d: any) => ({
            id: d.id,
            orderNumber: d.order_number || `ORD-${d.id}`,
            customerName: d.customer_name || 'Customer',
            email: d.customer_email || '',
            phone: d.customer_phone || '',
            address: d.customer_address || '',
            city: d.city || 'Eswatini',
            total: Number(d.total) || 0,
            itemsCount: Array.isArray(d.items) ? d.items.length : 1,
            itemsDescription: Array.isArray(d.items)
              ? d.items.map((i: any) => `${i.quantity || 1}x ${i.product_name || 'Item'}`).join(', ')
              : 'Wine selection',
            orderType: d.notes && d.notes.includes('VIP') ? 'vip_box' : 'standard',
            status: d.status || 'pending',
            paymentMethod: d.payment_method || 'EFT',
            date: d.created_at ? d.created_at.split('T')[0] : new Date().toISOString().split('T')[0],
            notes: d.notes || '',
          }));

          const dbOrderNumbers = new Set(dbOrders.map((o) => o.orderNumber));
          const memoryOnly = ordersStore.filter((o) => !dbOrderNumbers.has(o.orderNumber));
          return NextResponse.json({ success: true, orders: [...memoryOnly, ...dbOrders] });
        }
      } catch (dbErr) {
        console.warn('Supabase fetch orders error, falling back to memory:', dbErr);
      }
    }

    return NextResponse.json({ success: true, orders: ordersStore });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const newOrder: AdminOrder = {
      id: body.id || `ord_${Date.now()}`,
      orderNumber: body.orderNumber || `ORD-${Date.now().toString().slice(-6)}`,
      customerName: body.customerName || 'Customer',
      email: body.email || '',
      phone: body.phone || '',
      address: body.address || '',
      city: body.city || 'Eswatini',
      total: Number(body.total) || 0,
      itemsCount: Number(body.itemsCount) || 1,
      itemsDescription: body.itemsDescription || 'Wine selection',
      orderType: body.orderType || 'standard',
      status: body.status || 'pending',
      paymentMethod: body.paymentMethod || 'EFT',
      date: body.date || new Date().toISOString().split('T')[0],
      notes: body.notes || '',
    };

    addOrderToStore(newOrder);

    if (isSupabaseConfigured && supabase) {
      try {
        await supabase.from('orders').insert({
          order_number: newOrder.orderNumber,
          customer_name: newOrder.customerName,
          customer_email: newOrder.email,
          customer_phone: newOrder.phone,
          customer_address: newOrder.address,
          city: newOrder.city,
          total: newOrder.total,
          status: newOrder.status,
          payment_method: newOrder.paymentMethod,
          notes: newOrder.notes,
        });
      } catch (dbErr) {
        console.warn('Supabase order insert error (non-fatal):', dbErr);
      }
    }

    return NextResponse.json({ success: true, order: newOrder });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function PATCH(request: Request) {
  try {
    const body = await request.json();
    const { id, orderNumber, status } = body;

    if (!status) {
      return NextResponse.json({ success: false, error: 'Status is required' }, { status: 400 });
    }

    ordersStore = ordersStore.map((o) => {
      if ((id && o.id === id) || (orderNumber && o.orderNumber === orderNumber)) {
        return { ...o, status };
      }
      return o;
    });

    if (isSupabaseConfigured && supabase) {
      try {
        if (id) {
          await supabase.from('orders').update({ status }).eq('id', id);
        } else if (orderNumber) {
          await supabase.from('orders').update({ status }).eq('order_number', orderNumber);
        }
      } catch (dbErr) {
        console.warn('Supabase update order status error:', dbErr);
      }
    }

    return NextResponse.json({
      success: true,
      message: `Order status updated to ${status}`,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
