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

        $file_name=substr(ROOT_PATH,0,-1).$element['file_name'];
        $file = file_get_contents($file_name);

        return json_decode($file);
    }

    public function ajaxEdit()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);

        if(!empty($element['file_name'])){
            $file_name=substr(ROOT_PATH,0,-1).$element['file_name'];
            unlink($file_name);
        }
        
        $file_name=uniqid();
        $json_file="public".DS."uploads".DS."knowledge".DS."lib".DS."{$file_name}.json";
        $path=ROOT_PATH.$json_file;
        file_put_contents($path,$params);

        $node->file_name=DS.$json_file;
        $rows=$this->_category->ajaxThirdLevelTemplateEdit($node);
        
        return $rows>0?Json(AjaxOutput::success('文件编辑成功')):Json(AjaxOutput::error('文件编辑失败'));
    }

    

    // public function upload()
    // {
    //     $file=$this->request->file('image');
    //     $path=ROOT_PATH.'public'.DS.'uploads'.DS.'knowledge';
    //     $info=$file->move($path);
    //     if($info){
    //         echo $info->getSaveName();
    //         exit;
    //     }

    //     echo $file->getError();
    // }


    // public function add()
    // {
    //     //dump($this->_categ)

    //     $node=new \stdClass;
    //     $node->name='西瓜';
    //     $node->classify='作物类';
    //     $node->img_url='20161227/f49b3b3d3be526c0aa2fbb94237e21cf.jpg';
    //     $node->parent_id=0;
    //     $node->parent_name='';
    //     $node->popular='瓜果类';
    //     $node->academic='葫芦科';

    //     $rows=$this->_category->addCropAndDiseaseNode($node);
    //     return $rows;

    // }

    // public function add2()
    // {
    //     //dump($this->_categ)

    //     $node=new \stdClass;
    //     //$node->name='戴帽出土';
    //     $node->name='猝倒病';
    //     $node->classify='病害类';
    //     $node->img_url='20161227/f49b3b3d3be526c0aa2fbb94237e21cf.jpg';
    //     $node->parent_id=1;
    //     $node->parent_name='西瓜';
    //     $node->popular='';
    //     $node->academic='';

    //     $rows=$this->_category->addCropAndDiseaseNode($node);
    //     return $rows;

    // }

    // public function query()
    // {
    //     //$result=$this->_category->getCropClassify();
    //     //$result=$this->_category->getSelectCropDisease(1);
    //     $result=$this->_category->getBlurSearch('猝倒');

    //     return Json($result);
    // }

    // public function getCropClassify($callback='')
    // {
    //     return $callback!=''?Json($this->_category->getCropClassify()):Jsonp($this->_category->getCropClassify());
    // }
}
