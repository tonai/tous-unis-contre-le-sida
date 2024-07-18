# Tous Unis Contre Le Sida

Connect to mysql:
```sh
sudo mysql
```

Then create database and user:
```sql
CREATE DATABASE tousuniscontrelesida;
CREATE USER 'tousuniscontrelesida'@'localhost' IDENTIFIED BY 'tucls';
GRANT ALL ON tousuniscontrelesida.* TO 'tousuniscontrelesida'@'localhost';
```
