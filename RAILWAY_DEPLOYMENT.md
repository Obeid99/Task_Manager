# 🚀 Railway Deployment Guide

## Quick Setup

### 1. Add MySQL Database to Railway
1. Go to your Railway project dashboard
2. Click **"+ New"** → **"Database"** → **"MySQL"**
3. Railway will automatically:
   - Create a MySQL database
   - Set the `DATABASE_URL` environment variable
   - Connect it to your app

### 2. Set Environment Variables
In Railway dashboard → **Variables** tab, add:
```
APP_ENV=prod
APP_SECRET=your-secret-key-here
```

### 3. Deploy
```bash
git push origin main
```

## Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `DATABASE_URL` | MySQL connection string | Auto-set by Railway MySQL |
| `APP_ENV` | Application environment | Yes (`prod`) |
| `APP_SECRET` | Symfony secret key | Yes |

## Database Setup

The app will automatically:
- Wait for database connection (90 seconds timeout)
- Run migrations
- Create admin user (admin@jems.com / admin)
- Skip database operations if no connection

## Troubleshooting

### "Database is not ready yet. Waiting..."
- **Solution**: Add MySQL database service in Railway
- The app will timeout after 90 seconds and run without database

### App won't start
- Check Railway logs for errors
- Ensure all environment variables are set
- Verify Dockerfile builds successfully

## Local Development vs Production

- **Local**: Uses Docker Compose with MySQL container
- **Railway**: Uses Railway's managed MySQL database
- **Fallback**: App runs without database if none available
