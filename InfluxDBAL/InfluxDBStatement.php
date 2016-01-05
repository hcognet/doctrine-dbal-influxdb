<?php
namespace InfluxDBAL;

use Doctrine\DBAL\Driver\Statement;
use InfluxDB\Client;
use InfluxDB\Database;
use IteratorAggregate;

class InfluxDBStatement implements IteratorAggregate, Statement
{
    private $client;
    private $statement;
    private $values = [];

    /**
     * @var \InfluxDB\ResultSet
     */
    private $resultSet = null;

    /**
     * @var \Iterator
     */
    private $currentIterator = null;

    private $defaultFetchMode = \PDO::FETCH_ASSOC;
    private $database;

    public function __construct(Client $client, Database $database, $statement)
    {
        $this->client = $client;
        $this->database = $database;
        $this->statement = $statement;
    }

    public function bindValue($param, $value, $type = null)
    {
        $this->values[$param] = $value;
    }

    public function bindParam($column, &$variable, $type = null, $length = null)
    {
        $this->values[$column] =& $variable;
    }

    public function errorCode()
    {

    }

    public function errorInfo()
    {

    }

    public function execute($params = null)
    {
        $this->resultSet = null;

        $this->values = (is_array($params)) ? array_replace($this->values, $params) : $this->values;
        $stmt = $this->statement;
        foreach ($this->values as $key => $value) {
            $value = is_string($value) ? "'" . addslashes($value) . "'" : $value;
            $stmt = preg_replace("/(\?|:{$key})/i", "{$value}", $stmt, 1);
        }

        $this->resultSet = $this->database->query($stmt);
        return true;
    }

    public function rowCount()
    {
        throw new \RuntimeException("Not Implemented");
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ResultSetIterator($this->resultSet);
    }

    public function closeCursor()
    {
        $this->resultSet = null;
    }

    public function columnCount()
    {
        throw new \RuntimeException("Not Implemented");
    }

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        $this->defaultFetchMode = $fetchMode;
    }

    public function fetch($fetchMode = null)
    {
        if (!$this->currentIterator) {
            $this->currentIterator = $this->getIterator();
        }

        $data = $this->currentIterator->current();

        $this->currentIterator->next();

        return $data;
    }

    public function fetchAll($fetchMode = null)
    {
        $res = [];
        $it = $this->getIterator();
        foreach ($it as $item) {
            $res[] = $item;
        }

        return $res;
    }

    public function fetchColumn($columnIndex = 0)
    {
        $elem = $this->fetch();

        if ($elem) {
            if (array_key_exists($columnIndex, $elem)) {
                return $elem[$columnIndex];
            } else {
                return $elem[array_keys($elem)[$columnIndex]];
            }
        }

        return null;
    }
}
