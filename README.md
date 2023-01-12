
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
    EventChannel *.. Order
    OrderRecord <.. Order
    Item *-- OrderRecord
    Order <.. OrderFactory
    Order <.. OrderRepository
    class Product{
      +int id
      +int quantity
    }
    class Item{
      +Product product
    }
    class Order{
      -string id
      -DateTime date
      -Item[] items
      -int customerUserId
      -bool accepted
      -bool finished
      -EventChannel eventChannel

      +add(Product product)
      +accept()
      +remind()
      +done()
      +Order create(string id, int customerUserId, array products, EventChannel eventChannel)
      +OrderRecord toPersistenceRecord()
      +Order restore(OrderRecord record, EventChannel eventChannel)
    }
    class EventChannel{
      <<interface>>
      +publish()
    }
    class OrderRecord{
      +string id
      +DateTime date
      +Item[] items
      +int customerUserId
      +bool accepted
      +bool finished
    }
    class OrderFactory {
      <<interface>>
      +Order create(int customerUserId, array products)
    }
    class OrderRepository{
      <<interface>>
      +save(Order order)
      +Order findById(string id) 
    }

    OrderFactory *-- Interactor
    OrderRepository *-- Interactor
    OrderRecord <.. OrderRepository
    Input <.. Interactor
    Product *-- Input
    class Interactor{
      +execute(Input input)
    }
    class Input{
      +Product[] products
      +int customerUserId
    }
```
