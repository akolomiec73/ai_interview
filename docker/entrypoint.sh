#!/bin/sh
set -e

echo "Ожидание PostgreSQL..."
until php -r "try { new PDO('pgsql:host=db;port=5432;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo '✅ Соединение с БД установлено'; } catch (PDOException \$e) { echo '⏳ БД не готова...'; exit(1); }" 2>/dev/null; do
    sleep 1
done

echo "🔄 Выполнение миграций..."
php artisan migrate --force

echo "🚀 Запуск PHP-FPM..."
exec "$@"
