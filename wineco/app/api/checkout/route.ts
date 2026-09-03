import { NextResponse } from 'next/server';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';
import { sendEmail } from '@/lib/email';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const {
      orderNumber,
      customerName,
      customerEmail,
      customerPhone,
      customerAddress,
      city,
      items,
      subtotal,
      deliveryFee,
      grandTotal,
      paymentMethod,
      notes,
    } = body;

    // 1. If Supabase is configured, record order in Supabase
    if (isSupabaseConfigured && supabase) {
      try {
        await supabase.from('orders').insert({
          order_number: orderNumber,
          customer_name: customerName,
          customer_email: customerEmail,
          customer_phone: customerPhone,
          customer_address: customerAddress,
          city: city || 'Eswatini',
          items: items,
          subtotal: subtotal,
          tax: 0,
          shipping: deliveryFee,
          total: grandTotal,
          status: 'pending',
          payment_method: paymentMethod,
          payment_status: paymentMethod === 'stripe' ? 'paid' : 'pending',
          notes: notes || '',
        });
      } catch (dbErr) {
        console.error('Supabase order insert error (non-fatal):', dbErr);
      }
    }

    // 2. Send email notification via Gmail SMTP if configured
    const smtpUser = process.env.SMTP_USERNAME || 'phumza19952010@gmail.com';
    // Email to Customer via Resend / SMTP
    try {
      await sendEmail({
        to: customerEmail,
        subject: `🍷 Order Confirmed - ${orderNumber} - Wine & Co. Eswatini`,
        html: `
          <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #c9a03d; border-radius: 12px; background: #fff;">
            <div style="background: #1a0f0f; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
              <h2 style="color: #c9a03d; margin: 0; font-family: serif; letter-spacing: 2px;">THE WINE CO.</h2>
              <p style="font-size: 11px; color: #fff; text-transform: uppercase; margin: 5px 0 0 0; letter-spacing: 1px;">Reserve Order Confirmation</p>
            </div>
            <p>Dear ${customerName},</p>
            <p>Thank you for choosing Wine & Co. Your order <strong>${orderNumber}</strong> has been received and is being prepared in our cellar.</p>
            
            <div style="background-color: #faf6f0; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e8ddd0;">
              <h4 style="margin-top: 0; color: #722f37;">Order Summary:</h4>
              <p style="margin: 4px 0;"><strong>Total Amount:</strong> E${Number(grandTotal).toFixed(2)}</p>
              <p style="margin: 4px 0;"><strong>Payment Method:</strong> ${paymentMethod}</p>
              <p style="margin: 4px 0;"><strong>Delivery Address:</strong> ${customerAddress}, ${city || 'Eswatini'}</p>
            </div>

            <p style="font-size: 12px; color: #888;">If you have any questions, WhatsApp our sommelier directly on +268 7838 1971 or email winecoeswatini@yahoo.com.</p>
            <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;" />
            <p style="font-size: 11px; text-align: center; color: #aaa;">© ${new Date().getFullYear()} Wine & Co. Eswatini • Sip Responsibly (18+)</p>
          </div>
        `,
      });

      // Email to Admin
      await sendEmail({
        to: process.env.ADMIN_EMAIL || 'winecoeswatini@yahoo.com',
        subject: `🚨 New Cellar Order: ${orderNumber} (E${Number(grandTotal).toFixed(2)})`,
        text: `New order received from ${customerName} (${customerPhone}) for E${Number(grandTotal).toFixed(2)}. Address: ${customerAddress}, ${city}.`,
        html: `
          <div style="font-family: Arial, sans-serif; padding: 15px;">
            <h3 style="color: #722f37;">New Cellar Order Placed: ${orderNumber}</h3>
            <p><strong>Customer:</strong> ${customerName} (${customerPhone})</p>
            <p><strong>Email:</strong> ${customerEmail}</p>
            <p><strong>Total:</strong> E${Number(grandTotal).toFixed(2)}</p>
            <p><strong>Address:</strong> ${customerAddress}, ${city}</p>
            <p><strong>Payment:</strong> ${paymentMethod}</p>
          </div>
        `,
      });
    } catch (emailErr) {
      console.error('Email dispatch error (non-fatal):', emailErr);
    }

    return NextResponse.json({
      success: true,
      orderNumber,
      message: 'Order placed successfully',
    });
  } catch (error: any) {
    console.error('Checkout API error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Failed to place order' },
      { status: 500 }
    );
  }
}
