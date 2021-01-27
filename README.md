# Setting up the project
* run `docker-compose up --build --force-recreate -d`
* run `docker-compose exec app bin/console doctrine:migrations:migrate -n`
* run `docker-compose exec app bin/console doctrine:fixtures:load -n`

# Authorization
Use Bearer token authorization with one onf the following predefined values (users)

* user 1: `Random_Token_For_User_01`
* user 2: `Random_Token_For_User_02`
* user 3: `Random_Token_For_User_03`

# Endpoints
Go to `http://127.0.0.1:8888/` to check if it works (make sure it is http, not https).  
Run `docker-compose exec app bin/console debug:route` to get the list of endpoints.

## Publisher's endpoints
* Write new tweets
```
curl --location --request POST 'http://127.0.0.1:8888/api/tweets' \
--header 'Authorization: Bearer Random_Token_For_User_01' \
--header 'Content-Type: application/json' \
--data-raw '{
    "message": "message goes here"
}'
```

* Reply to existing tweets
```
curl --location --request POST 'http://127.0.0.1:8888/api/tweets/1/reply' \
--header 'Authorization: Bearer Random_Token_For_User_02' \
--header 'Content-Type: application/json' \
--data-raw '{
    "message": "reply goes here"
}'
```

* Mark some of their tweets as private, except to their followers
```
curl --location --request PATCH 'http://127.0.0.1:8888/api/tweets/1' \
--header 'Authorization: Bearer Random_Token_For_User_01' \
--header 'Content-Type: application/json' \
--data-raw '{
    "isPrivate": true
}'
```

* Remove followers so that they should not be able to re-follow a publisher once removed
```
curl --location --request DELETE 'http://127.0.0.1:8888/api/self/followers/2' \
--header 'Authorization: Bearer Random_Token_For_User_01' \
--header 'Content-Type: application/json' \
--data-raw '{
    "isPrivate": true
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

* Follow a publisher
```
curl --location --request POST 'http://127.0.0.1:8888/api/authors/1/followers' \
--header 'Authorization: Bearer Random_Token_For_User_03'
```