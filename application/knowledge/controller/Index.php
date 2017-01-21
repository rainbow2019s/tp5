<?php
namespace app\knowledge\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

use app\knowledge\model\Category as CategoryModel;
//use app\knowledge\model\Prevention as PreventionModel;
use app\admin\model\User as UserModel;

use ecopro\AjaxOutput;
use ecopro\SubMenu;

class Index extends Controller
{
    private $_user;
    private $_category;
    //private $_prevention;

    public function __construct(UserModel $userModel,CategoryModel $categoryModel)
    {
        parent::__construct();

        $this->_user=$userModel;
        $this->_category=$categoryModel;
        //$this->_prevention=$preventionModel;

    }


    public function reset()
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


    // ------------------------------------------------------------------
    // 微信入口页
    // ------------------------------------------------------------------
    public function index()
    {
        return view();
    }

    /**
     * 第一层查询 获取农作物分类和名称
     *
     */
    public function ajaxCategory()
    {
        $categories=[];

        $result=$this->_category->ajaxCategory();
        if(!empty($result)){
            foreach($result as $item){
                $category=AjaxOutput::toObject($item);
                $category->items=$this->_category->ajaxSubCategory($category->id);
                $categories[]=$category;
            }
        }

        return Json(AjaxOutput::success('',$categories));
    }

    /**
     * 第二层查询 根据农作物名称得到病虫
     *
     */
    public function ajaxSubCategory()
    {
        $params=input('params');
        $node=json_decode($params);

        $pests=[];
        $result=$this->_category->ajaxSubCategory($node->id);

        foreach($result as $item){
            $pest=AjaxOutput::toObject($item);
            $file1=substr(ROOT_PATH,0,-1).$pest->img_url_1;
            $file2=substr(ROOT_PATH,0,-1).$pest->img_url_2;
            
            $pest->img_url_1=file_exists($file1)?file_get_contents($file1):'';
            $pest->img_url_2=file_exists($file2)?file_get_contents($file2):'';
            $pests[]=$pest;
        }

        return Json(AjaxOutput::success('',$pests));
    }


    public function ajaxDetail()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);
        $file_name=substr(ROOT_PATH,0,-1).$element['file_name'];
        if(empty($element['file_name']) || !file_exists($file_name)){            
            return Json(['id'=>'',
                'feature'=>'',
                'law'=>'',
                'prevention'=>'',
                'measure'=>'',
                'skill'=>'',
                'images'=>[]]);
        }

        $file = file_get_contents($file_name);
        return json_decode($file);
    }


    public function ajaxSearch()
    {
        //$word='    上海青 虫病 ';
        $params=input('params');
        $target=json_decode($params);
        $text=trim($target->word);

        $detail=$this->_category->ajaxDetailSearch($text);
        if(!empty($detail)){
            return Json(AjaxOutput::success('detail',$detail));
        }

        $categories=$this->_category->ajaxCategorySearch($text);
        if(!empty($categories)){
            return Json(AjaxOutput::success('category',$categories));
        }

        return Json(AjaxOutput::error('农作物病虫不存在'));


        // $crop='';
        // $pest='';
        // $pattern = mb_substr(trim($text),-1,1)!='病'?'category':'detail';
        // if($pattern=='detail'){
        //     // 切词
        //     $length=mb_strlen($text);
        //     if($length<4){
        //         return Json(AjaxOutput::error('农作物病虫不存在'));
        //     }
        //     $pest=trim(mb_substr($text,-3,3));
        //     $crop=trim(mb_substr($text,0,$length-3));
        // }


        // if($pattern=='category'){
        //     $categories=$this->_category->ajaxCategorySearch($text);
        //     return Json(AjaxOutput::success('',$categories));
        // }

        // $detail=$this->_category->ajaxDetailSearch($crop,$pest);
        // return Json(AjaxOutput::success('',$detail));
    }

}
