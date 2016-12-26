<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Log;
use think\View;

use stdClass;
use app\admin\model\Activity as ActivityModel;

/**
 *
 * 用于功能的增删改查
 * 
 */



class Activity extends Controller
{
    private $_activity;


    public function __construct(ActivityModel $activityModel)
    {
        parent::__construct();
        
        $this->_activity=$activityModel;

    }


    // 获取所有有效的应用
    public function getAllEnabled()
    {
        return Json($this->_activity->getAllEnabled());
    }

    // 获取所有无效的应用
    public function getAllDisabled()
    {
        return Json($this->_activity->getAllDisabled());
    }

    // 获取所有应用（有效和无效）
    public function getAll()
    {
        return Json($this->_activity->getAll());     
    }

    // 注册应用
    public function add($params='')
    {
        //TODO: 调试时打开
        //$activity=json_decode($params);
        $activity = new stdClass;
        $activity->name='知识库3';
        $activity->domain='';
        $activity->host_ip_address='127.0.0.1';
        $activity->entrance_url='/tech3index';
        $activity->entrance_alias='知识问答3';


        $rows=$this->_activity->add($activity);
        return Json($rows);

    }

    // 移除一个小应用
    public function remove($id)
    {
        $rows=$this->_activity->remove($id);

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
