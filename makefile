
args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-}`

# Проводит миграцию
migrate:
	docker exec php-fpm php artisan migrate

# Проводит миграцию, которая пересоздаст все таблицы
migrate_fresh:
	docker exec php-fpm php artisan migrate:fresh

# Проводит миграцию, которая пересоздает все таблицы и создает начальный данные в бдд
migrate_seed:
	docker exec php-fpm php artisan migrate:fresh
	docker exec php-fpm php artisan db:seed

# Создание начальных данных в бд
seed:
	docker exec php-fpm php artisan db:seed

# Создание модели в параметр передается название ()
create_model:
	docker exec php-fpm php artisan make:model $(call args)

# Создание миграции в параметр передается название ()
create_migrate:
	docker exec php-fpm php artisan make:migration $(call args)

# Создание ресурса в параметр передается название ()
create_resource:
	docker exec php-fpm php artisan make:resource $(call args)

# Создание запроса в параметр передается название ()
create_request:
	docker exec php-fpm php artisan make:request $(call args)

# Создание миграции модели и контроллера для API в параметр передается название ()
create_api_model_migration_controller:
	docker exec php-fpm php artisan make:model $(call args) --api -m

# Выодит шпаргалку по artisan
create_web_controller:
	docker exec php-fpm php artisan make:controller $(call args) --resource

artisan_help:
	docker exec php-fpm php artisan

# Выводит список зарегистрованных путей сайта и API
artisan_list_route:
	docker exec php-fpm php artisan route:list

# Останавлеивает docker
docker_stop:
	docker-compose -f "docker-compose.yml" stop

# Запускает докер в фоновом режиме
docker_start_demon:
	docker-compose -f "docker-compose.yml" start

# Собирает docker и запускает в фоновом режиме
docker_build_start_demon:
	docker-compose up --build -d

docker_chmod_start_demon:
	sudo chmod 666 /var/run/docker.sock
	docker-compose up --build -d