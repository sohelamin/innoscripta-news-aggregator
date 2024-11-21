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

## Supervisor

To update supervisor config:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start new-job
```

## Scheduler

Add the scheduler command in cronjob:
```bash
* * * * * docker exec innoscripta_news_aggregator_app php /var/www/artisan schedule:run >> /dev/null 2>&1
```

## API Documentation

Swagger documentation is available at:

Development: http://localhost:8000/api/documentation
Production: <your-production-url>/api/documentation

To generate/update Swagger docs:
```bash
php artisan l5-swagger:generate
```

## Testing

Run tests with PHPUnit:
```bash
php artisan test
```
