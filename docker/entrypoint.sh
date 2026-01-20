#!/bin/sh
set -e

# Ajustar permisos en runtime PRIMERO (antes de cualquier otra cosa)
echo "Adjusting permissions for storage and bootstrap/cache..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Crear archivo laravel.log si no existe
touch /var/www/html/storage/logs/laravel.log 2>/dev/null || true
chown www-data:www-data /var/www/html/storage/logs/laravel.log 2>/dev/null || true
chmod 664 /var/www/html/storage/logs/laravel.log 2>/dev/null || true

# Función auxiliar para setear variables en .env desde variables de entorno
set_env_in_file() {
  file=$1
  key=$2
  val=$(printenv "$key")
  if [ -n "$val" ]; then
    if grep -q "^${key}=" "$file"; then
      sed -i "s#^${key}=.*#${key}=${val}#" "$file"
    else
      echo "${key}=${val}" >> "$file"
    fi
  fi
}

# Instalar vendor si no existe (útil con bind mounts locales)
if [ ! -d "/var/www/html/vendor" ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Copiar .env.example a .env y generar APP_KEY si no existe
if [ -f /var/www/html/artisan ] && [ ! -f /var/www/html/.env ]; then
  echo "Creating .env from .env.example..."
  if [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
  else
    touch /var/www/html/.env
  fi

  # Inyectar variables importantes desde el entorno (si están definidas)
  set_env_in_file /var/www/html/.env DB_CONNECTION
  set_env_in_file /var/www/html/.env DB_HOST
  set_env_in_file /var/www/html/.env DB_PORT
  set_env_in_file /var/www/html/.env DB_DATABASE
  set_env_in_file /var/www/html/.env DB_USERNAME
  set_env_in_file /var/www/html/.env DB_PASSWORD
  set_env_in_file /var/www/html/.env APP_ENV
  set_env_in_file /var/www/html/.env APP_DEBUG
  set_env_in_file /var/www/html/.env APP_URL
  set_env_in_file /var/www/html/.env APP_KEY

  # Generar clave si APP_KEY sigue vacía
  if ! grep -q "^APP_KEY=.\+" /var/www/html/.env || [ -z "$(grep '^APP_KEY=' /var/www/html/.env | cut -d'=' -f2)" ]; then
    echo "Generating APP_KEY..."
    php /var/www/html/artisan key:generate || true
  fi
fi

# Validar que APP_KEY existe en .env (crítico para Laravel)
if ! grep -q "^APP_KEY=base64:.\+" /var/www/html/.env 2>/dev/null; then
  echo "WARNING: APP_KEY not properly set in .env, attempting to generate..."
  php /var/www/html/artisan key:generate --force || true
fi

# Ejecutar migraciones si se pasa la variable de entorno RUN_MIGRATIONS=true
if [ "$RUN_MIGRATIONS" = "true" ] && [ -f /var/www/html/artisan ]; then
  echo "Running database migrations..."
  tries=0
  until php /var/www/html/artisan migrate --force; do
    tries=$((tries+1))
    if [ $tries -ge 15 ]; then
      echo "Migrations failed after $tries attempts"
      break
    fi
    echo "Waiting for database to be ready (attempt: $tries). Retrying in 3s..."
    sleep 3
  done
fi

# Ajuste final de permisos antes de iniciar php-fpm
echo "Final permission check..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "Starting php-fpm..."
# Ejecutar php-fpm en primer plano
php-fpm
