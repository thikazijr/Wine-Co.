import { Resend } from 'resend';
import nodemailer from 'nodemailer';

interface SendEmailParams {
  to: string | string[];
  subject: string;
  html: string;
  text?: string;
  replyTo?: string;
}

export async function sendEmail({ to, subject, html, text, replyTo = 'winecoeswatini@yahoo.com' }: SendEmailParams) {
  const resendApiKey = process.env.RESEND_API_KEY;
  const fromEmail = process.env.RESEND_FROM_EMAIL || 'Wine & Co. Eswatini <onboarding@resend.dev>';

  // 1. Prioritize Resend API if API key is provided
  if (resendApiKey) {
    try {
      const resend = new Resend(resendApiKey);
      const toArray = Array.isArray(to) ? to : [to];

      const result = await resend.emails.send({
        from: fromEmail,
        to: toArray,
        subject,
        html,
        text,
        replyTo,
      });

      console.log('✅ Email sent via Resend:', result);
      return { success: true, provider: 'resend', data: result };
    } catch (err: any) {
      console.error('❌ Resend email error:', err);
    }
  }

  // 2. Fallback to SMTP / Nodemailer if SMTP_PASSWORD is configured
  const smtpPass = process.env.SMTP_PASSWORD;
  const smtpUser = process.env.SMTP_USERNAME || 'winecoeswatini@yahoo.com';
  const smtpHost = process.env.SMTP_HOST || 'smtp.mail.yahoo.com';
  const smtpPort = Number(process.env.SMTP_PORT) || 587;

  if (smtpPass) {
    try {
      const transporter = nodemailer.createTransport({
        host: smtpHost,
        port: smtpPort,
        secure: smtpPort === 465,
        auth: { user: smtpUser, pass: smtpPass },
      });

      const result = await transporter.sendMail({
        from: `"Wine & Co. Eswatini" <${smtpUser}>`,
        to: Array.isArray(to) ? to.join(', ') : to,
        replyTo,
        subject,
        html,
        text,
      });

      console.log('✅ Email sent via SMTP:', result.messageId);
      return { success: true, provider: 'smtp', data: result };
    } catch (err: any) {
      console.error('❌ SMTP email error:', err);
    }
  }

  console.warn('⚠️ No email sent: Neither RESEND_API_KEY nor valid SMTP_PASSWORD is set.');
  return { success: false, error: 'No email service configured' };
}
