# TrapTrace - Network Honeypot Security System

A comprehensive honeypot system for detecting and monitoring network security threats.

## Features

- 🛡️ **Real-time Threat Detection**: Monitor and detect unauthorized access attempts
- 📊 **Security Analytics**: Visualize attack patterns and security metrics
- 🚨 **Alert Management**: Track critical, high, and medium-risk security events
- 📦 **Packet Capture**: Network traffic analysis (requires TShark)
- 👤 **Attacker Tracking**: Log and analyze malicious IP addresses
- 📝 **Activity Logging**: Comprehensive system and security event logs

## Deployment on Railway

### Quick Deploy

1. Click the button below to deploy to Railway:
   
   [![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template)

2. Add a MySQL database to your Railway project

3. Set the following environment variables in Railway:
   - `MYSQLHOST` - Your Railway MySQL host
   - `MYSQLPORT` - Your Railway MySQL port (usually 3306)
   - `MYSQLDATABASE` - Your database name (usually "railway")
   - `MYSQLUSER` - Your MySQL username (usually "root")
   - `MYSQLPASSWORD` - Your MySQL password
   - `APP_ENV` - Set to "production"

4. Import the database schema from `database/traptrace.sql`

### Manual Railway Deployment

1. Install Railway CLI:
   ```bash
   npm install -g @railway/cli
   ```

2. Login to Railway:
   ```bash
   railway login
   ```

3. Initialize project:
   ```bash
   railway init
   ```

4. Add MySQL database:
   ```bash
   railway add mysql
   ```

5. Deploy:
   ```bash
   railway up
   ```

6. Import database schema:
   - Access your Railway MySQL instance
   - Import the SQL file from `database/traptrace.sql`

## Database Setup

Import the database schema:

```sql
mysql -h [MYSQLHOST] -P [MYSQLPORT] -u [MYSQLUSER] -p[MYSQLPASSWORD] [MYSQLDATABASE] < database/traptrace.sql
```

Or use Railway's MySQL dashboard to import the `database/traptrace.sql` file.

## Default Credentials

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change the default password immediately after first login!

## Environment Variables

Required environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `MYSQLHOST` | MySQL database host | localhost |
| `MYSQLPORT` | MySQL database port | 3306 |
| `MYSQLDATABASE` | Database name | traptrace |
| `MYSQLUSER` | Database username | root |
| `MYSQLPASSWORD` | Database password | (empty) |
| `APP_ENV` | Application environment | development |
| `APP_DEBUG` | Enable debug mode | false |

## Features Overview

### Dashboard
- Real-time security metrics
- Recent attack events
- System status monitoring

### Alerts Management
- Critical, High, and Medium risk categorization
- Real-time alert notifications
- Attack type classification

### Packet Capture
- Network traffic monitoring
- Port scanning detection
- Protocol analysis (TCP/UDP)

### Analytics
- Attack pattern visualization
- Geographic threat analysis
- Historical trend reports

## Security Notes

1. **Packet Capture**: The packet capture feature requires TShark/Wireshark and is typically not available in containerized environments like Railway
2. **Production Deployment**: Ensure all environment variables are properly configured
3. **SSL/TLS**: Enable HTTPS through Railway's automatic SSL certificates
4. **Database Security**: Use strong passwords and secure connection strings
5. **Session Management**: Configure appropriate session timeouts

## Technology Stack

- **Backend**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **Deployment**: Railway with Nixpacks

## Local Development

1. Install XAMPP or similar PHP development environment
2. Clone the repository to your htdocs folder
3. Import `database/traptrace.sql` to your local MySQL
4. Configure database credentials in environment variables or `config/database.php`
5. Access the application at `http://localhost/TrapTrace-main/`

## Project Structure

```
TrapTrace-main/
├── API/                    # REST API endpoints
│   ├── alerts.php         # Security alerts API
│   ├── capture_packets.php # Packet capture API
│   └── detect_port_scans.php # Port scan detection
├── config/                # Configuration files
│   └── database.php       # Database connection
├── css/                   # Stylesheets
├── database/              # Database schema
│   └── traptrace.sql     # SQL schema file
├── Pages/                 # Application pages
│   ├── alerts.php        # Alerts dashboard
│   ├── analytics.php     # Analytics page
│   ├── attackers.php     # Attackers list
│   ├── logs.php          # System logs
│   ├── monitoring.php    # Monitoring dashboard
│   ├── packet_logs.php   # Packet capture logs
│   ├── reports.php       # Reports page
│   └── settings.php      # Settings page
├── auth.php              # Authentication handler
├── dashboard.php         # Main dashboard
├── index.php             # Login page
└── logout.php            # Logout handler
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is for educational and security research purposes.

## Support

For issues and questions, please open an issue on the repository.
