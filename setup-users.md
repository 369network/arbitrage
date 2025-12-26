# Setup User Accounts in Supabase

## User Credentials

### Admin Account
- **Email**: contact@369network.com
- **Password**: Spidigoo@#369
- **Role**: admin
- **Name**: 369Network Admin

### VPMedia Client
- **Email**: vpmedia@369network.com
- **Password**: Password@#123
- **Role**: client
- **Client Code**: VPM001
- **Name**: VPMedia

### Thebes Client
- **Email**: thebes@369network.com
- **Password**: Password@#123
- **Role**: client
- **Client Code**: THB001
- **Name**: Thebes

### Usman Client
- **Email**: usaman@369network.com
- **Password**: Password@#123
- **Role**: client
- **Client Code**: USM001
- **Name**: Usmanbhai

## Setup Instructions

### Option 1: Via Supabase Dashboard (Recommended)

1. Go to https://supabase.com/dashboard/project/wtmdbhlzhozjaddliqjr
2. Click "Authentication" in the left sidebar
3. Click "Add user" button
4. For each user above:
   - Enter email
   - Enter password
   - Check "Auto Confirm User"
   - Click "Create user"

5. After creating users in Auth, run this SQL to link them:

```sql
-- Update users table with correct roles and client associations
UPDATE users SET 
    email = 'contact@369network.com',
    name = '369Network Admin',
    role = 'admin',
    client_id = NULL
WHERE email = 'admin@369network.com';

-- Insert client users (they will be linked after Supabase Auth creation)
INSERT INTO users (email, name, role, client_id, status)
VALUES 
    ('vpmedia@369network.com', 'VPMedia', 'client', 'VPM001', 'active'),
    ('thebes@369network.com', 'Thebes', 'client', 'THB001', 'active'),
    ('usaman@369network.com', 'Usmanbhai', 'client', 'USM001', 'active')
ON CONFLICT (email) DO UPDATE SET
    name = EXCLUDED.name,
    role = EXCLUDED.role,
    client_id = EXCLUDED.client_id,
    status = EXCLUDED.status;
```

### Option 2: Via Supabase API (Automated)

Use the Supabase Admin API to create users programmatically. This requires the service role key.

## Verification

After setup, test each login:

1. **Admin Login**
   - URL: https://arbi.vercel.app/login.html
   - Email: contact@369network.com
   - Password: Spidigoo@#369
   - Should see: Full dashboard with all clients

2. **VPMedia Login**
   - Email: vpmedia@369network.com
   - Password: Password@#123
   - Should see: Only VPMedia data

3. **Thebes Login**
   - Email: thebes@369network.com
   - Password: Password@#123
   - Should see: Only Thebes data

4. **Usman Login**
   - Email: usaman@369network.com
   - Password: Password@#123
   - Should see: Only Usman data

## Security Notes

- All passwords are hashed by Supabase Auth
- Sessions expire after 1 hour by default
- JWT tokens are used for API authentication
- Row Level Security (RLS) enforces data isolation
- Clients can only see their own data
- Admin can see all data

## Password Reset

If you need to reset a password:
1. Go to Supabase Dashboard → Authentication
2. Find the user
3. Click "..." menu
4. Click "Reset Password"
5. Send reset email or set new password directly

