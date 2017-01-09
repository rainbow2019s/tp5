<?php
namespace app\admin\controller;

use think\Controller;
//use think\Db;
use think\Log;
use think\View;


use stdClass;
use think\Session;
use app\admin\model\User as UserModel;
use ecopro\SubMenu;
use ecopro\AjaxOutput;


/**
 * 登录控制器
 * author: l.hao
 * ver: 1.0.1 2017.01.08 重构视图类
 */

class Auth extends Controller
{
    /**
     * 用户模型类
     *
     */
    private $_user;

    /**
     * 构造方法
     * @params UserModel
     *
     * @throws Exception
     */
    public function __construct(UserModel $userModel)
    {
        parent::__construct();

        $this->_user=$userModel;

    }

     // --------------------------------------------------------------------------------------------------
     // 登录开始
     // --------------------------------------------------------------------------------------------------

    /**
     * 进入首页入口
     *
     * @throws Exception
     */
    public function index()
    {
        $exist = Session::has('user');
        // 如果Session中没有用户信息，则跳转登录页面
        if(empty($exist)){
            $this->redirect('auth/login');           
        }
        // 如果Session中存在用户信息，则跳转至用户首页入口
        $this->redirect('user/index');

    }

    /**
     * 后台登录视图
     *
     */
    public function login()
    {
        return view();
    }

    /**
     * Ajax提交后台登录
     * 用户登录后在Session里应保存的信息
     *  
     * @params username
     * @params password
     */
    
    public function ajaxlogin()
    {
         $params=input('params');
         $userForm=json_decode($params);

        if(!captcha_check($userForm->captcha)){
            return Json(AjaxOutput::error('验证码不正确'));
        }

        // 用户名不存在返回 bool(false)
        // 用户存在返回一维关联数组
        $singleResult=$this->_user->getSuperAdmin($userForm);
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


        $user=new stdClass;
        $user->id=$admin->id;
        $user->name=$admin->name;
        $user->phone=$admin->phone;
        $user->email=$admin->email;
        $user->password=$userForm->password;
        // 存入session
        Session::set('user',$user);

        return Json(AjaxOutput::success('',URL('index')));
    }


    // --------------------------------------------------------------------------------------------------

    /**
     * 退出系统
     *
     */

    public function logout()
    {
        Session::clear();
        Session::destroy();

        return $this->redirect('auth/index');
    }

    /**
     * 口令重置视图
     *
     */
    public function reset()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $this->assign('menu',SubMenu::getMenu('platform'));

        return view();
    }

    /**
     * 口令重置提交
     *
     */

    public function resetSubmit()
    {        
        $password=input('password1');

        $user=Session::get('user');
        $user->token=uniqid();
        $user->password=md5($password.$user->token);

        // 返回受影响行数 0 表示失败
        $rows=$this->_user->resetSubmit($user);

        if($rows>0){

            $this->redirect('auth/logout');
        }

        // 不成功处理返回重置页
        $this->redirect('auth/reset');

    }

}
