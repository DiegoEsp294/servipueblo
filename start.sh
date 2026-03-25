#!/bin/bash
set -e

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Migraciones
php artisan migrate --force

# Cache de configuración/rutas/vistas
php artisan optimize

# Iniciar servidor
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
