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




class Sensitive extends Controller
{
    private $_user='';
    //private $_questionlist='';

    public function __construct(UserModel $userModel)
    {
        parent::__construct();
        $this->_user=$userModel;
        //$this->_questionlist=$questionlistModel;
        
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
     * 保留敏感字库
     *
     */
    public function ajaxSensitiveSubmit()
    {
        $params=input('params');
        $sensitive=json_decode($params);

        $file=ROOT_PATH."public".DS."uploads".DS."question".DS."sensitive.txt";
        file_put_contents($file,$sensitive);

        return Json(AjaxOutput::success('修改敏感词库成功'));
    }

    /**
     * 读取敏感字库
     *
     */
    public function ajaxSensitiveEdit()
    {
        $file=ROOT_PATH."public".DS."uploads".DS."question".DS."sensitive.txt";
        
        return file_exists($file)?file_get_contents($file):'';
    }


}    
