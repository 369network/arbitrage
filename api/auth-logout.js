import { supabase } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization');

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const token = req.headers.authorization?.replace('Bearer ', '');
    
    if (token) {
      // Log activity before logout
      const { data: { user } } = await supabase.auth.getUser(token);
      
      if (user) {
        await supabase.from('activity_log').insert({
          user_id: user.id,
          action: 'logout',
          details: 'User logged out',
          ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
        });
      }

      // Sign out
      await supabase.auth.signOut();
    }

    return res.status(200).json({ success: true, message: 'Logged out successfully' });

  } catch (error) {
    console.error('Logout error:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}

