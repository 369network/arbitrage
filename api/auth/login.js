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

    // Temporary: Check against hardcoded credentials until Supabase Auth is set up
    const validCredentials = {
      'contact@369network.com': { password: 'Spidigoo@#369', role: 'admin', name: '369Network Admin', client_id: null },
      'vpmedia@369network.com': { password: 'Password@#123', role: 'client', name: 'VPMedia', client_id: 'VPM001' },
      'thebes@369network.com': { password: 'Password@#123', role: 'client', name: 'Thebes', client_id: 'THB001' },
      'usaman@369network.com': { password: 'Password@#123', role: 'client', name: 'Usmanbhai', client_id: 'USM001' }
    };

    const userCreds = validCredentials[email.toLowerCase()];
    
    if (!userCreds || userCreds.password !== password) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    // Create session data
    const sessionData = {
      access_token: 'temp_token_' + Date.now(),
      expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(), // 24 hours
      user: {
        email: email,
        id: email.replace('@', '_').replace('.', '_')
      }
    };

    return res.status(200).json({
      success: true,
      user: {
        id: email.replace('@', '_').replace('.', '_'),
        email: email,
        name: userCreds.name,
        role: userCreds.role,
        client_id: userCreds.client_id
      },
      session: sessionData
    });

  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}

