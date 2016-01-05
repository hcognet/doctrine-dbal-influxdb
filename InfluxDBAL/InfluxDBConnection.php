<?php
namespace InfluxDBAL;

use Doctrine\DBAL\Driver\Connection;
use InfluxDB\Client;
use InfluxDB\Database;

class InfluxDBConnection implements Connection
{
    private $client;
    private $database;

    public function __construct(array $params, $username, $password, array $driverOptions = array())
    {
        // directly get the database object
        $dsn = 'influxdb://';
        if ($username) {
            $dsn .= sprintf("%s:%s@", $username, $password);
        }

        if ($params["host"]) {
            $dsn .= $params["host"];
        } else {
            $dsn .= 'localhost';
        }

        if ($params["port"]) {
            $dsn .= ':' . $params["port"];
        } else {
            $dsn .= ':8086';
        }

        if ($params["dbname"]) {
            $dsn .= '/' . $params["dbname"];
        } else {
            $dsn .= '/';
        }

        $this->database = Client::fromDSN($dsn);
        $this->client = $this->database->getClient();
    }

    public function prepare($prepareString)
    {
        return new InfluxDBStatement($this->client, $this->database, $prepareString);
    }

    public function query()
    {
        $args = func_get_args();
        $sql = $args[0];
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function quote($input, $type = \PDO::PARAM_STR)
    {
        return "'" . addslashes($input) . "'";
    }

    public function exec($statement)
    {
        $stmt = $this->query($statement);
        if (false === $stmt->execute()) {
            throw new \RuntimeException("Unable to execute query '{$statement}'");
        }

        return $stmt->rowCount();
    }

    public function lastInsertId($name = null)
    {
        throw new \RuntimeException("Unable to get last insert id in InfluxDB");
    }

    public function beginTransaction()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    public function commit()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    public function rollBack()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    public function errorCode()
    {

    }

    public function errorInfo()
    {

    }

    public function insert(array $values = [], $precision = Database::PRECISION_SECONDS)
    {
        $this->database->writePoints($values, $precision);
    }
}
