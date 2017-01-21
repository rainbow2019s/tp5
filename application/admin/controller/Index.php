<?php
namespace app\admin\controller;

use think\Controller;
use think\Log;
use think\View;
use think\Session;

use ecopro\AjaxOutput;
use ecopro\SubMenu;
use email\Email;

/**
 * 普通管理员后台管理
 *
 */

use app\admin\model\User as UserModel;

class Index extends Controller
{

    private $_user;

    public function __construct(UserModel $userModel)
    {
        parent::__construct();
        $this->_user=$userModel;

        Log::init([
            'type' => 'File',
            'path' => APP_PATH . 'logs/']);
    }

    public function index()
    {
        $exist = Session::has('user');
        // 如果Session中没有用户信息，则跳转登录页面
        if(empty($exist)){
            $this->redirect('index/login');           
        }
        // 如果Session中存在用户信息，则跳转至用户首页入口
        $user=Session::get('user');
        $this->assign('user',$user);

        $url='';
        $menu=[];
        $titles=$this->_user->getAdminBindActivities($user);
        foreach($titles as $title){
            $obj=AjaxOutput::toObject($title);
            $obj->submenu=SubMenu::getMenu($obj->alias)['submenu'];
            if(count($obj->submenu)>0){
                $url=$obj->submenu[0]['url'];
                break;
            }
        }

        if(empty($url)){
            //TODO 404
        }

        $this->redirect($url);

    }



    public function login()
    {
        return view();
    }

    public function ajaxLogin()
    {
         $params=input('params');
         $userForm=json_decode($params);

        if(!captcha_check($userForm->captcha)){
            return Json(AjaxOutput::error('验证码不正确'));
        }

        // 用户名不存在返回 bool(false)
        // 用户存在返回一维关联数组
        $singleResult=$this->_user->getAdmin($userForm);
        if($singleResult===false){
            return Json(AjaxOutput::error('用户帐号不存在'));
        }

        $admin=AjaxOutput::toObject($singleResult);

        if($admin->phone!=$userForm->username && $admin->email!=$userForm->username){
             return Json(AjaxOutput::error('用户名不正确'));        
        }

        if(md5($userForm->password.$admin->token)!=$admin->password){
             return Json(AjaxOutput::error('用户密码不正确'));
        }


        $user=new \stdClass;
        $user->id=$admin->id;
        $user->name=$admin->name;
        $user->phone=$admin->phone;
        $user->email=$admin->email;
        $user->password=$userForm->password;
        // 存入session
        Session::set('user',$user);

        return Json(AjaxOutput::success('',URL('index')));
    }

    public function resetSubmit()
    {        
        $password=input('password1');

        $user=Session::get('user');
        $user->token=uniqid();
        $user->password=md5($password.$user->token);

        // 返回受影响行数 0 表示失败
        $rows=$this->_user->resetSubmit($user);

        if($rows>0){

            $this->redirect('logout');
        }

        // 不成功处理返回重置页
        $this->redirect('reset');

    }



   /**
     * 退出系统
     *
     */

    public function logout()
    {
        Session::clear();
        Session::destroy();

        return $this->redirect('index/index');
    }


    public function test()
    {
        $config['protocol']='smtp';
        $config['smtp_host']='smtp.126.com';
        $config['smtp_user']='t_hao_2015@126.com';
        $config['smtp_pass']='Tanghao2012';
        $config['smtp_port']=25;
        $config['smtp_timeout']=30;
        $config['charset']='utf-8';
        $config['wordwrap']=TRUE;
        $config['_smtp_auth']=TRUE;

        $email=new Email($config);
        $email->from('t_hao_2015@126.com','sz');
        //$email->reply_to('abc@qq.com','abc');//回复地址
        $email->to('l.hao.2012@qq.com');
        $email->subject('Email Test');
 
        $email->message('Testing the email class.');    
        //$this->email->attach('d:/cprm.mwb');
 
        $email->send();

    }


















    


    public function baidu()
    {
        $this->assign('name','');
        $data=new \stdClass;
        $data->id=123;
        $data->name='张三';
        $this->assign('user',$data);  
        $this->assign('time',time());
        $this->assign('str','AbcDeFghiJK');
        //echo strtoupper(substr($str,0,5));
        $this->assign('arr',[1,2,3,9,8]);
        $this->assign('arr2',[[1,2,3],[4,5,6]]);
       
        return view();
    }    


    public function hello($name='123')
    {

        $this->assign('name', $name);
        $this->assign('day',date('N',time()));
        return $this->fetch();
    }

    public function vue()
    {
        return view();
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
