## HOW TO RUN
- git clone
- cd into project
- composer install/update
- make the database in postgre or mysql or something else
- set the `env` file to `.env` then set the environment
- for me :
    ```
    CI_ENVIRONMENT = development
    ...
    database.default.hostname = localhost
    database.default.database = pokemon
    database.default.username = postgres
    database.default.password = bismillah
    database.default.DBDriver = Postgre
    database.default.DBPrefix =
    ```
- php spark migrate
- php spark serve
- test the API using postman or insomnia or other

## Desain Database

![alt](Screenshot%20(88).png)

## API GET ALL (method=get)

```
http://localhost:8080/pokemon
```

```
{
    "data": {
        "pokemons": [
            {
                "id": "436bba1f-34b8-49b0-b600-1db76bcea090",
                "number": "1",
                "name": "lutfi",
                "type": [
                    "Grass"
                ]
            },
            {
                "id": "16de796f-2dbb-4a94-adb0-28b63a5ab1d1",
                "number": "3",
                "name": "pikachu",
                "type": [
                    "Grass",
                    "Poison"
                ]
            },
            {
                "id": "cd7a7c8c-8dcf-4045-827e-9ca8361ea689",
                "number": "4",
                "name": "pikachu",
                "type": [
                    "Grass",
                    "Poison"
                ]
            }
        ]
    }
}
```

## API GET DETAIL (method=get)

```
http://localhost:8080/pokemon/a0ad13c3-c1bc-4de0-891f-1e29d265cfbe
```

```
{
    "status": 201,
    "error": null,
    "messages": {
        "success": "Succcesfully requested",
        "data": {
            "id": "a0ad13c3-c1bc-4de0-891f-1e29d265cfbe",
            "number": "2",
            "name": "kura",
            "type": [
                "Grass",
                "Galak"
            ]
        }
    }
}
```

## API CREATE (method=post)

type of pokemons will be automatically created in table `type` and `pokemonTypeTransaction`

```
http://localhost:8080/pokemon
```
request body:
```
{
    "name": "kura",
    "type": [
        "Ganas"
    ]
}
```
response:
```
{
    "name": "kura",
    "type": [
        "Ganas"
    ]
}
```

## API UPDATE (method=push)
type of pokemons will be automatically updated in table `type` and `pokemonTypeTransaction`
```
http://localhost:8080/pokemon/a0ad13c3-c1bc-4de0-891f-1e29d265cfbe
```
request body:
```
{
    "name": "kura",
    "type": [
        "Galak","Grass"
    ]
}
```
response:
```
{
    "status": 201,
    "error": null,
    "messages": {
        "success": "Succcesfully updated",
        "pokemon": "kura"
    }
}
```