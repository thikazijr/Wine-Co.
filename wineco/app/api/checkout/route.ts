import { NextResponse } from 'next/server';
import nodemailer from 'nodemailer';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';

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
    const smtpPass = process.env.SMTP_PASSWORD || '';

    if (smtpPass) {
      try {
        const transporter = nodemailer.createTransport({
          service: 'gmail',
          auth: {
            user: smtpUser,
            pass: smtpPass,
          },
        });

        // Email to Customer
        await transporter.sendMail({
          from: `"Wine & Co. Eswatini" <${smtpUser}>`,
          to: customerEmail,
          subject: `🍷 Order Confirmed - ${orderNumber} - Wine & Co. Eswatini`,
          html: `
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #c9a03d; border-radius: 12px;">
              <h2 style="color: #722f37; margin-bottom: 5px;">Wine & Co. Eswatini</h2>
              <p style="font-size: 12px; color: #c9a03d; text-transform: uppercase; margin-top: 0;">Order Confirmation</p>
              <p>Dear ${customerName},</p>
              <p>Thank you for choosing Wine & Co. Your order <strong>${orderNumber}</strong> has been received and is being prepared in our cellar.</p>
              
              <div style="background-color: #faf6f0; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #722f37;">Order Summary:</h4>
                <p><strong>Total Amount:</strong> E${Number(grandTotal).toFixed(2)}</p>
                <p><strong>Payment Method:</strong> ${paymentMethod}</p>
                <p><strong>Delivery Address:</strong> ${customerAddress}, ${city || 'Eswatini'}</p>
              </div>

              <p style="font-size: 12px; color: #888;">If you have any questions, reply directly to this email or WhatsApp our sommelier on +268 7838 1971.</p>
              <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;" />
              <p style="font-size: 11px; text-align: center; color: #aaa;">© ${new Date().getFullYear()} Wine & Co. Eswatini • Sip Responsibly (18+)</p>
            </div>
          `,
        });

        // Email to Admin
        await transporter.sendMail({
          from: `"Wine & Co. Store" <${smtpUser}>`,
          to: process.env.ADMIN_EMAIL || 'siphiwosethuthikazi@gmail.com',
          subject: `🚨 New Cellar Order: ${orderNumber} (E${Number(grandTotal).toFixed(2)})`,
          text: `New order received from ${customerName} (${customerPhone}) for E${Number(grandTotal).toFixed(2)}. Address: ${customerAddress}, ${city}.`,
        });
      } catch (emailErr) {
        console.error('Email dispatch error (non-fatal):', emailErr);
      }
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
