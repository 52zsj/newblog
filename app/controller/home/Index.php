<?php

namespace app\controller\home;

use app\BaseController;
use think\annotation\Route;
use think\annotation\route\Group;

/**
 * Class Index
 * @package app\controller\home
 * @Group("",ext="html");
 */
class Index extends BaseController
{
    /**
     * @Route("/",method="GET");
     */
    public function index()
    {
        echo 'home 首页';
    }

    /**
     * @param string $name
     * @Route(":name",method="GET");
     */
    public function hello($name = 'ThinkPHP6')
    {
        $id='';
        echo 'home hello ' . $name.'方法','id:'.$id;
    }
}
