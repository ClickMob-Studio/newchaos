<?php

abstract class DataMapper
{
    abstract public static function findByID($id);

    abstract public static function save($object);

    abstract public static function delete($object);

    abstract protected static function insert($object);

    abstract protected static function update($object);
}
