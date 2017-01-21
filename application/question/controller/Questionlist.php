<?php
namespace app\question\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

use app\admin\model\User as UserModel;
use app\question\model\Questionlist as QuestionlistModel;

use ecopro\AjaxOutput;
use ecopro\SubMenu;




class Questionlist extends Controller
{
    private $_user='';
    private $_questionlist='';

    public function __construct(UserModel $userModel,QuestionlistModel $questionlistModel)
    {
        parent::__construct();
        $this->_user=$userModel;
        $this->_questionlist=$questionlistModel;
        
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
     * 需要审核贴子的列表
     *
     */

    public function ajaxQuerylist()
    {
        $params=input('params');
        $page=json_decode($params);

        // $page=new \stdClass;
        // $page->num=1;
        // $page->size=10;
        // $page->text='aa';

        $page->total=$this->_questionlist->getQuestionlistTotal($page)['amount'];
        $page->count=ceil(intval($page->total)/intval($page->size));
        $result=$this->_questionlist->ajaxQuestionlist($page);

        $questions=[];
        //TODO:加入图片        
        foreach($result as $item){
            $question=AjaxOutput::toObject($item);
            $question->images=[];
            $files=$this->_questionlist->getGallery($question->id);
            foreach($files as $file){
                $image=substr(ROOT_PATH,0,-1).$file['file_name'];
                $question->images[]=file_get_contents($image);        
            }
            $questions[]=$question;
        }

        return Json(AjaxOutput::success($page,$questions));
    }

    /**
     * 贴子审核
     * 成功后再次调用ajaxQuerylist方法，已审贴子不再显示
     */
    public function ajaxIsAudiit()
    {
        $params=input('params');
        $page=json_decode($params);

        $rows=$this->_questionlist->ajaxIsAudit($page->id);       
        return $rows>0?Json(AjaxOutput::success('审贴成功')):Json(AjaxOutput::error('审贴失败'));
    }

    /**
     * 删贴操作
     * 成功后再次调用ajaxQuerylist方法，已删贴吧子不再显示
     */

    public function ajaxRemove()
    {
        $params=input('params');
        $page=json_decode($params);

        $files=$this->_questionlist->getGallery($page->id);
        foreach($files as $file){
            $image=substr(ROOT_PATH,0,-1).$file->file_name;
            //$question->images[]=file_get_contents($image);        
            @unlink($image);
        }

        $status=$this->_questionlist->ajaxRemove($page->id);  
        return $status?Json(AjaxOutput::success('删贴成功')):Json(AjaxOutput::error('删贴失败'));
    }

}