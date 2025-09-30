<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# 🏥 TRI MULYO Queue Management System

A comprehensive queue management system built with Laravel 12, designed specifically for healthcare facilities. This system provides real-time queue management, voice announcements, thermal printing, and role-based access control.

## ✨ Features

### 🎯 Core Functionality

-   **Real-time Queue Management** - Live updates across all interfaces
-   **Voice Announcements** - Browser-native speech synthesis for patient calls
-   **Thermal Printing** - USB/Bluetooth printer integration for queue tickets
-   **Multi-Service Support** - Handle different services (General, Pharmacy, Lab, etc.)
-   **Queue Recall System** - Recall skipped patients
-   **Daily Auto-Reset** - Queue numbers reset daily at 00:00 WIB

### 👥 User Roles & Access

-   **Admin** - Full system management, FilamentPHP admin panel
-   **Staff/Receptionist** - Create queues, call patients, manage queue flow
-   **Doctor/Nurse** - Monitor queue status, view patient information
-   **Display** - Public read-only access for display screens

### 🎨 User Experience

-   **Responsive Design** - Mobile-first approach, works on all devices
-   **TRI MULYO Branding** - Custom color scheme and professional design
-   **Public Display Screen** - TV/kiosk friendly interface
-   **Multi-language Support** - Indonesian voice announcements
-   **Real-time Updates** - WebSocket-powered live synchronization

## 🛠️ Technical Architecture

### Backend Stack

-   **Laravel 12** - Modern PHP framework
-   **SQLite Database** - Lightweight, file-based database
-   **Livewire v3** - Dynamic frontend components
-   **Spatie Permissions** - Role-based access control
-   **Laravel Queues** - Background job processing
-   **Laravel Events** - Real-time broadcasting

### Frontend Stack

-   **Blade Templates** - Server-side rendering
-   **Tailwind CSS v4** - Utility-first styling
-   **Laravel Echo** - WebSocket client
-   **SpeechSynthesis API** - Voice announcements
-   **Vite** - Modern asset building

### Third-party Integrations

-   **FilamentPHP** - Admin panel interface
-   **Pusher/Laravel WebSockets** - Real-time broadcasting
-   **Thermal Printer Support** - ESC/POS command integration

## 📋 Installation

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   SQLite extension

### Quick Setup

```bash
# Clone repository
git clone <repository-url>
cd queue-app

# Run deployment script
./deploy.sh
```

### Manual Installation

```bash
# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

## 🚀 Usage

### Default Access Points

-   **Public Display**: `http://localhost:8000/`
-   **Staff Dashboard**: `http://localhost:8000/staff`
-   **Admin Panel**: `http://localhost:8000/admin`

### Default Users

```
Admin: admin@trimulyo.com / password123
Staff: staff@trimulyo.com / password123
```

### Queue Workflow

1. **Patient Registration** - Staff creates queue entry
2. **Ticket Printing** - Automatic thermal printer output
3. **Queue Display** - Real-time updates on public screens
4. **Patient Calling** - Staff calls patients with voice announcements
5. **Status Management** - Mark as done, skip, or recall patients

## 🔧 Configuration

### Broadcasting Setup

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

### Queue Configuration

-   Queue numbering resets daily at 00:00 WIB (GMT+7)
-   Per-service counter system (GEN-001, PHR-001, etc.)
-   Automatic database cleanup

### Printer Configuration

-   Supports USB and Bluetooth thermal printers
-   ESC/POS command formatting
-   Automatic fallback if printer unavailable

## 🧪 Testing

Run the comprehensive test suite:

```bash
php artisan test
```

### Test Coverage

-   ✅ Queue Service Logic
-   ✅ User Authentication & Authorization
-   ✅ Staff Dashboard Functionality
-   ✅ Role-based Access Control
-   ✅ Queue Numbering System
-   ✅ Daily Reset Functionality

## 📁 Project Structure

```
app/
├── Events/              # Broadcasting events
├── Http/
│   ├── Controllers/     # Request handlers (*Handler naming)
│   ├── Livewire/       # Interactive components
│   └── Requests/       # Form validation
├── Models/             # Eloquent models
├── Policies/           # Authorization policies
└── Services/           # Business logic

resources/
├── js/                 # Frontend JavaScript
├── views/
│   ├── components/     # Blade components
│   ├── livewire/      # Livewire templates
│   └── layouts/       # Page layouts

database/
├── factories/          # Test data factories
├── migrations/         # Database schema
└── seeders/           # Sample data
```

## 🎯 Key Design Principles

### Naming Conventions

-   Controllers: `*Handler` suffix
-   Database tables: `snake_case` plural
-   Models: `StudlyCase` singular
-   Code formatting: 2-space indentation, PSR-12 compliant

### Security Implementation

-   CSRF protection on all forms
-   Input validation & sanitization
-   Role-based route protection
-   Secure password hashing

### Performance Features

-   Database query optimization
-   Asset optimization with Vite
-   Efficient WebSocket usage
-   Background job processing

## 🔌 API & Broadcasting

### Real-time Events

-   `QueueCreated` - New patient added
-   `QueueCalled` - Patient called to counter
-   `QueueRecalled` - Skipped patient recalled

### WebSocket Channels

-   `queues` - Global queue updates
-   Real-time synchronization across all interfaces

## 📱 Mobile Responsiveness

-   **Mobile-first design** - Optimized for smartphones and tablets
-   **Touch-friendly interfaces** - Large buttons and clear navigation
-   **Responsive layouts** - Adapts to all screen sizes
-   **PWA capabilities** - Can be installed as web app

## 🔊 Voice Announcements

-   **Browser-native synthesis** - No external dependencies
-   **Indonesian language support** - Native voice synthesis
-   **Customizable messages** - Configurable announcement templates
-   **Fallback mechanisms** - Graceful degradation if unavailable

## 🖨️ Printer Integration

### Supported Printers

-   USB thermal printers
-   Bluetooth thermal printers
-   ESC/POS compatible devices

### Print Features

-   Automatic ticket generation
-   Clinic branding
-   Queue information
-   Date/time stamps
-   Error handling & logging

## 📊 System Monitoring

### Logging & Monitoring

-   Queue creation tracking
-   Printer status monitoring
-   User activity logging
-   Performance metrics
-   Error reporting

### Admin Dashboard Features

-   Service management
-   User role assignment
-   System statistics
-   Queue analytics
-   Printer status

## 🔄 Background Jobs

### Queue Processing

```bash
# Start queue worker
php artisan queue:work --tries=3 --timeout=60

# Monitor queues
php artisan queue:monitor
```

### Scheduled Tasks

-   Daily queue number reset
-   Cleanup old queue records
-   Generate daily reports
-   System maintenance

## 🌐 Deployment

### Production Checklist

-   [ ] Change default passwords
-   [ ] Configure proper database
-   [ ] Set up SSL certificates
-   [ ] Configure broadcasting service
-   [ ] Set up queue workers
-   [ ] Configure printer connections
-   [ ] Test voice announcements
-   [ ] Verify all user roles

### Server Requirements

-   Web server (Apache/Nginx)
-   PHP 8.2+ with required extensions
-   Queue worker process
-   WebSocket server (optional)
-   Printer drivers (for local printing)

## 🤝 Contributing

This system follows Laravel best practices and includes:

-   Comprehensive test coverage
-   Clean architecture patterns
-   Detailed documentation
-   Code quality standards

## 📄 License

This project is licensed under the MIT License.

## 🏥 About TRI MULYO

This queue management system is specifically designed for TRI MULYO healthcare facility, incorporating their branding, workflow requirements, and operational needs.

---

**Built with ❤️ using Laravel 12, Livewire, and modern web technologies.**
