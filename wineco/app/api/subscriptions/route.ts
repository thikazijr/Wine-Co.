import { NextResponse } from 'next/server';
import nodemailer from 'nodemailer';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { planId, planName, price, fullName, email, phone, address, city, paymentMethod } = body;

    // Record in Supabase if configured
    if (isSupabaseConfigured && supabase) {
      try {
        await supabase.from('subscription_requests').insert({
          plan_id: planId,
          plan_name: planName,
          price: price,
          full_name: fullName,
          email: email,
          phone: phone,
          address: address,
          city: city,
          payment_method: paymentMethod || 'stripe',
          payment_status: 'paid',
          status: 'approved',
        });
      } catch (dbErr) {
        console.error('Supabase subscription insert error:', dbErr);
      }
    }

    // Send confirmation email
    const smtpUser = process.env.SMTP_USERNAME || 'phumza19952010@gmail.com';
    const smtpPass = process.env.SMTP_PASSWORD || '';

    if (smtpPass) {
      try {
        const transporter = nodemailer.createTransport({
          service: 'gmail',
          auth: { user: smtpUser, pass: smtpPass },
        });

        await transporter.sendMail({
          from: `"Wine & Co. Wine Club" <${smtpUser}>`,
          to: email,
          subject: `👑 Welcome to the ${planName} - Wine & Co. Eswatini`,
          html: `
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #c9a03d; border-radius: 12px;">
              <h2 style="color: #722f37; margin-bottom: 5px;">Welcome to the Wine & Co. Club</h2>
              <p>Dear ${fullName},</p>
              <p>Congratulations on joining the <strong>${planName}</strong> (E${Number(price).toFixed(2)}/month)!</p>
              <p>Your first monthly surprise wine box is currently being prepared and cellared for doorstep dispatch to <strong>${address}, ${city}</strong>.</p>
              <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;" />
              <p style="font-size: 11px; color: #888;">© ${new Date().getFullYear()} Wine & Co. Eswatini</p>
            </div>
          `,
        });
      } catch (emailErr) {
        console.error('Email error:', emailErr);
      }
    }

    return NextResponse.json({
      success: true,
      message: `Successfully subscribed to ${planName}!`,
    });
  } catch (error: any) {
    return NextResponse.json(
      { success: false, error: error.message || 'Subscription failed' },
      { status: 500 }
    );
  }
}
