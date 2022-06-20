# Airplane Booking System
REST API for multiple-seat booking
### Design description
We designed the code using the SOLID principles and the design patterns: repositories and services. 
Every controller has one work that's why I inject their main work in the class construct method when I need some work from another class I add it in the specific method using type hint. I keep the controllers clean, I use them just to point who has to work, repository or the service, and to deliver the response. 
I use the repositories to isolate the database connections so I can have better control and can have a better organization of complex queries. 
I use the services to isolate and keep the business rules
For this solution I developed 3 object classes:
    - Bookings, to keep all the bookings and control the application of the rules
    - Row, to keep the seats and other rows as a node inside a node so I can have recursive access to them
    - Seat, to control if it is occupied or is on window
So I have the Booking Service which manage these 3 classes to do the bookings.
### Possible enhancements
    - Login system
    - canceling system
    - Bookings by date
    - Friendly url
    - Register the passenger with a personal id
    - State design pattern to manage the rules 
### Tecnologies
    - Laravel 8
    - Mysql 8
    - Docker
### Instructions 
Get in the docker directory and execute. It may take a while
```sh
$ docker-compose up -d
```
After the first step finishes execute the comands:
```sh
$ docker container exec -it php-fpm composer install \
    && docker container exec -it php-fpm cp .env.example .env \
    && docker container exec -it php-fpm php ./artisan key:generate \
    && docker container exec -it php-fpm php ./artisan config:cache \
    && docker container exec -it php-fpm php ./artisan migrate:fresh --seed \
    && docker container exec -it php-fpm php ./artisan db:seed --class=ShortRangeAircraftSeeder \
    && docker container exec -it php-fpm composer dump-autoload \
    && docker container exec -it php-fpm php ./artisan optimize:clear
```
Now the system might have been available on the following link: http://localhost 
Execute the tests
```sh
$ docker container exec -it php-fpm ./artisan test
```
## API endpoints
### POST /bookings/aircrafts/{aircraft}
**Url Parameters**
|          Name | Required |  Type   | Description                                                                                                                                                           |
| -------------:|:--------:|:-------:| --------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
|     `aircraft` | required | integer  | The id of the aircraft(One aircraft has already been added with the id "1").                                                                     |
**Parameters**
```json
{
    "passengers" : [
        {
            "name" : "Iosu",
            "quantity_seats": 7
        },
        {
            "name" : "Gerard",
            "quantity_seats": 2
        }
    ]
}
```
**Response**
```json
{
    "data": [
        {
          "passenger": "Iosu",
          "seats": "A1, B1, C1, D1, A2, B2, C2"
        },
        {
          "passenger": "Gerard",
          "seats": "E1, F1"
        }
    ]
}
```
**Error Response**
```json
{
    "error": {
        "passengers": [
            "The passengers field is required."
        ]
    },
    "code": 422
}
```
```json
{
    "error": {
        "passengers.0.name": [
            "The passengers.0.name field is required."
        ],
        "passengers.0.quantity_seats": [
            "The passengers.0.quantity_seats field is required."
        ]
    },
    "code": 422
}
```
```json
{
    "error": "No seats available",
    "code": 422
}
```
```json
{
    "error": "There are no enough seats for this booking",
    "code": 422
}
```
### GET /bookings 
**Response**
```json
{
    "data": [
        {
          "passenger": "Iosu",
          "seats": "A1, B1, C1, D1, A2, B2, C2"
        },
        {
          "passenger": "Gerard",
          "seats": "E1, F1"
        }
    ]
}
```
### DELETE /bookings 
**Response**
```json
{
    "data": []
}
```
