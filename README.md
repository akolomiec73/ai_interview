# Календарь собеседований (ai_calendar)
Веб-приложение для планирования и отслеживания собеседований. 
Календарь с возможностью добавлять события, просматривать их по дням и управлять записями.
Внедрение AI в будущем

## 🚀 Технологии
- Backend: Laravel 11 (PHP 8.2)
- Frontend: JavaScript (ES6+), Vite, Axios
- База данных: PostgreSQL 16
- Очереди: Redis
- Сервер: Nginx, PHP-FPM
- Контейнеризация: Docker, Docker Compose

## 🐳 Запуск через Docker
- Предварительные требования Установленные Docker и Docker Compose
- Настройте .env
- Соберите и запустите контейнеры `docker compose up -d --build`
- Миграции выполнятся автоматически
- Откройте приложение http://localhost:8080

## 📁 Структура проекта (основные папки)
* `app/`
  * `DTO/` Data Transfer Objects
  * `Http/` 
    * `Controllers/` Контроллеры
    * `Requests/` Валидация
  * `Jobs/`
    * `ParseVacancyJob.php` Парсинг вакансии
  * `Models/` Модели
  * `Repositories/` Репозитории (работа с БД)
  * `Services/` Бизнес-логика
* `docker/` Dockerfile, entrypoint.sh, nginx/default.conf
* `resources/`
  * `css/ ` Стили
  * `js/` Скрипты
  * `views/` Blade-шаблоны
* `docker-compose.yml` Оркестрация контейнеров
