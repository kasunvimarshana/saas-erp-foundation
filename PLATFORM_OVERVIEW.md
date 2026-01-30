# SaaS ERP Foundation Platform

## Overview

A production-ready, enterprise-grade, modular SaaS ERP platform built with Laravel 11 (backend) and Vue.js 3 (frontend), following Clean Architecture, SOLID principles, and best practices for scalability, security, and maintainability.

## Architecture

### Backend (Laravel 11)
- **Location**: `/backend`
- **Framework**: Laravel 11.x with PHP 8.3+
- **Architecture**: Clean Architecture + Modular Design
- **Pattern**: Controller → Service → Repository
- **Database**: Multi-tenant with tenant isolation
- **API**: RESTful with Laravel Sanctum authentication

### Frontend (Vue.js 3)
- **Location**: `/frontend`
- **Framework**: Vue.js 3.4+ with Composition API
- **Build Tool**: Vite 5
- **State Management**: Pinia 2
- **Routing**: Vue Router 4
- **Styling**: Tailwind CSS 3
- **Internationalization**: Vue i18n

## Core Features

### 1. Multi-Tenancy
- Strict tenant isolation using Stancl/Tenancy
- Tenant-aware authentication and authorization
- Support for single-database (tenant_id) and multi-database strategies
- Automatic tenant scoping via global scopes

### 2. Authentication & Authorization (IAM)
- Laravel Sanctum for API token management
- Spatie Laravel Permission for RBAC
- Fine-grained permissions and roles
- Tenant-aware policies
- Password reset and email verification

### 3. Modular Structure

#### Backend Modules (12)
1. **Auth** - Authentication and authorization
2. **Tenant** - Tenant management and onboarding
3. **User** - User management
4. **Role** - Role and permission management
5. **Permission** - Permission management
6. **Customer** - Customer relationship management
7. **Vehicle** - Vehicle and fleet management
8. **Inventory** - Stock and warehouse management
9. **Product** - Product catalog and variants
10. **Order** - Order processing and fulfillment
11. **Invoice** - Invoicing and billing
12. **Payment** - Payment processing and records

#### Frontend Modules (12)
1. **Auth** - Login, register, password recovery
2. **Dashboard** - Statistics and KPIs
3. **Tenant** - Tenant management
4. **User** - User administration
5. **Customer** - Customer management
6. **Vehicle** - Vehicle tracking
7. **Inventory** - Inventory management
8. **Product** - Product catalog
9. **Order** - Order management
10. **Invoice** - Invoice generation
11. **Payment** - Payment tracking
12. **Reports** - Analytics and reporting

### 4. Base Classes and Abstractions

#### Backend Base Classes
- **BaseModel**: Common model functionality with UUIDs, soft deletes, scopes
- **BaseRepository**: Standard CRUD operations with filtering and pagination
- **BaseService**: Transaction handling, validation, error management
- **BaseController**: RESTful API endpoints with consistent responses

#### Frontend Base Components
- **Layouts**: MainLayout, AuthLayout, DashboardLayout
- **Common Components**: Button, Input, Card, Modal, Table, Form
- **Composables**: useApi, useAuth, useToast, usePermissions

### 5. Security Features
- HTTPS enforcement (production ready)
- API rate limiting
- CSRF protection
- XSS protection
- SQL injection prevention via Eloquent ORM
- Mass assignment protection
- Secure password hashing (bcrypt)
- API token encryption
- Audit logging

### 6. API Documentation
- Swagger/OpenAPI integration via L5-Swagger
- Auto-generated API documentation
- Interactive API testing interface
- Versioned API endpoints

### 7. Internationalization (i18n)
- Multi-language support (EN, ES, FR included)
- Backend: Laravel localization
- Frontend: Vue i18n
- Shared language keys
- Dynamic language switching

## Directory Structure

```
saas-erp-foundation/
├── backend/                      # Laravel Backend
│   ├── app/
│   │   ├── Base/                # Base classes
│   │   │   ├── BaseController.php
│   │   │   ├── BaseModel.php
│   │   │   ├── BaseRepository.php
│   │   │   └── BaseService.php
│   │   └── Modules/             # Domain modules
│   │       ├── Auth/
│   │       ├── Tenant/
│   │       ├── User/
│   │       ├── Role/
│   │       ├── Permission/
│   │       ├── Customer/
│   │       ├── Vehicle/
│   │       ├── Inventory/
│   │       ├── Product/
│   │       ├── Order/
│   │       ├── Invoice/
│   │       └── Payment/
│   ├── database/
│   │   └── migrations/          # Database migrations
│   ├── routes/
│   │   ├── api.php             # API routes
│   │   └── web.php             # Web routes
│   ├── config/                  # Configuration files
│   └── storage/                 # File storage
│
├── frontend/                     # Vue.js Frontend
│   ├── src/
│   │   ├── modules/             # Feature modules
│   │   │   ├── Auth/
│   │   │   ├── Dashboard/
│   │   │   ├── Tenant/
│   │   │   ├── User/
│   │   │   ├── Customer/
│   │   │   ├── Vehicle/
│   │   │   ├── Inventory/
│   │   │   ├── Product/
│   │   │   ├── Order/
│   │   │   ├── Invoice/
│   │   │   ├── Payment/
│   │   │   └── Reports/
│   │   ├── components/          # Shared components
│   │   │   ├── layouts/
│   │   │   └── common/
│   │   ├── stores/              # Pinia stores
│   │   ├── router/              # Vue Router
│   │   ├── api/                 # API clients
│   │   ├── composables/         # Composition functions
│   │   ├── locales/             # i18n translations
│   │   ├── assets/              # Static assets
│   │   ├── App.vue              # Root component
│   │   └── main.js              # Entry point
│   ├── public/                  # Public assets
│   ├── dist/                    # Build output
│   └── package.json             # Dependencies
│
└── README.md                    # Project documentation
```

## Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP**: 8.3+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Cache**: Redis (recommended)
- **Queue**: Redis/Database
- **Authentication**: Laravel Sanctum
- **Multi-tenancy**: Stancl/Tenancy 3.x
- **Permissions**: Spatie Laravel Permission 6.x
- **API Docs**: L5-Swagger 10.x
- **Testing**: PHPUnit

### Frontend
- **Framework**: Vue.js 3.4+
- **Build Tool**: Vite 5.x
- **State**: Pinia 2.x
- **Router**: Vue Router 4.x
- **HTTP**: Axios 1.x
- **Styling**: Tailwind CSS 3.x
- **i18n**: Vue i18n 9.x
- **Testing**: Vitest (recommended)

### DevOps
- **Containerization**: Docker (optional)
- **CI/CD**: GitHub Actions (ready)
- **Code Quality**: Laravel Pint, ESLint
- **Version Control**: Git

## Getting Started

### Prerequisites
- PHP 8.3 or higher
- Composer 2.x
- Node.js 20.x or higher
- npm or yarn
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional but recommended)

### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=saas_erp
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Publish vendor assets
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Stancl\Tenancy\TenancyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# Generate Swagger documentation
php artisan l5-swagger:generate

# Start development server
php artisan serve
```

### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Configure API endpoint
cp .env.example .env
# Edit .env and set VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev

# Build for production
npm run build
```

### Access Points

- **Backend API**: http://localhost:8000/api
- **Swagger Docs**: http://localhost:8000/api/documentation
- **Frontend**: http://localhost:5173
- **Admin Panel**: http://localhost:5173/dashboard

## Development Workflow

### Creating a New Module

#### Backend Module

1. Create module structure:
```bash
cd backend/app/Modules
mkdir NewModule
cd NewModule
mkdir Models Repositories Services Http/Controllers Http/Requests Policies Events Listeners DTOs
```

2. Create model extending BaseModel
3. Create repository extending BaseRepository
4. Create service extending BaseService
5. Create controller extending BaseController
6. Add routes in `routes/api.php`
7. Create migration
8. Write tests

#### Frontend Module

1. Create module structure:
```bash
cd frontend/src/modules
mkdir NewModule
cd NewModule
mkdir views components store api
```

2. Create Vue components in `views/`
3. Create Pinia store in `store/`
4. Create API client in `api/`
5. Add routes to router
6. Create translations

### Code Standards

#### Backend
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write PHPDoc comments
- Use type hints and return types
- Follow SOLID principles
- Write unit and feature tests

#### Frontend
- Follow Vue.js 3 style guide
- Use Composition API
- Use TypeScript (optional)
- Write component tests
- Use ESLint for linting
- Follow Tailwind CSS conventions

## Testing

### Backend Testing
```bash
cd backend

# Run all tests
php artisan test

# Run specific test
php artisan test --filter UserTest

# Run with coverage
php artisan test --coverage
```

### Frontend Testing
```bash
cd frontend

# Run unit tests
npm run test

# Run e2e tests
npm run test:e2e

# Coverage report
npm run test:coverage
```

## Deployment

### Production Checklist

#### Backend
- [ ] Set `APP_ENV=production` in .env
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure production database
- [ ] Set up queue workers
- [ ] Configure caching (Redis recommended)
- [ ] Set up scheduled tasks (cron)
- [ ] Enable HTTPS
- [ ] Configure rate limiting
- [ ] Set up logging and monitoring
- [ ] Run `php artisan optimize`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`

#### Frontend
- [ ] Set production API URL in .env
- [ ] Run `npm run build`
- [ ] Configure CDN for assets
- [ ] Enable HTTPS
- [ ] Set up monitoring
- [ ] Configure error tracking
- [ ] Optimize images
- [ ] Enable gzip/brotli compression

### Docker Deployment (Optional)

```bash
# Build containers
docker-compose build

# Start services
docker-compose up -d

# Run migrations
docker-compose exec backend php artisan migrate

# Stop services
docker-compose down
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `POST /api/password/email` - Send password reset
- `POST /api/password/reset` - Reset password

### Tenants
- `GET /api/tenants` - List tenants
- `POST /api/tenants` - Create tenant
- `GET /api/tenants/{id}` - Get tenant
- `PUT /api/tenants/{id}` - Update tenant
- `DELETE /api/tenants/{id}` - Delete tenant

### Users
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/{id}` - Get user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Customers
- `GET /api/customers` - List customers
- `POST /api/customers` - Create customer
- `GET /api/customers/{id}` - Get customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

*[Full API documentation available at /api/documentation]*

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.

## Support

For support, please contact the development team or open an issue in the repository.

## Roadmap

### Phase 1: Foundation (Current)
- ✅ Laravel backend scaffolding
- ✅ Vue.js frontend scaffolding
- ✅ Multi-tenancy setup
- ✅ Authentication and authorization
- ✅ Base modules structure

### Phase 2: Core Modules (Next)
- [ ] Complete CRUD implementations
- [ ] Advanced filtering and search
- [ ] File upload handling
- [ ] Email notifications
- [ ] Audit logging

### Phase 3: ERP Features
- [ ] Inventory management (FIFO/FEFO)
- [ ] Purchase orders and procurement
- [ ] Sales and invoicing
- [ ] Payment processing
- [ ] Reporting and analytics

### Phase 4: Advanced Features
- [ ] Real-time notifications
- [ ] WebSocket support
- [ ] Advanced reporting
- [ ] Data import/export
- [ ] Third-party integrations

### Phase 5: Optimization
- [ ] Performance optimization
- [ ] Caching strategies
- [ ] Query optimization
- [ ] Asset optimization
- [ ] CDN integration

## Acknowledgments

- Laravel Framework
- Vue.js Framework
- Stancl/Tenancy
- Spatie Laravel Permission
- Tailwind CSS
- And all other open-source contributors
