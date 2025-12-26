import { supabase } from '../../lib/supabase.js';

export default async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version');

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password are required' });
    }

    // Sign in with Supabase Auth
    const { data, error } = await supabase.auth.signInWithPassword({
      email,
      password
    });

    if (error) {
      console.error('Supabase auth error:', error);
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    // Map user roles based on email (until we set up proper user table sync)
    const userRoles = {
      'contact@369network.com': { role: 'admin', name: '369Network Admin', client_id: null },
      'vpmedia@369network.com': { role: 'client', name: 'VPMedia', client_id: 'VPM001' },
      'thebes@369network.com': { role: 'client', name: 'Thebes', client_id: 'THB001' },
      'usaman@369network.com': { role: 'client', name: 'Usmanbhai', client_id: 'USM001' }
    };

    const userInfo = userRoles[email.toLowerCase()] || { role: 'client', name: email.split('@')[0], client_id: null };

    // Try to get user details from users table
    const { data: userData } = await supabase
      .from('users')
      .select('*')
      .eq('email', email)
      .single();

    // Use database user info if available, otherwise use mapped info
    const finalUserInfo = userData || {
      id: data.user.id,
      email: email,
      name: userInfo.name,
      role: userInfo.role,
      client_id: userInfo.client_id
    };

    // Log activity
    try {
      await supabase.from('activity_log').insert({
        user_id: data.user.id,
        action: 'login',
        details: 'User logged in successfully',
        ip_address: req.headers['x-forwarded-for'] || req.connection?.remoteAddress || 'unknown'
      });
    } catch (logError) {
      console.error('Activity log error:', logError);
      // Don't fail login if activity log fails
    }

    return res.status(200).json({
      success: true,
      user: {
        id: data.user.id,
        email: finalUserInfo.email || email,
        name: finalUserInfo.name || userInfo.name,
        role: finalUserInfo.role || userInfo.role,
        client_id: finalUserInfo.client_id || userInfo.client_id
      },
      session: data.session
    });

  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).json({ error: 'Connection error. Please try again.' });
  }
}

