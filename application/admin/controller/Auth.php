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
    
    public function ajaxlogin()
    {
         //$code=Session::pull('code');

         $params=input('params');
         $userForm=json_decode($params);

        //$captcha=input('captcha');
        if(!captcha_check($userForm->captcha)){
            //$this->redirect('auth/login');
            return Json(['result'=>'error','message'=>'验证码不正确','data'=>'']);
        }

        $superAdmin=$this->_user->getSuperAdmin();
        //dump($superAdmin);

        if($superAdmin['phone']!=$userForm->username && $superAdmin['email']!=$userForm->username){
              return Json(['result'=>'error','message'=>'用户名不正确','data'=>'']);
        }

        if(md5($userForm->password.$superAdmin['token'])!==$superAdmin['password']){
            return Json(['result'=>'error','message'=>'用户密码不正确','data'=>'']);
        }


        $user=new stdClass;
        $user->id=$superAdmin['id'];
        $user->name=$superAdmin['name'];
        $user->phone=$superAdmin['phone'];
        $user->email=$superAdmin['email'];
        $user->password=$userForm->password;
        Session::set('user',$user);
        //dump($captcha);

        return Json(['result'=>'success','message'=>'','data'=>URL('index')]);
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
