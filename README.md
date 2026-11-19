<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Database setup

### 1. Prerequisites

Install PHP 8.4, Composer, and PostgreSQL 16. Use a local PostgreSQL login role that owns the application databases. Replace the example credentials with your local values.

### 2. Create the databases

Run this SQL as a PostgreSQL superuser. The application role does not need `CREATEDB`.

```sql
CREATE ROLE your_db_user LOGIN PASSWORD 'your_db_password';
CREATE DATABASE ecommerce OWNER your_db_user;
CREATE DATABASE ecommerce_test OWNER your_db_user;
```

### 3. Configure the environment

Set these values in `.env`. Set `APP_ENV=local` for development seeding.

```dotenv
APP_ENV=local
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecommerce
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 4. Build the schema and seed reference data

```bash
php artisan migrate
php artisan db:seed --class=ReferenceDataSeeder
```

### 5. Generate development data (optional, local only)

```bash
php artisan db:seed --class=DevelopmentDataSeeder
```

The seeder refuses to run if the `users` table already contains rows. To rebuild disposable development data, run:

```bash
php artisan migrate:fresh --seed
```

**Warning:** `migrate:fresh` deletes all tables in the database it targets. Never point it at a database with data you want to keep.

### 6. Run tests against the test database

```bash
php artisan test
```

PHPUnit defaults to `ecommerce_test`. Laravel feature tests use `RefreshDatabase`, and the test bootstrap rejects connections that do not target the PostgreSQL database `ecommerce_test` before any test migrations run.

### 7. Sample dataset reference

The schema matches the 1K PostgreSQL e-commerce sample from databasesample.com. The sample data is not stored in this repository. `ReferenceDataSeeder` seeds reference tables with values from that sample. `DevelopmentDataSeeder` generates development records, including orders, products, and users.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
