<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/18
 * Time: 9:01
 */

namespace app\common\library;


class Test
{
    protected $price = '360';
    private $table = '无属性表格';

    public function __set($name, $value)
    {
        echo '<br>设置变量：' . $name . '时调用了该方法<br>';
        $this->$name = $value;

    }

    public function __get($name)
    {
        echo '<br>获取变量：' . $name . '时调用了该方法<br>';
        return $this->$name;
    }

    public function __construct()
    {

    }

}