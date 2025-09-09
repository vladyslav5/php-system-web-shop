sudo docker-compose up -d
sudo docker exec -it symfony-app php bin/console doctrine:database:drop --force --env=test
sudo docker exec -it symfony-app php bin/console doctrine:database:create --env=test
sudo docker exec -it symfony-app php bin/console make:migration -n --env=test
sudo docker exec -it symfony-app php bin/console doctrine:migrations:migrate -n --env=test
sudo docker exec -it symfony-app php bin/console doctrine:fixtures:load -n --env=test
sudo docker exec -it symfony-app vendor/bin/codecept run Api CreateOrderCest.php
sudo docker exec -it symfony-app vendor/bin/codecept run Api ViewOrderCest.php
