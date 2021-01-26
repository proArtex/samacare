# Running the project
* run `docker-compose up --build --force-recreate -d`
* run `docker-compose exec app bin/console doctrine:migrations:migrate -n`
* run `docker-compose exec app bin/console doctrine:fixtures:load -n`
* go to `http://127.0.0.1:8888/` (make sure it is http, not https)
* run `docker-compose exec app bin/console debug:route` to get the list of endpoints

# Authorization
Use Bearer token authorization with one onf the following predefined values (users)

* user 1: `sadasdGJHVhgcgfxnbKTY561`
* user 2: `sadasdGJHVhgcgfxnbKTY562`
* user 3: `sadasdGJHVhgcgfxnbKTY563`