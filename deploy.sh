#!/bin/bash
set -e
cd ~/sipena

echo "→ Pull kode terbaru"
git pull origin main

echo "→ Install dependency"
composer install --no-dev --optimize-autoloader

echo "→ Migrasi database"
php artisan migrate --force

echo "→ Optimasi cache"
php artisan optimize

echo "✓ Deploy selesai"