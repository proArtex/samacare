# Setting up the project
* run `docker-compose up --build --force-recreate -d`
* run `docker-compose exec app bin/console doctrine:migrations:migrate -n`
* run `docker-compose exec app bin/console doctrine:fixtures:load -n`

# Authorization
Use Bearer token authorization with one onf the following predefined values (users)

* user 1: `sadasdGJHVhgcgfxnbKTY561`
* user 2: `sadasdGJHVhgcgfxnbKTY562`
* user 3: `sadasdGJHVhgcgfxnbKTY563`

# Endpoints
Go to `http://127.0.0.1:8888/` to check if it works (make sure it is http, not https).  
Run `docker-compose exec app bin/console debug:route` to get the list of endpoints.

## Publisher's endpoints
* Write new tweets
```
curl --location --request POST 'http://127.0.0.1:8888/api/tweets' \
--header 'Authorization: Bearer sadasdGJHVhgcgfxnbKTY561' \
--header 'Content-Type: application/json' \
--data-raw '{
    "message": "message goes here"
}'
```

* Reply to existing tweets
```
curl --location --request POST 'http://127.0.0.1:8888/api/tweet-replies' \
--header 'Authorization: Bearer sadasdGJHVhgcgfxnbKTY562' \
--header 'Content-Type: application/json' \
--data-raw '{
    "tweetId": 1,
    "message": "reply goes here"
}'
```

## Reader's endpoints
* Query all tweets in the system
```
curl --location --request GET 'http://127.0.0.1:8888/api/tweets' \
--header 'Content-Type: application/json'
```

* Query all tweets by a group of publishers
```
curl --location -g --request GET 'http://127.0.0.1:8888/api/tweets?filter[authors]=1,2,3' \
--header 'Content-Type: application/json'
```

* Query all tweets in a specific window of time
```
curl --location -g --request GET 'http://127.0.0.1:8888/api/tweets?filter[start]=2021-01-25T14:37:38.669Z&filter[end]=2021-01-26T14:37:38.669Z' \
--header 'Content-Type: application/json'
```

* Query a tweet and all the responses to this tweet
```
curl --location --request GET 'http://127.0.0.1:8888/api/tweets/1' \
--header 'Content-Type: application/json'
```