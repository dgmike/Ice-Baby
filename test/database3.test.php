<?php
include_once 'config.php';
include_once 'ice/model.php';

if (!class_exists('Admin')) {
    class Admin extends Model { 
        var $relatedJoin = array(
            'roles' => 'Role',
        );
    }
}

if (!class_exists('Role')) {
    class Role extends Model { 
        var $relatedJoin = array(
            'admins' => 'Role',
        );
    }
}

class Database extends UnitTestCase
{
    public function setUp()
    {
        createDatabase();
    }

    public function testRelatedJoin()
    {
        
    }
}
