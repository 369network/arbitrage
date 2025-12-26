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

    // GET - List all payments
    if (req.method === 'GET') {
      const { data, error } = await supabase
        .from('payments')
        .select('*, clients(name, client_code)')
        .order('payment_date', { ascending: false });

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      return res.status(200).json({ success: true, payments: data });
    }

    // POST - Create new payment
    if (req.method === 'POST') {
      const { client_id, type, amount, payment_date, method, notes } = req.body;

      if (!client_id || !type || !amount || !payment_date) {
        return res.status(400).json({ error: 'Missing required fields: client_id, type, amount, payment_date' });
      }

      const { data, error } = await supabase
        .from('payments')
        .insert([{
          client_id,
          payment_type: type,
          amount: parseFloat(amount),
          payment_date,
          payment_method: method || 'bank_transfer',
          notes: notes || null,
          status: 'completed'
        }])
        .select()
        .single();

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      // Log activity
      await supabase.from('activity_log').insert({
        user_id: user.id,
        action: 'create_payment',
        details: `Recorded payment: $${amount} for client ${client_id}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(201).json({ success: true, payment: data });
    }

    // PUT - Update payment
    if (req.method === 'PUT') {
      const { id, ...updates } = req.body;

      if (!id) {
        return res.status(400).json({ error: 'Payment ID is required' });
      }

      const { data, error } = await supabase
        .from('payments')
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
        action: 'update_payment',
        details: `Updated payment: ${id}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(200).json({ success: true, payment: data });
    }

    // DELETE - Delete payment
    if (req.method === 'DELETE') {
      const { id } = req.query;

      if (!id) {
        return res.status(400).json({ error: 'Payment ID is required' });
      }

      const { error } = await supabase
        .from('payments')
        .delete()
        .eq('id', id);

      if (error) {
        return res.status(500).json({ error: error.message });
      }

      // Log activity
      await supabase.from('activity_log').insert({
        user_id: user.id,
        action: 'delete_payment',
        details: `Deleted payment: ${id}`,
        ip_address: req.headers['x-forwarded-for'] || req.connection.remoteAddress
      });

      return res.status(200).json({ success: true, message: 'Payment deleted' });
    }

    return res.status(405).json({ error: 'Method not allowed' });

  } catch (error) {
    console.error('Payments API error:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}

