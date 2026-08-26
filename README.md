# FatherShope — POS System

A full-stack Point of Sale system built with **Laravel 12** (API) + **React 19** (SPA).

## Tech Stack

| Layer | Tech |
|-------|------|
| Backend | Laravel 12, PHP 8.2+, Sanctum SPA Auth |
| Frontend | React 19, Vite 6, Tailwind CSS 4, React Router 7 |
| Database | SQLite (dev) / MySQL (prod) |

## Quick Start

### Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve          # http://localhost:8000
```

### Frontend

```bash
cd frontend
npm install
npm run dev                # http://localhost:5173
```

### Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Manager | admin@fatherShope.com | password |
| Cashier | cashier@fatherShope.com | password |

## Features

- **Auth**: Sanctum SPA cookie-based login/logout (no registration)
- **Dashboard**: Today's revenue/transactions, product count, low stock alerts
- **Products**: Full CRUD, search, pagination, soft deactivate
- **Sales (POS)**: Create sale with cart, stock validation, transaction locking, tax calculation
- **Users**: Manager-only user creation (manager/cashier roles)
- **RBAC**: Manager = full access, Cashier = sales + product view only

## Project Structure

```
FatherShope/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Auth, Product, Sale, User, Dashboard
│   │   ├── Middleware/      # EnsureUserIsActive
│   │   ├── Requests/       # Form validation
│   │   └── Resources/      # API response formatting
│   ├── Models/             # User, Product, Sale, SaleItem
│   ├── Policies/           # ProductPolicy
│   └── Services/           # SaleService (transaction + locking)
├── database/
│   ├── factories/          # User, Product, Sale, SaleItem
│   └── seeders/            # Admin + cashier + products + sales
├── routes/api.php          # 20 API routes
├── tests/Feature/          # 23 tests
└── frontend/               # React SPA (separate directory)
    └── src/
        ├── api/            # Axios + API modules
        ├── components/     # Layout + UI components
        ├── context/        # AuthContext
        └── pages/          # Login, Dashboard, Products, Sales, Users
```

## API Endpoints

| Method | Endpoint | Auth | Role |
|--------|----------|------|------|
| POST | /api/auth/login | No | — |
| POST | /api/auth/logout | Yes | Any |
| GET | /api/auth/me | Yes | Any |
| GET | /api/dashboard/stats | Yes | Manager |
| GET | /api/dashboard/inventory-alerts | Yes | Manager |
| GET/POST | /api/products | Yes | Any / Manager |
| GET/PUT/DELETE | /api/products/{id} | Yes | Any / Manager |
| POST | /api/sales | Yes | Any |
| GET | /api/sales | Yes | Any |
| GET | /api/sales/{id} | Yes | Any |
| GET/POST | /api/users | Yes | Manager |

## Running Tests

```bash
php artisan test           # 23 tests, 57 assertions
```

## License

MIT
