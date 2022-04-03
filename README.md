# ctl-file-processor
Processing and loading files into the database

#create table
php file_upload.php --create_table="users" -hlocalhost -uuser -ppassword

#upload data into table
php file_upload.php --file="user.csv" -hlocalhost -uuser -ppassword

USAGE

--file           [File name]        Parsing file and insert into table
--create_table   [Table name]       Create table in database
--drop_table     [Table name]       Drop table in database
--truncate_table [Table name]       Truncate table in database
--dry_run                           Parsing file and prepare to processing without insert
-u               [User name]        Create table in database
-p               [Password]         Create table in database
-h               [host name or IP]  Create table in database

TESTS
./vendor/bin/phpunit --debug --bootstrap autoloader.php tests/

