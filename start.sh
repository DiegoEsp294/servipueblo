#!/bin/bash
set -e

# Limpiar cualquier cache de config viejo bakeado en la imagen
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Descargar fuentes TTF para generación de imágenes
FONT_DIR="/tmp/sp-fonts"
mkdir -p "$FONT_DIR"
if [ ! -f "$FONT_DIR/Regular.ttf" ]; then
    curl -sL "https://github.com/google/fonts/raw/main/apache/roboto/static/Roboto-Regular.ttf" -o "$FONT_DIR/Regular.ttf" || true
fi
if [ ! -f "$FONT_DIR/Bold.ttf" ]; then
    curl -sL "https://github.com/google/fonts/raw/main/apache/roboto/static/Roboto-Bold.ttf" -o "$FONT_DIR/Bold.ttf" || true
fi

# Migraciones y seeder del admin
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force

# Cache de configuración y vistas (route:cache se omite porque hay rutas con closures)
php artisan config:cache
php artisan view:cache

# Limpiar cache de tarjetas de compartir (se regeneran con nuevas fuentes)
php artisan tinker --execute="Storage::deleteDirectory('workers/share-cards');" 2>/dev/null || true

# Generar sitemap estático
php artisan sitemap:generate

# Iniciar servidor en background, esperar que arranque, calentar el sitemap en Cloudflare
php artisan serve --host=0.0.0.0 --port=${PORT:-10000} &
SERVER_PID=$!

# Esperar que el servidor esté listo
sleep 5
curl -s "http://localhost:${PORT:-10000}/sitemap.xml" -o /dev/null || true

# Traer el proceso al frente
wait $SERVER_PID
