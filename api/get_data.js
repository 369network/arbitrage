import { supabase, verifyToken, isAdmin } from '../lib/supabase.js';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization');

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const token = req.headers.authorization?.replace('Bearer ', '');
    const user = await verifyToken(token);

    if (!user) {
      return res.status(401).json({ error: 'Unauthorized' });
    }

    const userIsAdmin = await isAdmin(user.id);

    // Fetch clients
    let clientsQuery = supabase.from('clients').select('*');
    
    if (!userIsAdmin) {
      const { data: userData } = await supabase
        .from('users')
        .select('client_id')
        .eq('id', user.id)
        .single();
      
      if (userData?.client_id) {
        clientsQuery = clientsQuery.eq('client_code', userData.client_id);
      }
    }

    const { data: clients, error: clientsError } = await clientsQuery;

    if (clientsError) {
      console.error('Clients error:', clientsError);
    }

    // Fetch domains
    const { data: domains, error: domainsError } = await supabase
      .from('domains')
      .select('*, clients(name, client_code)');

    if (domainsError) {
      console.error('Domains error:', domainsError);
    }

    // Fetch monthly data
    const { data: monthlyData, error: monthlyError } = await supabase
      .from('monthly_data')
      .select('*, domains(domain_name, country, traffic_source)')
      .order('year', { ascending: false })
      .order('month', { ascending: false });

    if (monthlyError) {
      console.error('Monthly data error:', monthlyError);
    }

    // Fetch payments
    const { data: payments, error: paymentsError } = await supabase
      .from('payments')
      .select('*, clients(name)')
      .order('payment_date', { ascending: false })
      .limit(50);

    if (paymentsError) {
      console.error('Payments error:', paymentsError);
    }

    // Calculate summary
    const totalRevenue = monthlyData?.reduce((sum, item) => sum + parseFloat(item.revenue || 0), 0) || 0;
    const totalExpense = monthlyData?.reduce((sum, item) => sum + parseFloat(item.expense || 0), 0) || 0;
    const totalProfit = totalRevenue - totalExpense;

    // Format monthly data by month
    const monthlyDataFormatted = {};
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    monthlyData?.forEach(item => {
      const monthIndex = monthNames.indexOf(item.month);
      const key = `${item.month} ${item.year}`;
      
      if (!monthlyDataFormatted[key]) {
        monthlyDataFormatted[key] = {
          month: key,
          revenue: 0,
          expense: 0,
          profit: 0,
          domains: [],
          networkShare: 0,
          clientShare: 0
        };
      }

      monthlyDataFormatted[key].revenue += parseFloat(item.revenue || 0);
      monthlyDataFormatted[key].expense += parseFloat(item.expense || 0);
      monthlyDataFormatted[key].profit += parseFloat(item.profit || 0);

      if (item.domains) {
        monthlyDataFormatted[key].domains.push({
          name: item.domains.domain_name,
          country: item.domains.country,
          source: item.domains.traffic_source,
          revenue: parseFloat(item.revenue || 0),
          expense: parseFloat(item.expense || 0)
        });
      }
    });

    // Calculate shares (50/50 split)
    Object.keys(monthlyDataFormatted).forEach(key => {
      const profit = monthlyDataFormatted[key].profit;
      monthlyDataFormatted[key].networkShare = profit / 2;
      monthlyDataFormatted[key].clientShare = profit / 2;
    });

    // Enrich clients with calculated fields
    const enrichedClients = (clients || []).map(client => {
      // Find domains for this client
      const clientDomains = domains?.filter(d => d.client_id === client.id) || [];
      
      // Calculate totals from monthly_data
      let totalRevenue = 0;
      let totalExpense = 0;
      
      clientDomains.forEach(domain => {
        const domainData = monthlyData?.filter(md => md.domain_id === domain.id) || [];
        domainData.forEach(md => {
          totalRevenue += parseFloat(md.revenue || 0);
          totalExpense += parseFloat(md.expense || 0);
        });
      });
      
      return {
        ...client,
        totalRevenue,
        totalExpense,
        profit: totalRevenue - totalExpense,
        domains: clientDomains.map(d => d.domain_name)
      };
    });

    return res.status(200).json({
      clients: enrichedClients,
      domains: domains || [],
      transactions: payments || [],
      monthlyData: monthlyDataFormatted,
      summary: {
        totalRevenue,
        totalExpense,
        totalProfit,
        activeClients: enrichedClients.length,
        activeDomains: domains?.length || 0
      }
    });

  } catch (error) {
    console.error('Get data error:', error);
    return res.status(500).json({ error: 'Internal server error' });
  }
}

