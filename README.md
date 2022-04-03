# ctl-file-processor
Processing and loading files into the database

### create table
```php
php file_upload.php --create_table=users -hlocalhost -uuser -ppassword -ddb_users
```

### upload data into table
```php
php file_upload.php --file=user.csv -hlocalhost -uuser -ppassword -ddb_users
```

USAGE

| Command | Parameter | Description |
| ---------- | ---------- | ---------- |
| --file           | File name       |  Parsing file and insert into table|
| --create_table   | Table name      |  Create table in database |
| --drop_table     | Table name      |  Drop table in database |
| --truncate_table | Table name      |  Truncate table in database |
| --dry_run        |                 |  Parsing file and prepare to processing without insert |
| -u               | User name       |  User name |
| -p               | Password        |  User password |
| -h               | Host name or IP |  Database host |
| -d               | Name            |  Database name |
| -help            |                 |  Help information |

## RUN TESTS
./vendor/bin/phpunit --bootstrap autoloader.php tests/

