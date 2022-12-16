build:
	docker-compose build --no-cache --progress=plain

up:
	docker-compose up -d
	docker-compose logs -f

down:
	docker-compose down -v
