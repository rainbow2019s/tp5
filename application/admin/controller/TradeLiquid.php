<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Log;
use think\View;

//use admin\index\;

//use admin;

class TradeLiquid extends Controller
{
    public function __construct()
    {
        parent::__construct();

        Log::init([
            'type' => 'File',
            'path' => APP_PATH . 'logs/']);
    }

    public function testURL($name)
    {
        echo "URL".$name;
    }


    public function index()
    {
        return $this->success("操作成功","dbTest");

        //return "Hello ThinkPHP";
    }

    public function baidu()
    {
        $this->assign('name','');
        $data=new \stdClass;
        $data->id=123;
        $data->name='张三';
        $this->assign('user',$data);    

        return view();
    }    


    public function hello($name='123')
    {

        $this->assign('name', $name);
        return $this->fetch();
    }

    public function dbTest()
    {

        //$result = Db::query('select id,code,nums from scg_factory where nums>:nums', ['nums' => 0]);

        //$this->assign('name', 'TP');
        //return $this->fetch('index/dbtest', ['name' => 'TP']);
        //
        //Log::write('读取数据库成功');
        //Log::write(json_encode($result));

        $user=model("admin/index/User");

        $view = new View();
        return $view->fetch('index/dbtest', ['name' => 'TP2', 'result' => $user->getFactory()]);
    }


    public function dbTest2()
    {
        $factory=Db::name('scg_factory')->find();
        var_dump($factory);
    }

    public function phpview()
    {
        $view = new View();

        //$view_path=APP_PATH.DS.'admin'
        return $view->engine('php')->fetch();
    }

    public function arrayTest()
    {
        $arr = ['a' => 1, 14, 'b' => 7, 8];
        asort($arr);
        print_r($arr);
        var_dump($arr);
        #array(4) { ["a"]=> int(1) ["b"]=> int(7) [1]=> int(8) [0]=> int(14) }
        ksort($arr);
        print_r($arr);
        #Array ( [a] => 1 [b] => 7 [0] => 14 [1] => 8 )
    }

    public function classTest()
    {
        $admin = new \admin\Test();
        $admin->show();

        //$user = model('User');
        //echo $user->getId();

        //$user = Loader::model('User');
        //echo $user->getId();
        //$user = new \app\admin\model\User;
        //echo $user->getId();
    }

}
