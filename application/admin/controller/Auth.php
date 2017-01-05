<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Log;
use think\View;


use stdClass;
use think\Session;
use app\admin\model\User as UserModel;
use ecopro\SubMenu;

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

        $this->redirect('user/index');

    }

    // 后台登录
    public function login()
    {
        return view();
    }

    // Ajax提交后台登录
    // 用户登录后在Session里应保存的信息
    
    public function ajaxlogin()
    {
         $params=input('params');
         $userForm=json_decode($params);

        if(!captcha_check($userForm->captcha)){
            return Json(['result'=>'error','message'=>'验证码不正确','data'=>'']);
        }

        $superAdmin=$this->_user->getSuperAdmin();

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

        return Json(['result'=>'success','message'=>'','data'=>URL('index')]);
    }

    public function ajaxlogout()
    {
        Session::clear();
        Session::destroy();

        return Json(['result'=>'success','message'=>'','data'=>URL('auth/index')]);
    }


    public function reset()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $this->assign('menu',SubMenu::getMenu('platform'));

        return view();
    }

    public function resetSubmit()
    {
        
        $password=input('password1');

        $user=Session::get('user');
        $user->token=uniqid();
        $user->password=md5($password.$user->token);

        $rows=$this->_user->resetSubmit($user);
        // 大于0表示成功
        if($rows>0){
            Session::clear();
            Session::destroy();

            $this->redirect('admin/auth/index');
        }

        // TODO: 不成功处理
        $this->redirect('auth/reset');

    }

}
