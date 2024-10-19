
 api-pedidos


para rodar o projeto ->
./vendor/bin/sail up
./vendor/bin/sail artisan migrate



docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
"