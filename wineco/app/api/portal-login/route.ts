import { NextResponse } from 'next/server';
import { supabase, isSupabaseConfigured } from '@/lib/supabase';
import { PortalLoginLog } from '@/lib/types';

// In-memory fallback store for real-time tracking across requests in serverless runtime
let loginLogsStore: PortalLoginLog[] = [
  {
    id: 'log_1',
    email: 'admin@wineco.sz',
    role: 'admin',
    timestamp: new Date(Date.now() - 1000 * 60 * 15).toISOString(),
    status: 'success',
  },
  {
    id: 'log_2',
    email: 'siphiwosethuthikazi@gmail.com',
    role: 'admin',
    timestamp: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
    status: 'success',
  },
  {
    id: 'log_3',
    email: 'phumza19952010@gmail.com',
    role: 'member',
    timestamp: new Date(Date.now() - 1000 * 60 * 120).toISOString(),
    status: 'success',
  },
  {
    id: 'log_4',
    email: 'staff@wineco.sz',
    role: 'staff',
    timestamp: new Date(Date.now() - 1000 * 60 * 240).toISOString(),
    status: 'success',
  },
];

export async function GET() {
  try {
    if (isSupabaseConfigured && supabase) {
      try {
        const { data, error } = await supabase
          .from('portal_logins')
          .select('*')
          .order('timestamp', { ascending: false })
          .limit(50);

        if (!error && data && data.length > 0) {
          return NextResponse.json({ success: true, logs: data });
        }
      } catch (e) {
        console.warn('Supabase fetch failed, using memory store:', e);
      }
    }

    return NextResponse.json({ success: true, logs: loginLogsStore });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { email, role = 'member', status = 'success', ip } = body;

    if (!email) {
      return NextResponse.json({ success: false, error: 'Email is required' }, { status: 400 });
    }

    const newLog: PortalLoginLog = {
      id: `log_${Date.now()}_${Math.random().toString(36).substr(2, 6)}`,
      email: email.toLowerCase().trim(),
      role: role,
      timestamp: new Date().toISOString(),
      ip: ip || '127.0.0.1',
      status: status,
    };

    // Prepend to memory store
    loginLogsStore = [newLog, ...loginLogsStore].slice(0, 100);

    // Save to Supabase if configured
    if (isSupabaseConfigured && supabase) {
      try {
        await supabase.from('portal_logins').insert({
          email: newLog.email,
          role: newLog.role,
          timestamp: newLog.timestamp,
          ip: newLog.ip,
          status: newLog.status,
        });
      } catch (dbErr) {
        console.warn('Supabase insert login error:', dbErr);
      }
    }

    return NextResponse.json({
      success: true,
      message: 'Portal login recorded in real time',
      log: newLog,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
