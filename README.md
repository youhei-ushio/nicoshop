
```
docker run \
  --volume `pwd`:`pwd` \
  --workdir `pwd` \
  php:8.1.13-zts-alpine3.17 \
  php vendor/bin/phpunit
```

```mermaid
classDiagram
    Item *-- Order
    Product *-- Item
    Product <.. Order
    OrderCreated <.. Order
    OrderRecord <.. Order
    class Product{
      +int id
      +string name
    }
    class Item{
      +Product product
      +int quantity
    }
    class Order{
      -DateTime date
      -Item[] items
      -int customerUserId
      -bool accepted
      -bool finished

      +Order create(int customerUserId)$
      +add(Product product, int quantity)
      +accept()
      +remind()
      +done()
      +OrderRecord toSaveRecord()
      +Order restore(OrderRecord record)
    }
    class OrderCreated{
      <<interface>>
      +notify()
    }
    class OrderRecord{
      +DateTime date
      +Item[] items
      +int customerUserId
      +bool accepted
      +bool finished
    }
```
