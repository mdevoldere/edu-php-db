# edu-php-db

For educational purposes. Please do not use this on production environment.


## Usage

### Create DbContext

```php
use Md\Db;

Db::register('mycontext', [
    'db_type' => 'mysql',
    'db_dsn' => 'mysql:host=localhost;port=3306;dbname=mydb;charset=utf8',
    'db_user' => 'myusername', // 'root' if undefined
    'db_pass' => 'mypassword', // 'empty password' if undefined
    ]);
```

### Get & Use DbContext

```php
use Md\Db;

$db = Db::getContext('mycontext');

$result = $db->fetchAll('select * from mytable;');

$result = $db->fetch('select * from mytable where id=:id', [':id' => 1]);

$result = $db->exec('update mytable set name=:name where id=:id', [':id' => 1, ':name' => 'Mike']);
```

### Repository usage

```php
use Md\Db\Repository;

$repo = new Repository('mytable', 'id', 'mycontext');

$result = $repo->getById(1);

$result = $repo->getByCol('name', 'Mike');

$result = $repo->add(['name' => 'Mike', 'email' => 'mike@example.com']);

$result = $repo->update(1, ['name' => 'Mike']);

$result = $repo->delete(1);
```