# Contest Participation System (Laravel)

A backend system that manages user contests, scoring, leaderboards, and prizes.

## 🚀 Features
- Role-based access (Admin, VIP, Normal, Guest)
- Contest & Question Management
- Participation & Scoring
- Leaderboard (with pagination)
- Prize Auto Awarding
- REST APIs with Postman Docs
- Rate Limiting & Validation
- Optional Blade Leaderboard UI

## 🧰 Tech Stack
Laravel 10, Sanctum, MySQL/PostgreSQL, Redis (optional), Tailwind, Postman

## ⚙️ Setup Instructions
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
