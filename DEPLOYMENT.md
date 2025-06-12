# 🚀 JEMS Task Manager - Deployment Guide

## Prerequisites

- Docker Desktop installed and running
- Git (to clone the repository)

## Quick Deployment

### Option 1: Windows
```bash
deploy.bat
```

### Option 2: Linux/Mac
```bash
./deploy.sh
```

### Option 3: Manual
```bash
# Copy production environment
cp .env.prod .env

# Build and start
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Access the Application

- **Web App**: http://localhost:8000
- **Database**: localhost:3306
- **phpMyAdmin**: http://localhost:8081

## Default Login

**Admin Account:**
- Email: `admin@jems.com`
- Password: `admin`

## Useful Commands

```bash
# View logs
docker-compose logs -f web

# Stop services
docker-compose down

# Restart services
docker-compose restart

# Access web container
docker-compose exec web bash

# Access database
docker-compose exec database mysql -u symfony -p symfony
```

## Production Notes

- Change `APP_SECRET` in `.env.prod` for production
- Update database credentials for production
- Configure proper mailer settings
- Set up SSL/HTTPS for production
- Consider using environment-specific docker-compose files

## Troubleshooting

**Database connection issues:**
```bash
docker-compose logs database
```

**Web app not starting:**
```bash
docker-compose logs web
```

**Reset everything:**
```bash
docker-compose down -v
docker-compose up --build
```
