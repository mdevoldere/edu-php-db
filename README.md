# edu-php-db

For educational purposes. Please do not use this on production environment.

## Require 
- PHP >= 8.1
- mysql/mariadb or sqlite

## Basic Examples 
- [Examples using 1 table](DbTests/)
- [Config Examples for mysql & sqlite](DbTests/cfg/)


## Usage
Asuming using this table `users` :
| id | name | email |
| --- | --- | --- |
| 1 | Mike | mike@test.fr |
| 2 | Cindy | cindy@example.com |
| 3 | Paul | paul@example.com |

... and using this config file :

```php
/* /path/to/config/files/mycontext.php */
return [
    'mysql:host=localhost;dbname=db_test;charset=utf8mb4',
    'username',
    'userpass'
];
```

### Create DbContext

```php
use Md\Db;

// Set config directory
Db::setConfigDirectory('/path/to/config/files/');

// load mycontext.php config file from config directory
$db = Db::getContext('mycontext'); 
```

### Get & Use DbContext

```php
use Md\Db;

Db::setConfigDirectory('/path/to/config/files/');

$db = Db::getContext('mycontext');

$result = $db->fetchAll('select name, email from users;'); // get all rows

$result = $db->fetch('select * from users where id=:id', [':id' => 1]); // get single row

$result = $db->exec('update users set name=:name where id=:id', [':id' => 1, ':name' => 'Mike']); // update row
```

### Repository usage

```php
use Md\Db\Repository;

Db::setConfigDirectory('/path/to/config/files/');

// Repository uses Db::getContext()
$repo = new Repository('users', 'id', 'mycontext');

$result = $repo->getById(1); // select user where id=1

$result = $repo->getByCol('name', 'Mike'); // select user(s) where name=Mike

$result = $repo->add(['name' => 'Jack', 'email' => 'jack@example.com']); // add jack

$result = $repo->update(1, ['name' => 'MikeUpdated']); // update Mike's name

$result = $repo->delete(3); // delete Paul
```
