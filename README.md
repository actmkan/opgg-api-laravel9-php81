## OP.GG 사전과제

실행 순서 : \
$ docker run --rm \ \
-u "$(id -u):$(id -g)" \ \
-v $(pwd):/opt \ \
-w /opt \ \
laravelsail/php81-composer:latest \ \
composer install --ignore-platform-reqs

$ sail up -d
