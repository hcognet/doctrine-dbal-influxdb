<?php
namespace InfluxDBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\DriverException;
use Doctrine\DBAL\Driver\ExceptionConverterDriver;
use Doctrine\DBAL\VersionAwarePlatformDriver;

class InfluxDBDriver implements Driver, ExceptionConverterDriver, VersionAwarePlatformDriver
{
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        $conn = new InfluxDBConnection($params, $username, $password, $driverOptions);
        return $conn;
    }

    public function getDatabasePlatform()
    {
        return new InfluxDBPlatform();
    }

    public function getSchemaManager(Connection $conn)
    {

    }

    public function getName()
    {
        return "influxdb";
    }

    public function getDatabase(Connection $conn)
    {
        return $conn->getParams()["dbname"];
    }

    public function convertException($message, DriverException $exception)
    {

    }

    public function createDatabasePlatformForVersion($version)
    {
    }
}
