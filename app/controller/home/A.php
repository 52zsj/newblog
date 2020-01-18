<?php

namespace app\controller\home;


use app\common\library\AbstractTest;

class A extends AbstractTest
{
    protected $action = 'action';
    public $name = 'name';

    public function test1()
    {
        // TODO: Implement test1() method.
        echo 'test1' . '<br>' . $this->action . '<br>' . $this->name;
    }

    public function test2()
    {
        // TODO: Implement test2() method.
        echo 'test2';
    }
}