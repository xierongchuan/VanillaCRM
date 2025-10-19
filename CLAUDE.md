# VanillaCRM Project Information for Claude Code

## Project Overview
VanillaCRM is a Laravel-based Customer Relationship Management system built with PHP 8.4 and modern web technologies. This project serves as a comprehensive CRM solution with expense management, user role management, and reporting capabilities.

## Technology Stack
- **Backend**: Laravel 12 with PHP 8.4
- **Frontend**: Vite with Bootstrap 5.3.2, jQuery 3.7.1
- **Database**: MariaDB/MySQL
- **Testing**: PHPUnit 11.0 with Pest 3.8
- **Build Tools**: Composer, NPM, Docker
- **Additional**: Laravel Sanctum for authentication, PhpSpreadsheet for Excel exports

## Development Environment

### Local Development
```bash
# Start development server (runs PHP artisan serve + Vite dev server)
npm run dev

# Start only Vite build process
npm run build

# Run Laravel development server
php artisan serve
```

### Docker Environment
The project includes Docker configuration with:
- **MariaDB** container for database
- **phpMyAdmin** for database management (port 8080)
- **PHP-FPM** container for Laravel application
- **Nginx** reverse proxy (port 8002)

```bash
# Start Docker containers
docker-compose up -d

# Access the application
# Application: http://localhost:8002
# phpMyAdmin: http://localhost:8080
```

## Key Features & Integrations
- **Expense Management**: Integration with VanillaFlow API for expense request management
- **User Role Management**: Comprehensive permission and role system
- **Reporting**: Cashier reports with XLSX export functionality
- **Authentication**: Laravel Sanctum for API authentication
- **File Processing**: Excel/Spreadsheet generation and parsing capabilities

## Database Configuration
- **Connection**: MySQL/MariaDB
- **Testing Database**: vanilla_crm_test
- **Migration Commands**: Standard Laravel migration commands available
- **Models**: User, Report, Permission, Field, Post, and other domain-specific models

## Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
./vendor/bin/phpunit

# Testing database config:
# - Database: vanilla_crm_test
# - Connection: MySQL
# - Cache/Session: Array drivers for testing
```

## Code Quality Tools
- **PHP CS Fixer**: Code formatting and style
- **PHP CodeSniffer**: Code quality checks
- **Laravel Pint**: Additional code formatting

## Environment Configuration
Key environment variables:
- `VANILLAFLOW_API_URL`: External API integration endpoint
- `VANILLAFLOW_API_TOKEN`: Authentication token for external API
- Standard Laravel environment variables for database, mail, etc.

## Project Structure Notes
- No custom Artisan commands found (uses standard Laravel commands)
- Feature and Unit tests organized in standard Laravel structure
- Bootstrap frontend components with jQuery dependencies
- Docker-based deployment with automated migrations
- Russian language comments in Docker configuration

## Development Commands
When making changes to this codebase, always run:
```bash
# Run tests to ensure nothing is broken
php artisan test

# Run code quality checks
./vendor/bin/php-cs-fixer fix
./vendor/bin/phpcs

# Clear caches if needed
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

Issue to solve: undefined
Your prepared branch: issue-15-1e450333
Your prepared working directory: /tmp/gh-issue-solver-1760901670847
Your forked repository: konard/VanillaCRM
Original repository (upstream): xierongchuan/VanillaCRM

Proceed.