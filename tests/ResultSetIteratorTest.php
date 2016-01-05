<?php

namespace InfluxDBAL;

use InfluxDB\ResultSet;

require_once __DIR__ . "/../vendor/autoload.php";

class ResultSetIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $str = '{"results":[{"series":[{"name":"t140","tags":{"label":"*"},"columns":["time","cnt","summult","Label"],"values":[["1970-01-01T00:00:00Z",12265,6718195,null]]},{"name":"t140","tags":{"label":"jp@gc - Dummy Sampler"},"columns":["time","cnt","summult","Label"],"values":[["1970-01-01T00:00:00Z",12265,null,null]]}]}]}';
        $resultSet = new ResultSet($str);
        $it = new ResultSetIterator($resultSet);
        $res = [];
        foreach ($it as $point) {
            $res[] = $point;
        }
        $this->assertEquals(2, sizeof($res));
    }
}
