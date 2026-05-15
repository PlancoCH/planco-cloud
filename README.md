# Planco Cloud (Backend) 
<img width="200" height="200" alt="image" src="https://github.com/user-attachments/assets/20afdb34-3846-465b-899d-d5a00fcc7e1d" />

| Metadata | Information |
| --- | --- |
| **Organization** | Planco |
| **Project Status** | 🔴 Alpha |
| **Primary Owner** | @TobiasClausen |
| **Primary Tech Stack** | Laravel, Postgres |
| **CI/CD Status** | NOT SETUP |

---

## Purpose

This repository contains the backend service for the Planco platform implemented in Laravel. It provides API endpoints, user management, authentication, database migrations, and frontend asset tooling via Vite.

## Description

Planco Cloud Backend is a Laracel-based REST API built with modern web frameworks and 
PostgreSQL database. It manages user accounts, authentication, authorization, and 
provides machine learning functionalities for predictive analytics and intelligent features. 
The backend is designed to be scalable, maintainable, and secure, serving multiple client 
applications (web, mobile, etc.).

### Key Components

- **User Management:** Registration, authentication, profiles, roles, and permissions
- **Authentication & Authorization:** Token-based auth (Sanctum), session management, RBAC
- **Database:** MySQL/Postgres with Eloquent ORM
- **API Layer:** RESTful endpoints for client applications
- **Frontend Assets:** Vite (Node) for building JS/CSS
- **Background Jobs:** Laravel queues for async tasks

## Prerequisites

Before you begin, ensure you have these installed:

- PHP 8.1+ with required extensions
- Composer
- Node.js 16+ and npm (or pnpm/yarn)
- A supported database: MySQL, MariaDB, or PostgreSQL
- Git

## Installation & Setup

```bash
# Clone the repository
git clone git@github.com:PlancoCH/planco-cloud.git
cd planco-cloud

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Configure database settings in .env

# Run database migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed

# Install frontend dependencies and build assets (for local dev)
npm install
npm run dev

# Start the development server
php artisan serve --host=127.0.0.1 --port=8000
```

Access the application at http://127.0.0.1:8000

## Environment

Edit `.env` to configure the database and other settings. Common vars:

```
APP_NAME=Planco
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=planco
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

## Running Tests

Run PHP unit tests with PHPUnit:

```bash
./vendor/bin/phpunit
```

## Common Artisan Commands

- Clear config cache: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`
- Run migrations: `php artisan migrate`
- Fresh migrate + seed: `php artisan migrate:fresh --seed`

## Frontend (Vite)

Development:

```bash
npm run dev
```

Build for production:

```bash
npm run build
```

## Support & Contact

For questions or issues, contact the maintainers:

- **Primary Contact:** Tobias Clausen (@TobiasClausen)
- **Team Chat:** #planco

---

## License & Ownership

**Copyright © 2026 Planco. All rights reserved.**

This software and all associated files are the exclusive property of **Planco**. Unauthorized copying, distribution, or modification of this file, via any medium, is strictly prohibited. This code is proprietary and confidential. Any use of this material without express written permission from Planco is prohibited.