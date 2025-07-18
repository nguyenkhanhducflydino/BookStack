#!/bin/bash

# BookStack Docker Setup Script

echo "🐳 BookStack Docker Setup"
echo "========================="

# Function to generate APP_KEY
generate_app_key() {
    echo "📝 Generating Laravel APP_KEY..."
    docker run --rm -v "$PWD":/app -w /app php:8.2-cli php -r "require 'vendor/autoload.php'; echo 'APP_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
}

# Check if .env exists
if [ ! -f .env ]; then
    echo "📋 Creating .env file from .env.docker template..."
    cp .env.docker .env
    
    # Generate APP_KEY
    APP_KEY=$(generate_app_key | grep APP_KEY | cut -d'=' -f2-)
    sed -i "s/APP_KEY=/APP_KEY=$APP_KEY/" .env
    echo "✅ .env file created with generated APP_KEY"
else
    echo "⚠️  .env file already exists"
fi

# Build and start containers
echo "🏗️  Building Docker containers..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install dependencies and setup Laravel
echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install

echo "🔧 Setting up Laravel..."
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

echo "🌱 Seeding database..."
docker-compose exec app php artisan db:seed --force

echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

echo "🎨 Building frontend assets..."
docker-compose exec app npm install
docker-compose exec app npm run build

echo ""
echo "✅ BookStack is ready!"
echo "🌐 Open http://localhost in your browser"
echo ""
echo "📊 Container status:"
docker-compose ps

echo ""
echo "📝 Useful commands:"
echo "  docker-compose logs app     # View app logs"
echo "  docker-compose exec app bash # SSH into app container"
echo "  docker-compose down         # Stop containers"
echo "  docker-compose up -d        # Start containers"
