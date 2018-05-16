<?php

namespace Tests\Storage;

use Clockwork\Request\Request;
use Clockwork\Storage\SqlStorage;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @package Tests\Storage
 */
class SqlStorageTest extends TestCase
{
    /**
     * @var string
     */
    private $tableName = 'clockwork';

    /**
     * @var string
     */
    private $dbName = 'testdb';

    /**
     * @var string
     */
    private $dbUser = 'testdb_user';

    /**
     * @var string
     */
    private $dbPassword = 'testdb_password';

    /**
     * @var \PDO
     */
    private $pdo;

    public function setUp()
    {
        parent::setUp();
        $this->pdo = $this->getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("DROP TABLE IF EXISTS {$this->tableName}");
    }

    public function testDatabaseTableCanBeCreated()
    {
        $storage = new SqlStorage($this->pdo, $this->tableName);
        $requestId = '1526454388-5414-782344514';
        $request = new Request(['id' => $requestId]);

        $storage->store($request);

        $statement = $this->pdo->query("select * from {$this->tableName}");
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(1, $results);
        $this->assertEquals($results[0]['id'], $requestId);
    }

    /**
     * @return \PDO
     */
    private function getPdo()
    {
        $dbType = getenv('DB');
        switch($dbType) {
            case 'mysql':
                return new PDO('mysql:host=localhost;dbname=' . $this->dbName, $this->dbUser, $this->dbPassword);
            case 'pgsql':
                return new PDO('pgsql:host=localhost;dbname=' . $this->dbName, $this->dbUser, $this->dbPassword);
            case 'sqlite':
            default:
                return new PDO('sqlite:' . __DIR__ . '/' . $this->dbName . '.sqlite');
        }
    }
}
