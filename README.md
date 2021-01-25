# Running the project
* run `docker-compose up --build --force-recreate -d`
* run `docker-compose exec app bin/console doctrine:migrations:migrate -n`
* go to `http://127.0.0.1:8888/` (make sure it is http, not https)