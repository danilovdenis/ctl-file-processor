# ctl-file-processor
Processing and loading files into the database

```shell
composer install
```

### RUN TESTS
./vendor/bin/phpunit --bootstrap autoloader.php tests/


### CREATE TABLE
```php
php file_upload.php --create_table -hlocalhost -uuser -ppassword -ddb_users
```

### UPLOAD DATA INTO TABLE
```
WARNING
Table users has a unique key `email`. Therefore, you must first check the file for duplicate emails.
```
```php
php file_upload.php --file=users.csv -hlocalhost -uuser -ppassword -ddb_users
```



### USAGE

| Command | Parameter | Description |
| ---------- | ---------- | ---------- |
| --file           | File name       |  Parsing file and insert into table|
| --create_table   | Table name      |  Create/recreate table in database |
| --dry_run        |                 |  Parsing file and prepare to processing without insert |
| -u               | User name       |  User name |
| -p               | Password        |  User password |
| -h               | Host name or IP |  Database host |
| -d               | Name            |  Database name |
| -help            |                 |  Help information |

