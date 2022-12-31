build:
	docker-compose build --progress=plain

rebuild:
	docker-compose build --no-cache --progress=plain

up:
	docker-compose up -d
	docker-compose logs -f

down:
	docker-compose down -v

server:
	php artisan octane:start --host 0.0.0.0
