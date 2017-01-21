<?php
namespace app\admin\controller;

use think\Controller;
use think\Url;
use think\Session;

use ecopro\SubMenu;
use ecopro\AjaxOutput;
use app\admin\model\User as UserModel;


class User extends Controller
{
    private $_user;

    public function __construct(UserModel $userModel)
    {
        parent::__construct();
        $this->_user=$userModel;
    }



    public function index()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $this->assign('menu',SubMenu::getMenu('platform'));

        return view();
    }


    /**
     * 获取所有有效的管理员
     *
     */
    public function ajaxQueryAllUsers()
    {
        return Json($this->_user->ajaxQueryAllUsers());
    }


    /**
     * 创建管理员
     *
     */
    public function ajaxAddUser()
    {

        $params=input('params');
        $admin=json_decode($params);

        // 默认是普通管理员，口令是123456
        $admin->is_super=0;
        $admin->token=uniqid();
        $admin->password=md5('123456'.$admin->token);

        // 判断手机号是否唯一
        $phone=$this->_user->phoneIsUnique($admin->phone,NULL);
        if($phone['amount']>0){
            return Json(AjaxOutput::error('手机号码已存在'));
        }

        // 判断邮箱是否唯一
        $email=$this->_user->emailIsUnique($admin->email,NULL);
        if($email['amount']>0){
            return Json(AjaxOutput::error('邮箱已存在'));
        }

        $rows=$this->_user->ajaxAddUser($admin);
        
        return $rows>0?Json(AjaxOutput::success('创建管理员成功')):Json(AjaxOutput::error('创建管理员失败'));

    }

    /**
     * 获取指定管理员记录
     *
     */

    public function ajaxQueryUser()
    {
        $params=input('params');
        $admin=json_decode($params);

        $admin=$this->_user->ajaxQueryUser($admin);

        return isset($admin)?Json(AjaxOutput::success('请求成功',$admin)):Json(AjaxOutput::error('请求失败'));
    }

    /**
     * 编辑普通管理员
     *
     */

    public function ajaxEditUser()
    {
        $params=input('params');
        $admin=json_decode($params);

        $phone=$this->_user->phoneIsUnique($admin->phone,$admin->id);
        if($phone['amount']>0){
            return Json(AjaxOutput::error('手机号码已存在'));
        }

        $email=$this->_user->emailIsUnique($admin->email,$admin->id);
        if($email['amount']>0){
            return Json(AjaxOutput::error('邮箱已存在'));
        }

        $rows=$this->_user->ajaxEditUser($admin);

        return $rows>0?Json(AjaxOutput::success('管理员帐号更新成功')):Json(AjaxOutput::error('管理员帐号更新失败'));
    }

    /**
     * 清除管理员帐号
     * 级联清除 管理员绑定了功能也要清除
     *
     */
    public function ajaxUserRemove()
    {
        $params=input('params');
        $user=json_decode($params);

        $status=$this->_user->ajaxUserRemove($user);

        return $status?Json(AjaxOutput::success('删除成功')):Json(AjaxOutput::error('删除失败'));  
    }

    /**
     * 在超级管理员和普通管理员之间的区域
     *
     */
    public function ajaxUserRightsChange()
    {
        $params=input('params');
        $admin=json_decode($params);

        $status=$this->_user->ajaxUserRightsChange($admin);
        
        return $status?Json(AjaxOutput::success('权限修改成功')):Json(AjaxOutput::error('权限修改失败'));    

    }


    // -------------------------------------------------------------------------
    // 管理员功能绑定
    // -------------------------------------------------------------------------

    /**
     * 普通管理员绑定应用功能视图
     *
     */
    public function bind()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $this->assign('menu',SubMenu::getMenu('platform'));

        return view();
    }

    /**
     * 获取管理员绑定的后台管理模块
     *
     */
    public function ajaxAdminAllActivities()
    {
        $adminUsers=[];
        $users=$this->_user->ajaxQueryAllAdminUsers();
        foreach($users as $user){
            $adminUser=AjaxOutput::toObject($user);
            
            $adminUser->checklist=$this->_user->ajaxAdminBindAllActivities($adminUser);
            $adminUsers[]=$adminUser;
        }

        return Json(AjaxOutput::success('',$adminUsers));
    }

    /**
     * 普通管理员功能绑定
     *
     */

    public function ajaxAdminBinding()
    {
        $params=input('params');
        $rights=json_decode($params);

        $status=$this->_user->ajaxAdminBinding($rights);

        return $status?Json(AjaxOutput::success('修改权限成功')):Json(AjaxOutput::error('修改权限失败'));

    }
}
