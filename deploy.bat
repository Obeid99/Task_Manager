@echo off
echo 🚀 Deploying JEMS Task Manager...

echo 📝 Setting up production environment...
copy .env.prod .env

echo 🐳 Building Docker containers...
docker-compose down
docker-compose build --no-cache
docker-compose up -d

echo ⏳ Waiting for services to start...
timeout /t 10 /nobreak > nul

echo 🔍 Checking service status...
docker-compose ps

echo ✅ Deployment complete!
echo.
echo 🌐 Application: http://localhost:8000
echo 🗄️  Database: localhost:3306
echo 📊 phpMyAdmin: http://localhost:8081
echo.
echo 👤 Admin Login:
echo    Email: admin@jems.com
echo    Password: admin
echo.
echo 📋 To view logs: docker-compose logs -f web
echo 🛑 To stop: docker-compose down

pause
