<?php 


namespace Md\Db;

use PDO;
use Exception;
use Md\Db\Exceptions\BadContextException;
use Md\Db\Exceptions\BadDbConfigException;
use Md\Db\Exceptions\BadQueryException;
use PDOStatement;

use function class_exists;

$d = new DbQuery(new PDO('sqlite::memory:'));
$d->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)')->execute();
$d->exec('INSERT INTO test (id, name) VALUES (1, "Alice")')->execute()
  ->exec('INSERT INTO test (id, name) VALUES (2, "Bob")')->execute();
$result = $d->query('SELECT * FROM test where id=:id')
            ->bind(':id', 1)
            ->fetch();

            var_export($result);

/** Class DbQuery (Database Query Builder)
 * DbContext Instance 
 *
 * @author   MDevoldere 
 * @version  1.1.0
 * @access   public
 */
class DbQuery
{
    private bool $f; // false = exec (returns number of affected rows), true = query (returns result set)
    private string $q; // query string
    private array $v; // bind values
    private int $nr; // number of affected rows
    private ?PDOStatement $stmt; // query statement

    /**
     * Dbo Constructor
     */
    public function __construct(public readonly PDO $pdo) 
    {
        $this->f = false;
        $this->q = '';
        $this->v = [];
        $this->nr = 0;
        $this->stmt = null;
    }

    public function exec(string $query): self
    {
        $this->f = false;
        $this->q = $query;
        return $this;
    }

    public function query(string $query): self
    {
        $this->f = true;
        $this->q = $query;
        return $this;
    }

    public function bind(string $param, mixed $value, ?int $type = null): self
    {
        $this->v[$param] = $value;
        return $this;
    }

    public function bindAll(array $values): self
    {
        $this->v = $values;
        return $this;
    }

    public function execute(): self
    {
        if(empty($this->q)) {
            throw new BadQueryException('No Query to Execute');
        }
        if(!empty($this->v)) {
            $this->stmt = $this->pdo->prepare($this->q);
            $this->stmt->execute($this->v);
        } else {
            if($this->f === false) {
                $this->nr = $this->pdo->exec($this->q);
            } else {
                $this->stmt = $this->pdo->query($this->q);
            }
        }
        return $this;
    }

    private function prefetch(?string $model = null): self {
        $this->execute();
        if($model !== null && class_exists($model)) {
            $this->stmt->setFetchMode(PDO::FETCH_CLASS, $model);
        } else {
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
        }
        return $this;
    }

    public function fetch(?string $model = null): mixed
    {
        $this->prefetch($model);
        return $this->stmt ? $this->stmt->fetch() : [];
    }

    public function fetchAll(?string $model = null): array
    {
        $this->prefetch($model);
        return $this->stmt ? $this->stmt->fetchAll() : [];
    }
}
