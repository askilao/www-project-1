<?php
require_once "../html/classes/DB.php";

class DBTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testDBConnection(){
        $this->assertInstanceOf(
            PDO::class, DB::getDBConnection()
        );
    }
}