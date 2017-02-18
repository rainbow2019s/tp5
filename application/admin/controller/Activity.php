<?php
namespace app\admin\controller;

use think\Controller;
//use think\Db;
//use think\Log;
use think\View;
use think\Session;


use ecopro\SubMenu;
use ecopro\AjaxOutput;
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

    public function index()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $menu=[];
        $subMenu=SubMenu::getMenu('platform'); 
        foreach($subMenu['submenu'] as $m ){  
   
             $active='no_hover';
            if($m['name']=='应用管理'){
                 $active='hover';
            }
           $menu[]=$active;
            
        } 
        $this->assign('menu',SubMenu::getMenu('platform'));
        $this->assign('active',$menu);

        return view();
    }

    public function bind()
    {
        $user=Session::get('user');
        $this->assign('user',$user);
        $this->assign('menu',SubMenu::getMenu('platform'));

        return view();
    }

    //---------------------------------------------------------------------

    // 获取所有有效的应用
    public function ajaxQueryAllActivities()
    {
        return Json($this->_activity->ajaxQueryAllActivities());
    }


    /**
     * 注册应用
     * $activity=json_decode($params);
     * $activity = new stdClass;
     * $activity->name='知识库3';
     * $activity->domain='';
     * $activity->host_ip_address='127.0.0.1';
     * $activity->entrance_url='/tech3index';
     * $activity->entrance_alias='知识问答3';
     *
     */
    public function ajaxActivityAdd()
    {
        $params= input('params');
        $activity=json_decode($params);

        $rows=$this->_activity->ajaxActivityAdd($activity);

        return $rows>0?Json(AjaxOutput::success('新增应用成功')):Json(AjaxOutput::error('新增应用失败')); 

    }

    /**
     * 返回单个Activity应用记录
     *
     */
    public function ajaxQueryActivity()
    {
        $params=input('params');
        $activity=json_decode($params);

        $activity=$this->_activity->ajaxQueryActivity($activity);

        return isset($activity)?Json(AjaxOutput::success('操作成功',$activity)):
            Json(AjaxOutput::error('记录不存在'));
    }

    /**
     * 更新一个功能应用
     *
     */
    public function ajaxEditActivity()
    {
        $params=input('params');
        $activity=json_decode($params);

        $rows=$this->_activity->ajaxEditActivity($activity);

        return $rows>0?Json(AjaxOutput::success('应用更新成功')):Json(AjaxOutput::error('应用更新失败'));  
    }

    /**
     * 移除一个小应用
     *
     */
    public function ajaxRemoveActivity()
    {
        $params=input('params');
        $activity=json_decode($params);

        $status=$this->_activity->ajaxRemoveActivity($activity);

        return $status?Json(AjaxOutput::success('应用删除成功')):Json(AjaxOutput::error('应用删除失败'));  

    }

}
