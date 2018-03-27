FreelanceMarket dockerized. Задание 4 для стажировки в МэдДевс в Докере

Чтобы работало:

1. В .env файле задать данные своей mysql базы данных 
2. Запустить docker-compose up --build
2. Запустить миграции: docker exec freelancemarket php artisan migrate
    