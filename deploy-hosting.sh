#!/usr/bin/env bash

set -Eeuo pipefail

APP_ROOT="/www/wwwroot/taksu.smkn1mas.sch.id"

if [[ ! -f "$APP_ROOT/artisan" || ! -f "$APP_ROOT/.env" ]]; then
    printf 'Laravel atau .env tidak ditemukan di %s\n' "$APP_ROOT" >&2
    exit 1
fi

cd "$APP_ROOT"

php artisan down || true
trap 'php artisan up >/dev/null 2>&1 || true' EXIT

composer install --no-dev --optimize-autoloader --no-interaction
if ! command -v npm >/dev/null 2>&1; then
    printf 'Deploy dihentikan: npm wajib tersedia untuk build Tailwind lokal.\n' >&2
    exit 1
fi
npm ci --ignore-scripts
npm run build
if [[ ! -f public/build/manifest.json ]]; then
    printf 'Deploy dihentikan: public/build/manifest.json gagal dibuat.\n' >&2
    exit 1
fi
php artisan migrate --force
php artisan storage:link --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true

php artisan up
trap - EXIT

printf 'Deploy selesai. Storage: %s/public/storage -> %s/storage/app/public\n' "$APP_ROOT" "$APP_ROOT"
