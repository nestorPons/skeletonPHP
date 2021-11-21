# docker-lamp

Docker example with Apache, MySql 8.0, PhpMyAdmin and Php

You can use MySql 5.7 if you checkout to the tag `mysql5.7`

I use docker-compose as an orchestrator. To run these containers:

```
sudo docker-compose up -d
```

Open phpmyadmin at [http://localhost:8000](http://localhost:8000)
Open web browser to look at a simple php example at [http://localhost:8001](http://localhost:8001)
http://localhost:8080/index.php

Run mysql client:

```
sudo docker-compose exec db mysql -u root -p
```

user: root
password: test

Default configuration use .htaccess.local for configure folder server

Enjoy !
