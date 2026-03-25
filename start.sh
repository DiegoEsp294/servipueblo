#!/bin/bash
set -e

# Debug: mostrar vars de BD
echo "--- DB DEBUG ---"
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_DATABASE=$DB_DATABASE"
echo "DB_USERNAME=$DB_USERNAME"
echo "DATABASE_URL=$DATABASE_URL"
echo "----------------"

# Limpiar cualquier cache de config viejo bakeado en la imagen
php artisan config:clear
php artisan cache:clear

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Migraciones
php artisan migrate --force

# Cache de configuración/rutas/vistas
php artisan optimize

# Iniciar servidor
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
