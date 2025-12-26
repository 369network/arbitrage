import { supabase, verifyToken, isAdmin } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,POST,PUT,DELETE');
  res.setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization');

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  try {
    const token = req.headers.authorization?.replace('Bearer ', '');
    const user = await verifyToken(token);

    if (!user) {
      return res.status(401).json({ error: 'Unauthorized' });
    }

    const userIsAdmin = await isAdmin(user.id);

    if (!userIsAdmin) {
      return res.status(403).json({ error: 'Forbidden - Admin access required' });
    }

    // GET - List all clients
    if (req.method === 'GET') {
      const { data, error } = await supabase
        .from('clients')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      return res.status(200).json({ success: true, clients: data });
    }

    // POST - Create new client
    if (req.method === 'POST') {
      const { client_code, name, email, phone, revenue_share, status } = req.body;

      if (!name || !email) {
        return res.status(400).json({ error: 'Missing required fields: name and email' });
      }

      // Auto-generate client_code if not provided (first 3 letters of name + random number)
      const finalClientCode = client_code || `${name.substring(0, 3).toUpperCase()}${Math.floor(Math.random() * 1000)}`;

      const { data, error } = await supabase
        .from('clients')
        .insert([{
          client_code: finalClientCode,
          name,
          email,
          phone: phone || null,
          revenue_share: revenue_share || 50.00,
          status: status || 'active'
        }])
        .select()
        .single();

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      // Log activity
      await supabase.from('activity_log').insert({
        user_id: user.id,
        action: 'create_client',
        details: `Created client: ${name}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(201).json({ success: true, client: data });
    }

    // PUT - Update client
    if (req.method === 'PUT') {
      const { id, ...updates } = req.body;

      if (!id) {
        return res.status(400).json({ error: 'Client ID is required' });
      }

      const { data, error } = await supabase
        .from('clients')
        .update(updates)
        .eq('id', id)
        .select()
        .single();

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      // Log activity
      await supabase.from('activity_log').insert({
        user_id: user.id,
        action: 'update_client',
        details: `Updated client: ${id}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(200).json({ success: true, client: data });
    }

    // DELETE - Delete client
    if (req.method === 'DELETE') {
      const { id } = req.query;

      if (!id) {
        return res.status(400).json({ error: 'Client ID is required' });
      }

      const { error } = await supabase
        .from('clients')
        .delete()
        .eq('id', id);

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      // Log activity
      await supabase.from('activity_log').insert({
        user_id: user.id,
        action: 'delete_client',
        details: `Deleted client: ${id}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(200).json({ success: true, message: 'Client deleted' });
    }

    return res.status(405).json({ error: 'Method not allowed' });

  } catch (error) {
    console.error('Clients API error:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}

