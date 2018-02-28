FreelanceMarket. Задание 4 для стажировки в МэдДевс

Конфигурация:

1. В .env файле задать данные своей mysql базы данных 
2. Запустить миграции: php artisan migrate
3. Создать Passport ключи: php artisan passport:install
4. Запустить сервер: 
    Разработческий сервер (самый быстрый способ протестить проект): php artisan serve
    Для Apache: 
        a) в httpd.conf указать Directory и DocumentRoot на public папку
        б) сменить владельца на Apache user-а
        в) прописать 755 доступ на storage директорию
    