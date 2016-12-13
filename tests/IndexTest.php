<?php
namespace tests;

//use tests\TestCase;
use admin\index\Add;

class IndexTest extends TestCase
{
    public function testAdd()
    {
        $add=new Add();
        $value=$add->count(10);

        fopen('php://output','r');
        echo 'print result';
        echo $value;

        $this->assertTrue($value==100);
    }
}