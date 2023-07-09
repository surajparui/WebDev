# Web Development Project
# Airplane Booking System

The Airplane Booking System is a REST API designed to handle multiple-seat bookings for an aircraft. It follows the SOLID principles and utilizes design patterns such as repositories and services to ensure a clean and maintainable codebase. The system is built using Laravel 8, MySQL 8, and Docker.

## Design Description

The codebase is organized into controllers, repositories, and services. The controllers are responsible for directing the flow of execution and delivering responses. They inject the necessary dependencies through the constructor method. The repositories handle database connections and queries, providing a better organization for complex queries. The services encapsulate the business rules and manage the booking process.

The Airplane Booking System consists of three main classes:
- **Bookings**: Manages all the bookings and enforces application rules.
- **Row**: Represents a row of seats and allows recursive access to other rows.
- **Seat**: Controls seat occupation and identifies if it is a window seat.

Possible Enhancements:
- **Login System**: Implement a login system to manage user authentication and authorization.
- **Canceling System**: Add the ability to cancel bookings and free up seats.
- **Bookings by Date**: Extend the system to allow bookings based on specific dates.
- **Friendly URL**: Improve the URL structure to make it more user-friendly.
- **Register Passenger with Personal ID**: Enhance the passenger registration process by including a personal identification field.
- **State Design Pattern**: Implement the state design pattern to manage and enforce rules dynamically.

## Technologies Used

- Laravel 8: A powerful PHP framework for building web applications.
- MySQL 8: A popular open-source relational database management system.
- Docker: A containerization platform that simplifies the deployment and management of applications.

## Instructions

1. Navigate to the docker directory and execute the following command:
   ```
   $ docker-compose up -d
   ```

2. After the previous step finishes, run the following commands:
   ```
   $ docker container exec -it php-fpm composer install \
       && docker container exec -it php-fpm cp .env.example .env \
       && docker container exec -it php-fpm php ./artisan key:generate \
       && docker container exec -it php-fpm php ./artisan config:cache \
       && docker container exec -it php-fpm php ./artisan migrate:fresh --seed \
       && docker container exec -it php-fpm php ./artisan db:seed --class=ShortRangeAircraftSeeder \
       && docker container exec -it php-fpm composer dump-autoload \
       && docker container exec -it php-fpm php ./artisan optimize:clear
   ```

3. The system should now be available at the following link: [http://localhost](http://localhost).

## Execute Tests

To run the tests, execute the following command:
```
$ docker container exec -it php-fpm ./artisan test
```

## API Endpoints

### POST /bookings/aircrafts/{aircraft}

#### URL Parameters

| Name      | Required | Type    | Description                                          |
|-----------|----------|---------|------------------------------------------------------|
| aircraft  | required | integer | The ID of the aircraft (an aircraft with ID "1" is available). |

#### Parameters

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

#### Response

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

#### Error Response

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
    "error": "There are not enough seats for this booking",
    "code": 422
}
```

### GET /bookings

#### Response

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

#### Response

```json
{
    "data": []
}
```

## Acknowledgements

The Airplane Booking System was developed with the intention to solve the problem statement and requirements provided.
