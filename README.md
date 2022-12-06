
```
docker run \
  --volume `pwd`:`pwd` \
  --workdir `pwd` \
  php:8.1.13-zts-alpine3.17 \
  php vendor/bin/phpunit
```