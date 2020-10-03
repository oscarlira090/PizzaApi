# PizzaApi

The task:

Create an API for ordering pizza that can support an app with the following use cases:

- Create an order
- Update an order
- View order status

Rules:

orders can have multiple pizzas
pizzas can have multiple toppings

Use however many endpoints you feel are appropriate

Create the API using your preferred version of Symfony running on any version php7 and any recent version of MySQL
Here are a few of the packages that may be useful in the API development:

- sensio/framework-extra-bundle
- friendsofsymfony/rest-bundle
- jms/serializer-bundle

## Installation

- composer install
- php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate
- symfony  server:start  
### Import Data
- INSERT INTO `pizzaapi`.`pizza`(`name`)VALUES('MEXICAN');
- INSERT INTO `pizzaapi`.`pizza`(`name`)VALUES('ITALIAN');
- INSERT INTO `pizzaapi`.`topping`(`name`)VALUES('Pepperoni');
- INSERT INTO `pizzaapi`.`topping`(`name`)VALUES('Champiñones');
### END POINTS
-- Body Request

{
    "order":{
        "customer":10,
        "pizzas":[
            {
                "id":1,
                "quantity":10,
                "price":25,
                "toppings": [1,2]
            },
            {
                "id":2,
                "quantity":10,
                "price":25,
                "toppings": [1,2]
            }
        ]
    }
}
POST http://127.0.0.1:8000/api/orders

-- Body Request

{
    "order":{
        "customer":10,
        "pizzas":[
            {
                "id":1,
                "quantity":2,
                "price":25,
                "toppings": [1,2]
            },
            {
                "id":2,
                "quantity":10,
                "price":25,
                "toppings": [1,2]
            }
        ]
    }
}
PUT http://127.0.0.1:8000/api/orders/{order_id}

GET http://127.0.0.1:8000/api/orders/{order_id}/status




