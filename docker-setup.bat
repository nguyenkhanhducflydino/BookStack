@echo off
REM BookStack Docker Setup Script for Windows

echo 🐳 BookStack Docker Setup
echo =========================

REM Check if .env exists
if not exist .env (
    echo 📋 Creating .env file from .env.docker template...
    copy .env.docker .env
    echo ✅ .env file created
) else (
    echo ⚠️  .env file already exists
)

REM Build and start containers
echo 🏗️  Building Docker containers...
docker-compose build

echo 🚀 Starting containers...
docker-compose up -d

REM Wait for database to be ready
echo ⏳ Waiting for database to be ready...
timeout /t 15 /nobreak > nul

REM Install dependencies and setup Laravel
echo 📦 Installing Composer dependencies...
docker-compose exec app composer install

echo 🔧 Setting up Laravel...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo 🗄️  Running database migrations...
docker-compose exec app php artisan migrate --force

echo 🌱 Seeding database...
docker-compose exec app php artisan db:seed --force

echo 🔗 Creating storage link...
docker-compose exec app php artisan storage:link

echo 🎨 Building frontend assets...
docker-compose exec app npm install
docker-compose exec app npm run build

echo.
echo ✅ BookStack is ready!
echo 🌐 Open http://localhost in your browser
echo.
echo 📊 Container status:
docker-compose ps

echo.
echo 📝 Useful commands:
echo   docker-compose logs app     # View app logs
echo   docker-compose exec app bash # SSH into app container
echo   docker-compose down         # Stop containers
echo   docker-compose up -d        # Start containers

pause
