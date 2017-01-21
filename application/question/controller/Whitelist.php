<?php
namespace app\question\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

use app\admin\model\User as UserModel;
use app\question\model\Whitelist as WhitelistModel;

use ecopro\AjaxOutput;
use ecopro\SubMenu;




class Whitelist extends Controller
{
    private $_user='';
    private $_whitelist='';

    public function __construct(UserModel $userModel,WhitelistModel $whitelistModel)
    {
        parent::__construct();
        $this->_user=$userModel;
        $this->_whitelist=$whitelistModel;
        
    }


    public function index()
    {
        $user=Session::get('user');
        $this->assign('user',$user);

        $menu=[];
        $titles=$this->_user->getAdminBindActivities($user);
        foreach($titles as $title){
            $obj=AjaxOutput::toObject($title);
            $obj->submenu=SubMenu::getMenu($obj->alias)['submenu'];
            $menu[]=$obj;
        }

        $this->assign('menu',$menu);

        return view();
    }

    /**
     * 获取白名单列表
     * $page {num,size,total}
     * /list/:num/:size
     */
    public function  ajaxWhitelist()
    {
        $params=input('params');
        $page=json_decode($params);

        $page->total=$this->_whitelist->getWhitelistTotal($page)['amount'];
        $page->count=ceil(intval($page->total)/intval($page->size));
        $users=$this->_whitelist->ajaxWhitelist($page);

        return Json(AjaxOutput::success($page,$users));
    }

    /**
     * 新增专家
     *
     */
    public function ajaxExpertAdd()
    {
        $params=input('params');
        $expert=json_decode($params);

        $rows=$this->_whitelist->ajaxExpertAdd($expert);
        
        return $rows>0?Json(AjaxOutput::success('新增专家成功')):Json(AjaxOutput::error('新增专家失败'));

    }

    public function ajaxUserEdit()
    {
        $params=input('params');
        $user=json_decode($params);

        $user=$this->_whitelist->ajaxUserEdit($user);

        return Json(AjaxOutput::success('',$user));
    }

    /**
     * 修改允许还是禁止
     *
     */
    public function ajaxExpertEditSubmit()
    {
        $params=input('params');
        $expert=json_decode($params);

        $rows=$this->_whitelist->ajaxExpertEditSubmit($expert);
        
        return $rows>0?Json(AjaxOutput::success('编辑专家成功')):Json(AjaxOutput::error('编辑专家失败'));
    }


    /**
     *  修改允许还是禁止
     *
     */
    public function ajaxEnabledStatus()
    {
        $params=input('params');
        $user=json_decode($params);

        $rows=$this->_whitelist->ajaxEnabledStatus($user->value,$user->id);

        return $rows>0?Json(AjaxOutput::success('修改状态成功')):Json(AjaxOutput::error('修改状态失败'));
    }

    /**
     * 修改白名单状态
     *
     */
    public function ajaxWhiteStatus()
    {
        $params=input('params');
        $user=json_decode($params);

        $rows=$this->_whitelist->ajaxWhiteStatus($user->value,$user->id);

        return $rows>0?Json(AjaxOutput::success('修改白名单成功')):Json(AjaxOutput::error('修改白名单失败'));
    }

    /**
     * 删除用户
     *
     */
    public function ajaxUserRemove()
    {
        $params=input('params');
        $user=json_decode($params);

        $rows=$this->_whitelist->ajaxUserRemove($user->id);

        return $rows>0?Json(AjaxOutput::success('删除成功')):Json(AjaxOutput::error('删除失败'));
    }

    // /**
    //  * 保留敏感字库
    //  *
    //  */
    // public function ajaxSensitiveSubmit()
    // {
    //     $params=input('params');
    //     $sensitive=json_decode($params);

    //     $file=ROOT_PATH."public".DS."uploads".DS."question".DS."sensitive.txt";
    //     file_put_contents($file,$sensitive);

    //     return Json(AjaxOutput::success('修改敏感词库成功'));
    // }

    // /**
    //  * 读取敏感字库
    //  *
    //  */
    // public function ajaxSensitiveEdit()
    // {
    //     $file=ROOT_PATH."public".DS."uploads".DS."question".DS."sensitive.txt";
        
    //     return file_exists($file)?file_get_contents($file):'';
    // }

}