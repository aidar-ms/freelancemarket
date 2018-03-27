FreelanceMarket dockerized. Задание 4 для стажировки в МэдДевс в Докере


Для докера:

1. В .env файле задать данные своей mysql базы данных 
2. Запустить docker-compose build
3. Запустить миграции: docker exec freelancemarket php artisan migrate
4. Создать Passport ключи: docker exec freelancemarket php artisan passport:install
5. Запустить docker-compose up

Для dev сервера:

1. В .env файле задать данные своей mysql базы данных 
2. Запустить миграции: php artisan migrate
3. Создать Passport ключи: php artisan passport:install
4. Запустить php сервер: php artisan serve