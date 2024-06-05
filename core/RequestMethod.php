<?php

namespace core;

class RequestMethod
{
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    public function __get($name)
    {
        return isset($this->array[$name]) ? $this->array[$name] : null;
    }

    public function getAll()
    {
        return $this->array;
    }
}