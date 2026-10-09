# Railway Deployment Setup Guide

## Step 1: Create a Railway Project

1. Go to [Railway.app](https://railway.app)
2. Click "New Project"
3. Select "Deploy from GitHub repo" or "Empty Project"

## Step 2: Add MySQL Database

1. In your Railway project dashboard, click "+ New"
2. Select "Database" → "Add MySQL"
3. Railway will automatically provision a MySQL instance
4. Note: Railway will automatically set these environment variables:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   - `MYSQL_URL`

## Step 3: Deploy Your Application

### Option A: Deploy from GitHub

1. Connect your GitHub repository to Railway
2. Railway will automatically detect it's a PHP project
3. Click "Deploy"

### Option B: Deploy using Railway CLI

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login to Railway
railway login

# Link to your project (or create new)
railway link

# Deploy
railway up
```

## Step 4: Set Additional Environment Variables

In Railway dashboard, add these variables to your service:

```
APP_ENV=production
APP_DEBUG=false
```

## Step 5: Import Database Schema

### Method 1: Using Railway CLI

```bash
# Get your database connection string
railway variables

# Connect to MySQL
railway connect mysql

# Then paste the contents of database/traptrace.sql
```

### Method 2: Using MySQL Client

```bash
mysql -h [MYSQLHOST] -P [MYSQLPORT] -u [MYSQLUSER] -p[MYSQLPASSWORD] [MYSQLDATABASE] < database/traptrace.sql
```

### Method 3: Using Railway MySQL Dashboard

1. Go to your MySQL database in Railway
2. Click "Data" tab
3. Use the query editor to paste and execute the SQL from `database/traptrace.sql`

## Step 6: Verify Deployment

1. Click on your service in Railway
2. Go to "Settings" → "Domains"
3. Generate a domain or add your custom domain
4. Visit your domain and login with:
   - Username: `admin`
   - Password: `admin123`

## Important Notes

### Packet Capture Feature
⚠️ **The packet capture feature will NOT work on Railway** because:
- Railway uses containerized environments
- No direct network interface access
- TShark/Wireshark cannot be installed
- This is expected behavior - packet capture is for local development only

### Security Recommendations

1. **Change Default Password**: Immediately after first login
2. **Use HTTPS**: Railway provides automatic SSL certificates
3. **Environment Variables**: Never commit `.env` file to repository
4. **Database Backups**: Set up regular backups in Railway dashboard
5. **Monitor Usage**: Check Railway dashboard for resource usage

### Database Connection

Railway MySQL can be connected using:
- Individual variables (MYSQLHOST, MYSQLPORT, etc.)
- Connection URL (MYSQL_URL)

The application supports both methods automatically.

### Troubleshooting

#### Can't connect to database
- Verify all MySQL environment variables are set
- Check Railway MySQL service is running
- Ensure database schema is imported

#### Session issues
- Railway provides HTTPS automatically
- Secure cookies are enabled in production
- Clear browser cookies and try again

#### Page not loading
- Check Railway deployment logs
- Verify PHP version is 8.0+
- Check for any error messages in logs

### Getting Logs

```bash
# Using Railway CLI
railway logs

# Or view in Railway dashboard under "Deployments" → Click deployment → "Logs"
```

## Railway-Specific Features

### Automatic SSL
Railway provides automatic SSL certificates for all deployments.

### Environment Variables
All Railway services can share environment variables within the same project.

### Automatic Deployments
When connected to GitHub, Railway automatically deploys on every push to main branch.

### Scaling
You can scale your application in Railway dashboard under "Settings" → "Resources"

## Cost Estimation

Railway offers:
- **Hobby Plan**: $5/month for 500 execution hours
- **MySQL Database**: Included in plan
- **Bandwidth**: 100GB included

For production use, monitor your usage in the Railway dashboard.

## Next Steps

1. Change admin password
2. Customize application settings
3. Set up monitoring/alerts
4. Configure custom domain (optional)
5. Set up database backups

## Support

- Railway Documentation: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Railway GitHub: https://github.com/railwayapp
