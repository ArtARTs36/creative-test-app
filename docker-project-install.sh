echo "
cd app
cp .env.docker.example .env.local
composer install
php bin/console orm:schema-tool:update --force
php bin/console fetch:trailers
" | docker exec -i aspirant_test-php-fpm bash
