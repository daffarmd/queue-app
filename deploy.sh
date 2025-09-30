#!/bin/bash

# TRI MULYO Queue Management System - Deployment Script
# This script sets up the complete queue management system

echo "🏥 TRI MULYO Queue Management System Deployment"
echo "================================================"

# Step 1: Install dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Step 2: Setup application
echo "🔑 Setting up application..."
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 3: Database setup
echo "🗄️ Setting up database..."
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=ServiceSeeder --force

# Step 4: Create storage links
echo "🔗 Creating storage links..."
php artisan storage:link

# Step 5: Set permissions
echo "🛡️ Setting file permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "🚀 System Overview:"
echo "==================="
echo "• Public Display: http://your-domain.com/"
echo "• Staff Dashboard: http://your-domain.com/staff"
echo "• Admin Panel: http://your-domain.com/admin"
echo "• Login: http://your-domain.com/login"
echo ""
echo "👥 Default Users:"
echo "• Admin: admin@trimulyo.com / password123"
echo "• Staff: staff@trimulyo.com / password123"
echo ""
echo "⚠️ Important: Change default passwords in production!"
echo ""
echo "📋 Services Available:"
echo "• General Consultation (GEN)"
echo "• Pharmacy (PHR)"
echo "• Laboratory (LAB)"
echo "• Registration (REG)"
echo ""
echo "🔧 Background Services:"
echo "• Queue worker: php artisan queue:work --tries=3"
echo "• WebSocket server: Configure Pusher or use Laravel WebSockets"
echo ""

# Optional: Start queue worker
read -p "Start queue worker now? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🔄 Starting queue worker..."
    php artisan queue:work --daemon &
    echo "Queue worker started in background"
fi

echo "🎉 TRI MULYO Queue Management System is ready!"
