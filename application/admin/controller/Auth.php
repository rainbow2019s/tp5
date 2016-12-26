<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Log;
use think\View;


use stdClass;
use think\Session;
use app\admin\model\User as UserModel;


class Auth extends Controller
{
    private $_user;
    /**
     * 构造方法
     *
     *
     * @throws Exception
     */
    public function __construct(UserModel $userModel)
    {
        parent::__construct();

        $this->_user=$userModel;

    }

    /**
     * 进入首页
     *
     * @throws Exception
     */
    public function index()
    {
        $exist = Session::has('user');
        // 如果Session中没有用户信息，则跳转登录
        if(empty($exist)){
            $this->redirect('auth/login');           
        }
        
        $user=Session::get('user');
        $this->assign('user',json_encode($user));
        return view();
    }

    // 后台登录
    public function login()
    {
        //Session::flash('code','1234');
        //$this->assign('code','1234');
        return view();
    }

    // Ajax提交后台登录
    // 用户登录后在Session里应保存的信息
    
    public function ajaxlogin($params='')
    {
         //$code=Session::pull('code');

         $captcha=input('captcha');
         if(!captcha_check($captcha)){
             $this->redirect('auth/login');
         }


         $user=new stdClass;
         $user->id=1;
         $user->name='l.hao';
         $user->phone='18015826672';
         $user->email='l.hao.2012@qq.com';
         Session::set('user',$user);
         dump($captcha);
    }


    // 测试用途
    public function test()
    {
        //$admin=$this->_user->getSuperAdmin();
        //dump($admin);

        $admin=new stdClass;
        $admin->phone='18015826672';

        dump($this->_user->getAdmin($admin));
    }
}
