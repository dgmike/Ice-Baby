<?php
include 'config.php';
include 'ice/model.php';

class ModelTest extends UnitTestCase
{
    public function setUp()
    {
        createDatabase();
    }

    public function testConnect()
    {
    }
}
