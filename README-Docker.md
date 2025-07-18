# BookStack Clone - Docker Deployment

## 🐳 Triển khai Docker

Dự án BookStack Clone đã được cấu hình để chạy với Docker, bao gồm:

- **PHP 8.2** với FPM
- **Nginx** web server
- **MySQL 8.0** database
- **Redis** cho cache và sessions
- **Node.js** để build assets

## 📋 Yêu cầu

- Docker Desktop
- Docker Compose
- Git

## 🚀 Cách chạy

### 1. Clone project
```bash
git clone <repository-url>
cd BookStack
```

### 2. Chạy setup script

**Windows:**
```cmd
docker-setup.bat
```

**Linux/macOS:**
```bash
chmod +x docker-setup.sh
./docker-setup.sh
```

### 3. Hoặc chạy thủ công

```bash
# 1. Tạo file .env
cp .env.docker .env

# 2. Build và start containers
docker-compose up -d

# 3. Install dependencies
docker-compose exec app composer install

# 4. Setup Laravel
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
docker-compose exec app php artisan storage:link

# 5. Build assets
docker-compose exec app npm install
docker-compose exec app npm run build
```

## 🌐 Truy cập ứng dụng

- **Web Application**: http://localhost
- **Database**: localhost:3306
- **Redis**: localhost:6379

## 📊 Quản lý containers

```bash
# Xem trạng thái containers
docker-compose ps

# Xem logs
docker-compose logs app
docker-compose logs db

# SSH vào container
docker-compose exec app bash

# Stop containers
docker-compose down

# Start containers
docker-compose up -d

# Rebuild containers
docker-compose build --no-cache
```

## 🗄️ Database

**Thông tin kết nối:**
- Host: localhost (từ host machine) hoặc `db` (từ containers)
- Port: 3306
- Database: bookstack
- Username: bookstack
- Password: bookstack_password

## 🔧 Environment Variables

Các biến môi trường quan trọng trong `.env`:

```env
APP_URL=http://localhost
DB_HOST=db
DB_DATABASE=bookstack
DB_USERNAME=bookstack
DB_PASSWORD=bookstack_password
REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## 📁 Cấu trúc Docker

```
docker/
├── nginx/
│   └── default.conf       # Nginx configuration
├── apache/
│   └── 000-default.conf   # Apache configuration (alternative)
└── php/
    └── local.ini          # PHP configuration

docker-compose.yml         # Container orchestration
Dockerfile                 # Application container
.dockerignore              # Files to ignore in build
.env.docker               # Environment template
```

## 🛠️ Development

Để development, bạn có thể mount source code:

```yaml
# Thêm vào docker-compose.yml service app:
volumes:
  - ./:/var/www/html
  - /var/www/html/vendor
  - /var/www/html/node_modules
```

## 🔍 Troubleshooting

### Container không start
```bash
docker-compose logs app
docker-compose logs db
```

### Permission errors
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Database connection errors
```bash
# Kiểm tra database container
docker-compose exec db mysql -u bookstack -p bookstack

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

### Clear caches
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

## 🌍 Production Deployment

Để deploy production:

1. **Tạo .env.production**
2. **Cấu hình SSL certificate**
3. **Setup proper domain**
4. **Configure backup strategy**
5. **Setup monitoring**

```bash
# Production build
docker-compose -f docker-compose.production.yml up -d
```

## 📝 Ghi chú

- Default admin user sẽ được tạo thông qua seeder
- Upload files được lưu trong `/storage/app/public`
- Logs được lưu trong `/storage/logs`
- Session và cache sử dụng Redis
