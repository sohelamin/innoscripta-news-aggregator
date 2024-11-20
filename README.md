# Innoscripta News Aggregator

This Laravel application aggregates news from multiple sources.

## Prerequisites

- PHP >= 8.2
- Composer
- MySQL
- Redis
- Node.js & NPM (for frontend assets)
- Supervisor
- Swagger

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/sohelamin/inscripta-task.git
cd inscripta-task
```

### 2. Build and start Docker containers

```bash
docker-compose up -d
```

### 3. Install Dependencies

```bash
docker-compose exec app bash
composer install
npm install
```

### 4. Set Up Environment Variables

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Run database migrations and seed

```bash
php artisan migrate --seed
```

### 6. Serve the Application (Locally)

```bash
php artisan serve
```

## API Documentation

Swagger documentation is available at:

Development: http://localhost:8000/api/documentation
Production: <your-production-url>/api/documentation

To generate/update Swagger docs:
```bash
php artisan l5-swagger:generate
```
