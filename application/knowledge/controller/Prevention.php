<?php
namespace app\knowledge\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

use app\knowledge\model\Category as CategoryModel;
use app\knowledge\model\Prevention as PreventionModel;
use app\admin\model\User as UserModel;

use ecopro\AjaxOutput;
use ecopro\SubMenu;

class Prevention extends Controller
{
    private $_user;
    private $_category;
    //private $_prevention;


    public function __construct(UserModel $userModel,CategoryModel $categoryModel
       /*PreventionModel $preventionModel*/)
    {
        parent::__construct();

        $this->_user=$userModel;
        $this->_category=$categoryModel;
        //$this->_prevention=$preventionModel;

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
     * 根据一个父结点返回下一层子集（深度为3没有子集）
     * URL参数params中应包含parent_id
     */
    public function ajaxQuery()
    {
        $params=input('params');
        $node=json_decode($params);

        $rows=$this->_category->ajaxQuery($node);

        return Json(AjaxOutput::success('',$rows));

    }

    public function ajaxElement()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);

        if(empty($element['file_name'])){
            
            return Json(['id'=>'',
                'feature'=>'',
                'law'=>'',
                'prevention'=>'',
                'measure'=>'',
                'skill'=>'',
                'images'=>[]]);
        }

        //$file_name=substr(ROOT_PATH,0,-1).$element['file_name'];
        $file_name=ROOT_PATH.$element['file_name'];
        // 转换为发布文件 2017-03-08
        //$source = str_replace('tmp.json','json',$file_name);
        $file = file_get_contents($file_name);

        return json_decode($file);
    }

    // 暂存文件 2017-03-08
    public function ajaxEdit()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);
                
        $file_name=uniqid();
        $file_name="public".DS."uploads".DS."knowledge".DS."lib".DS."{$file_name}.tmp.json";
        if(!empty($element['file_name'])){
            //$file_name=substr(ROOT_PATH,0,-1).$element['file_name'];
            $file_name=ROOT_PATH.$element['file_name'];
            @unlink($file_name);
            $file_name=$element['file_name'];
        }
        
        // 加入tmp 2017-03-08
        //$json_file="public".DS."uploads".DS."knowledge".DS."lib".DS."{$file_name}.tmp.json";
        $json_file=$file_name;
        $path=ROOT_PATH.$json_file;
        file_put_contents($path,$params);

        //$node->file_name=DS.$json_file;
        $node->file_name=$json_file;
        $rows=$this->_category->ajaxThirdLevelTemplateEdit($node);
        
        return $rows>0?Json(AjaxOutput::success('暂存成功')):Json(AjaxOutput::error('暂存失败'));
    }

    // 发布文件 2017=03=08
    public function ajaxRelease()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);

        if(!empty($element['file_name'])){
            //$source=substr(ROOT_PATH,0,-1).$element['file_name'];
            $source=ROOT_PATH.$element['file_name'];
            $dest=str_replace('tmp.json','json',$source);
            copy($source,$dest);
        }

        return AjaxOutput::success('发布成功');
    }

}
