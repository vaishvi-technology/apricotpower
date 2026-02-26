# Apricot Power - E-commerce Platform

A Laravel-based e-commerce application built with [Lunar PHP](https://lunarphp.io/) and Livewire, powered by Docker using Laravel Sail.

## Tech Stack

- **Framework:** Laravel 10/11
- **E-commerce:** Lunar PHP
- **Frontend:** Livewire
- **Payment:** Stripe
- **Search:** Meilisearch
- **Cache:** Redis
- **Storage:** AWS S3
- **Containerization:** Docker (Laravel Sail)

## Prerequisites

Before you begin, ensure you have the following installed:

- [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/get-started) & Docker Compose
- PHP 8.2+ (for initial composer install, or use Docker)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/vaishvi-technology/apricotpower.git
cd apricotpower
```

### 2. Configure Environment

Copy the example environment file and configure your settings:

```bash
cp .env.example .env
```

### 3. Install Dependencies

Run the following command to install PHP dependencies using Docker (no local PHP required):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Configure Laravel Sail

Install and configure Laravel Sail for your environment:

```bash
php artisan sail:install
```

When prompted, select the services you need (MySQL, Redis, Meilisearch, etc.).

### 5. Start the Application

Launch all Docker containers in detached mode:

```bash
./vendor/bin/sail up -d
```

### 6. Generate Application Key

Generate a unique application key for encryption:

```bash
./vendor/bin/sail artisan key:generate
```

### 7. Run Database Migrations

Create the database tables:

```bash
./vendor/bin/sail artisan migrate
```

### 8. Run Seeders

Run Seeders:

```bash
./vendor/bin/sail artisan db:seed
```

## Usage

Once installed, the application will be available at:

- **Application:** http://localhost
- **Meilisearch:** http://localhost:7700

## Troubleshooting

### Port Conflicts

If port 80 is already in use, modify `APP_PORT` in your `.env` file:

```env
APP_PORT=8080
```

### Permission Issues

If you encounter permission errors, ensure Docker has proper permissions:

```bash
sudo chown -R $USER:$USER .
```
