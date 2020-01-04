<?php


namespace app\common\library;


use think\App;

class Auth
{
    protected static $instance = null;
    protected $app = null;
    protected $request = null;

    public function __construct(App $app)
    {

        $this->app = $app;
        $this->request = $this->app->request;
        // 控制器初始化
        $this->initialize();

    }

    // 初始化
    protected function initialize()
    {
    }

    public function checkLogin()
    {

    }
}