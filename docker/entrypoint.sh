#!/bin/sh
set -e
cd /var/www/html

# Apache écoute sur le port fourni par l'hébergeur (PORT), 80 par défaut.
PORT="${PORT:-80}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Dossier des photos : volume persistant monté sur public/storage.
mkdir -p public/storage/eleves/signatures
chown -R www-data:www-data public/storage storage bootstrap/cache

# Photos et signatures initiales (copiées une seule fois si le volume est vide).
if [ -d /opt/seed-storage ] && [ -z "$(ls -A public/storage/eleves 2>/dev/null | grep -v '^signatures$')" ]; then
    cp -a /opt/seed-storage/. public/storage/
    chown -R www-data:www-data public/storage
fi

if [ -n "$APP_KEY" ]; then
    php artisan migrate --force || echo "ATTENTION : migrate a échoué"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
