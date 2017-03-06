<?php


class RouterTest extends PHPUnit_Framework_TestCase
{

    public function testIsThereAnySyntaxError()
    {
        $var = new Snelling\Router;
        $this->assertTrue(is_object($var));
        unset($var);
    }
}