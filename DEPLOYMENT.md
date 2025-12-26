# 369Network Arbitrage - Vercel + Supabase Deployment Guide

This guide will help you deploy the 369Network Arbitrage Dashboard to Vercel with Supabase as the database.

## 📋 Prerequisites

- GitHub account
- Vercel account (sign up at https://vercel.com)
- Supabase account (sign up at https://supabase.com)

## 🗄️ Step 1: Set Up Supabase Database

### 1.1 Create Supabase Project

1. Go to https://supabase.com/dashboard
2. Click "New Project"
3. Fill in the details:
   - **Name**: 369-arbitrage
   - **Database Password**: (create a strong password and save it)
   - **Region**: Choose closest to your users
4. Click "Create new project" (takes ~2 minutes)

### 1.2 Run Database Schema

1. Once your project is ready, go to **SQL Editor** in the left sidebar
2. Click "New Query"
3. Copy the entire contents of `supabase-schema.sql` from your repository
4. Paste it into the SQL editor
5. Click "Run" to execute the schema
6. Verify tables are created by going to **Table Editor**

### 1.3 Get Supabase Credentials

1. Go to **Project Settings** (gear icon in sidebar)
2. Click **API** in the left menu
3. Copy these values (you'll need them later):
   - **Project URL** (looks like: `https://xxxxx.supabase.co`)
   - **anon public** key (starts with `eyJhbGci...`)

## 🚀 Step 2: Deploy to Vercel

### 2.1 Push Code to GitHub

Your code is already on GitHub at: https://github.com/369network/arbitrage

### 2.2 Import to Vercel

1. Go to https://vercel.com/dashboard
2. Click "Add New..." → "Project"
3. Click "Import" next to your `369network/arbitrage` repository
4. If not connected, click "Import Git Repository" and connect your GitHub account

### 2.3 Configure Project

1. **Framework Preset**: Leave as "Other"
2. **Root Directory**: Leave as `./`
3. **Build Command**: Leave default
4. **Output Directory**: Leave default

### 2.4 Add Environment Variables

Click "Environment Variables" and add:

| Name | Value |
|------|-------|
| `SUPABASE_URL` | Your Supabase Project URL from Step 1.3 |
| `SUPABASE_ANON_KEY` | Your Supabase anon key from Step 1.3 |

**Important**: Add these variables for all environments (Production, Preview, Development)

### 2.5 Deploy

1. Click "Deploy"
2. Wait for deployment to complete (~2-3 minutes)
3. Once done, you'll get a URL like: `https://arbitrage-xxxxx.vercel.app`

## ✅ Step 3: Verify Deployment

### 3.1 Test the Application

1. Visit your Vercel URL
2. You should see the login page
3. Try logging in with:
   - **Email**: `admin@369network.com`
   - **Password**: `admin123`

### 3.2 Check API Endpoints

Test these endpoints (replace `your-url` with your Vercel URL):

```bash
# Health check
curl https://your-url.vercel.app/api/get_data

# Should return 401 Unauthorized (expected without auth token)
```

## 🔐 Step 4: Set Up Authentication

### 4.1 Enable Email Auth in Supabase

1. Go to **Authentication** in Supabase dashboard
2. Click **Providers**
3. Enable **Email** provider
4. Configure email templates if needed

### 4.2 Create Admin User (if not exists)

The schema already creates an admin user:
- Email: `admin@369network.com`
- Password: `admin123`

**⚠️ IMPORTANT**: Change this password immediately after first login!

## 📊 Step 5: Add Sample Data (Optional)

If you want to test with sample data:

1. Go to Supabase **SQL Editor**
2. Run this query:

```sql
-- Insert sample domains
INSERT INTO domains (client_id, domain_name, country, traffic_source)
SELECT 
    c.id,
    'example-domain.com',
    'USA',
    'facebook'
FROM clients c
WHERE c.client_code = 'USM001'
LIMIT 1;

-- Insert sample monthly data
INSERT INTO monthly_data (domain_id, client_id, month, year, revenue, expense)
SELECT 
    d.id,
    d.client_id,
    'December',
    2025,
    50000,
    30000
FROM domains d
LIMIT 1;
```

## 🔄 Step 6: Set Up Continuous Deployment

Vercel automatically deploys when you push to GitHub:

1. Make changes to your code locally
2. Commit and push to GitHub:
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   ```
3. Vercel automatically deploys the new version

## 🛠️ Troubleshooting

### Issue: API Returns 500 Error

**Solution**: Check Vercel Function Logs
1. Go to Vercel Dashboard → Your Project
2. Click "Functions" tab
3. Check error logs

### Issue: Database Connection Failed

**Solution**: Verify environment variables
1. Go to Vercel Dashboard → Your Project → Settings
2. Click "Environment Variables"
3. Verify `SUPABASE_URL` and `SUPABASE_ANON_KEY` are correct
4. Redeploy after fixing

### Issue: Login Not Working

**Solution**: Check Supabase Auth
1. Go to Supabase Dashboard → Authentication
2. Verify Email provider is enabled
3. Check if user exists in Users table

## 📱 Step 7: Custom Domain (Optional)

### 7.1 Add Custom Domain in Vercel

1. Go to Project Settings → Domains
2. Click "Add"
3. Enter your domain (e.g., `arbitrage.369network.com`)
4. Follow DNS configuration instructions

### 7.2 Update Supabase Redirect URLs

1. Go to Supabase → Authentication → URL Configuration
2. Add your custom domain to **Site URL**
3. Add to **Redirect URLs**

## 🔒 Security Checklist

- [ ] Change default admin password
- [ ] Enable 2FA for Supabase account
- [ ] Enable 2FA for Vercel account
- [ ] Review Supabase RLS policies
- [ ] Set up database backups in Supabase
- [ ] Enable Vercel password protection (if needed)
- [ ] Configure CORS properly
- [ ] Review API rate limits

## 📈 Monitoring

### Vercel Analytics

1. Go to Project → Analytics
2. View page views, performance metrics

### Supabase Monitoring

1. Go to Database → Reports
2. Monitor query performance
3. Check API usage

## 🆘 Support

If you encounter issues:

1. Check Vercel deployment logs
2. Check Supabase logs (Database → Logs)
3. Review browser console for errors
4. Contact 369Network support team

## 🎉 Success!

Your 369Network Arbitrage Dashboard is now live on:
- **Production URL**: https://your-project.vercel.app
- **Database**: Hosted on Supabase
- **Auto-deploy**: Enabled via GitHub

---

**Next Steps**:
1. Change default passwords
2. Add your team members
3. Import your actual data
4. Configure custom domain
5. Set up monitoring alerts

