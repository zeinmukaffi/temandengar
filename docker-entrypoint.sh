#!/usr/bin/env bash
set -e

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true
php artisan migrate --force

# Jalankan queue worker di latar belakang untuk memproses AI Hermes
php artisan queue:work --tries=1 --timeout=220 --sleep=3 &

# Jalankan web server Laravel di port Render
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}