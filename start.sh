#!/bin/bash
set -e

# Limpiar cualquier cache de config viejo bakeado en la imagen
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Migraciones y seeder del admin
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan tinker --execute="App\Models\Worker::all()->each->recalculateRating();" 2>/dev/null || true

# Cache de configuración/rutas/vistas
php artisan optimize

# Iniciar servidor
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
