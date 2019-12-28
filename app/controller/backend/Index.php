<?php


namespace app\controller\backend;


use app\middleware\Check;
use think\facade\Log;

/**
 * Class Index
 * @package app\controller\backend
 * @Group("backend",ext="html");
 */
class Index extends Base
{

    protected $middleware=[Check::class];
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @Route("index",method="GET");
     */
    public function index()
    {
        Log::error('1');
        dump('aaaa');
        echo 'admin.haha';
    }

    /**
     * @Route(":name/:id",method="GET");
     */
    public function test()
    {
        $name = $this->request->param('name');
        $id = $this->request->param('id');
        dump($name);
        dump($id);
        echo ';';
    }
}