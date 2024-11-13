# Innoscripta News Aggregator

This Laravel application aggregates news from multiple sources.

## Prerequisites

- PHP >= 8.0
- Composer
- MySQL
- Redis
- Node.js & NPM (for frontend assets)

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/sohelamin/inscripta-task.git
cd inscripta-task
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Set Up Environment Variables

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. Serve the Application (Locally)

```bash
php artisan serve
```
