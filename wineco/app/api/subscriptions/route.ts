import { NextResponse } from 'next/server';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';
import { Subscriber } from '@/lib/types';
import { sendEmail } from '@/lib/email';
import { addOrderToStore, AdminOrder } from '@/app/api/orders/route';

// In-memory fallback subscriber store for instant state reactivity
let subscribersStore: Subscriber[] = [
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
];

export async function GET() {
  try {
    if (isSupabaseConfigured && supabase) {
      try {
        const { data, error } = await supabase
          .from('subscription_requests')
          .select('*')
          .order('created_at', { ascending: false });

        if (!error && data && data.length > 0) {
          const dbSubs: Subscriber[] = data.map((d: any) => ({
            id: d.id,
            fullName: d.full_name || 'Subscriber',
            email: d.email,
            phone: d.phone || '',
            planName: d.plan_name || 'Wine Club Box',
            price: Number(d.price) || 499,
            status: d.status === 'approved' || d.status === 'active' ? 'active' : 'cancelled',
            paymentMethod: d.payment_method || 'EFT',
            startDate: d.created_at ? d.created_at.split('T')[0] : '2026-08-20',
            city: d.city || 'Eswatini',
          }));

          return NextResponse.json({ success: true, subscribers: dbSubs });
        }
      } catch (e) {
        console.warn('Supabase fetch failed, using memory store:', e);
      }
    }

    return NextResponse.json({ success: true, subscribers: subscribersStore });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { planId, planName, price, fullName, email, phone, address, city, paymentMethod } = body;

    if (!email) {
      return NextResponse.json({ success: false, error: 'Email is required' }, { status: 400 });
    }

    const newSub: Subscriber = {
      id: `sub_${Date.now()}`,
      fullName: fullName || 'VIP Member',
      email: email.toLowerCase().trim(),
      phone: phone || '',
      planName: planName || 'VIP Access Tier',
      price: Number(price) || 499,
      status: 'active',
      paymentMethod: paymentMethod || 'MTN MoMo',
      startDate: new Date().toISOString().split('T')[0],
      address: address || '',
      city: city || 'Eswatini',
    };

    // Store subscriber in memory
    subscribersStore = [newSub, ...subscribersStore];

    // Automatically create a Pending Delivery Order for the VIP Box
    const bottlesCount = planName?.includes('Elegance') ? 2 : planName?.includes('Voyager') ? 4 : planName?.includes('Grand Reserve') ? 12 : 6;
    const vipOrderNumber = `ORD-VIP-${Date.now().toString().slice(-6)}`;
    const vipDeliveryOrder: AdminOrder = {
      id: `ord_vip_${Date.now()}`,
      orderNumber: vipOrderNumber,
      customerName: newSub.fullName,
      email: newSub.email,
      phone: newSub.phone,
      address: address || '',
      city: city || 'Eswatini',
      total: newSub.price,
      itemsCount: bottlesCount,
      itemsDescription: `${planName || 'VIP Club Box'} (${bottlesCount} bottles) - Monthly Delivery`,
      orderType: 'vip_box',
      status: 'pending',
      paymentMethod: paymentMethod || 'MTN MoMo',
      date: new Date().toISOString().split('T')[0],
      notes: `VIP Club Box Delivery to ${address || 'customer address'}, ${city || 'Eswatini'}. Driver Contact: ${newSub.phone}`,
    };

    addOrderToStore(vipDeliveryOrder);

    // Record in Supabase if configured
    if (isSupabaseConfigured && supabase) {
      try {
        await supabase.from('subscription_requests').insert({
          plan_id: planId || 1,
          plan_name: planName || 'VIP Member Access',
          price: newSub.price,
          full_name: newSub.fullName,
          email: newSub.email,
          phone: newSub.phone,
          address: address || '',
          city: city || '',
          payment_method: paymentMethod || 'momo',
          payment_status: 'paid',
          status: 'active',
        });

        // Also insert pending delivery order into orders table
        await supabase.from('orders').insert({
          order_number: vipDeliveryOrder.orderNumber,
          customer_name: vipDeliveryOrder.customerName,
          customer_email: vipDeliveryOrder.email,
          customer_phone: vipDeliveryOrder.phone,
          customer_address: vipDeliveryOrder.address,
          city: vipDeliveryOrder.city,
          total: vipDeliveryOrder.total,
          status: 'pending',
          payment_method: vipDeliveryOrder.paymentMethod,
          notes: vipDeliveryOrder.notes,
        });
      } catch (dbErr) {
        console.error('Supabase subscription & order insert error:', dbErr);
      }
    }

    // Send confirmation email via Resend (with SMTP fallback)
    const today = new Date().toLocaleDateString('en-ZA', { day: 'numeric', month: 'long', year: 'numeric' });

    try {
      await sendEmail({
        to: email,
        subject: `👑 Welcome to the Wine & Co. VIP Club — ${planName}`,
        html: `
          <!DOCTYPE html>
          <html lang="en">
          <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
          <body style="margin:0;padding:0;background:#f5efe8;font-family:'Georgia',serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5efe8;padding:30px 0;">
              <tr>
                <td align="center">
                  <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:16px;overflow:hidden;border:2px solid #c9a03d;">
                    <!-- Header -->
                    <tr>
                      <td style="background:#1a0f0f;padding:30px 40px;text-align:center;">
                        <div style="border:2px solid #c9a03d;border-radius:12px;display:inline-block;padding:10px 20px;margin-bottom:10px;">
                          <span style="color:#c9a03d;font-size:22px;font-weight:bold;letter-spacing:2px;">THE WINE CO.</span>
                        </div>
                        <p style="color:#c9a03d;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0;">Est. Eswatini · Reserve Wine Club</p>
                      </td>
                    </tr>
                    <!-- Gold banner -->
                    <tr>
                      <td style="background:linear-gradient(135deg,#c9a03d,#e8c96a);padding:14px 40px;text-align:center;">
                        <span style="color:#1a0f0f;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">🥂 VIP Membership Confirmed</span>
                      </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                      <td style="padding:36px 40px;color:#2c1a1a;">
                        <h2 style="color:#722f37;font-size:24px;margin:0 0 8px 0;">Welcome, ${fullName}!</h2>
                        <p style="color:#4a2c2a;font-size:14px;line-height:1.7;margin:0 0 20px 0;">
                          Your membership to the <strong style="color:#722f37;">${planName}</strong> has been successfully authorized. You are now a verified member of Eswatini's most exclusive curated wine experience.
                        </p>

                        <!-- Membership Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf6f0;border-radius:12px;border:1px solid #e8ddd0;margin-bottom:24px;">
                          <tr>
                            <td style="padding:20px 24px;">
                              <p style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:2px;color:#c9a03d;margin:0 0 12px 0;">Your Membership Summary</p>
                              <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;font-weight:bold;">Membership Tier</td>
                                  <td style="padding:6px 0;font-size:13px;color:#722f37;font-weight:bold;text-align:right;">${planName}</td>
                                </tr>
                                <tr>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;font-weight:bold;">Monthly Dues</td>
                                  <td style="padding:6px 0;font-size:13px;color:#1a6b3c;font-weight:bold;text-align:right;">E${Number(price).toFixed(2)} / month</td>
                                </tr>
                                <tr>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;font-weight:bold;">Member ID</td>
                                  <td style="padding:6px 0;font-size:13px;color:#722f37;font-weight:bold;text-align:right;font-family:monospace;">VIP-${Date.now().toString().slice(-6)}</td>
                                </tr>
                                <tr>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;font-weight:bold;">Member Since</td>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;text-align:right;">${today}</td>
                                </tr>
                                <tr>
                                  <td style="padding:6px 0;font-size:13px;color:#4a2c2a;font-weight:bold;">Status</td>
                                  <td style="padding:6px 0;text-align:right;"><span style="background:#d4f7e8;color:#1a6b3c;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:bold;">✓ ACTIVE</span></td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>

                        <!-- What to Expect -->
                        <p style="font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:1.5px;color:#722f37;margin:0 0 10px 0;">What to Expect Next</p>
                        <ul style="font-size:13px;color:#4a2c2a;line-height:1.9;padding-left:18px;margin:0 0 24px 0;">
                          <li>Our sommelier will curate your first monthly wine selection from our Reserve Cellar.</li>
                          <li>You will receive a delivery notification via WhatsApp when your box is dispatched.</li>
                          <li>Access your Member Lounge at <strong>wineco.sz</strong> to track your deliveries and manage your subscription.</li>
                        </ul>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                            <td align="center" style="padding:10px 0 24px 0;">
                              <a href="https://wineco.sz" style="background:#722f37;color:#fff;text-decoration:none;padding:13px 32px;border-radius:30px;font-size:13px;font-weight:bold;display:inline-block;letter-spacing:0.5px;">🍷 Visit Your Member Lounge</a>
                            </td>
                          </tr>
                        </table>

                        <p style="font-size:12px;color:#888;line-height:1.6;margin:0;">
                          Questions? Contact us:<br>
                          📧 <a href="mailto:winecoeswatini@yahoo.com" style="color:#722f37;">winecoeswatini@yahoo.com</a><br>
                          📱 WhatsApp: <a href="https://wa.me/26878381971" style="color:#722f37;">+268 7838 1971</a>
                        </p>
                      </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                      <td style="background:#1a0f0f;padding:16px 40px;text-align:center;">
                        <p style="color:#c9a03d;font-size:10px;letter-spacing:1px;margin:0 0 4px 0;text-transform:uppercase;">The Wine Co. Eswatini</p>
                        <p style="color:#888;font-size:10px;margin:0;">Mbabane · Manzini · Ezulwini · Nationwide Delivery</p>
                        <p style="color:#555;font-size:9px;margin:6px 0 0 0;">© ${new Date().getFullYear()} Wine &amp; Co. Eswatini — Sip Responsibly 🍷</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </body>
          </html>
        `,
      });
    } catch (emailErr) {
      console.error('Email dispatch error (non-fatal):', emailErr);
    }

    return NextResponse.json({
      success: true,
      message: `Successfully registered ${newSub.email} for VIP membership!`,
      subscriber: newSub,
    });
  } catch (error: any) {
    return NextResponse.json(
      { success: false, error: error.message || 'Subscription failed' },
      { status: 500 }
    );
  }
}

export async function PATCH(request: Request) {
  try {
    const body = await request.json();
    const { id, status } = body;

    if (!id || !status) {
      return NextResponse.json({ success: false, error: 'ID and Status required' }, { status: 400 });
    }

    subscribersStore = subscribersStore.map((s) => (s.id == id ? { ...s, status } : s));

    if (isSupabaseConfigured && supabase) {
      try {
        await supabase
          .from('subscription_requests')
          .update({ status: status })
          .eq('id', id);
      } catch (dbErr) {
        console.warn('Supabase update status failed:', dbErr);
      }
    }

    return NextResponse.json({
      success: true,
      message: `Subscription status updated to ${status}`,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
