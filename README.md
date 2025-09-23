# AndCRM

### Install Env
```sh
composer install --optimize-autoloader --no-dev --no-scripts
npm i
```

### Run
```sh
composer run dev
```
```sh
docker-compose up -d --build
```

### Build Frontend
```sh
npm run build
```
### Generate SSL
```sh
mkdir -p certbot/conf certbot/www

docker-compose up -d nginx

docker-compose run --rm certbot

docker-compose down
```

### Requires 
* php8.4
* nodejs
* npm

License: CC BY-NC-ND 4.0
