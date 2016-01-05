<?php

namespace InfluxDBAL;


use InfluxDB\ResultSet;

class ResultSetIterator implements \Iterator
{
    /**
     * @var ResultSet
     */
    private $resultSet;

    /**
     * @var \Iterator
     */
    private $series;

    /**
     * @var \Iterator
     */
    private $points;

    public function __construct(ResultSet $resultSet)
    {
        $this->resultSet = $resultSet;
        $this->rewind();
    }

    public function current()
    {
        $value = $this->points->current();
        $serie = $this->series->current();
        if ($serie === null || $value === null) {
            return null;
        }
        $result = array_combine($serie['columns'], $value);
        return array_merge($result, $serie['tags'] ? $serie['tags'] : []);
    }

    public function next()
    {
        $this->points->next();

        if (!$this->points->valid()) {
            $this->series->next();
            $serie = $this->series->current();
            $this->points = new \ArrayIterator($serie ? $serie['values'] : []);
        }
    }

    public function key()
    {
        throw new \RuntimeException("Unimplemented");
    }

    public function valid()
    {
        return $this->series->valid();
    }

    public function rewind()
    {
        $this->series = new \ArrayIterator($this->resultSet->getSeries());
        $serie = $this->series->current();
        $this->points = new \ArrayIterator($serie ? $serie['values'] : []);
    }
}