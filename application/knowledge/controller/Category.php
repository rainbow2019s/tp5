<?php
namespace app\knowledge\controller;

use think\Controller;
use think\View;
use think\Request;

use app\knowledge\model\Category as CategoryModel;

class Category extends Controller
{
    private $_category;


    public function __construct(CategoryModel $categoryModel)
    {
        parent::__construct();

        $this->_category=$categoryModel;
    }


    public function index()
    {
        return view();
    }

    public function upload()
    {
        $file=$this->request->file('image');
        $path=ROOT_PATH.'public'.DS.'uploads'.DS.'knowledge';
        $info=$file->move($path);
        if($info){
            echo $info->getSaveName();
            exit;
        }

        echo $file->getError();
    }


    public function add()
    {
        //dump($this->_categ)

        $node=new \stdClass;
        $node->name='西瓜';
        $node->classify='作物类';
        $node->img_url='20161227/f49b3b3d3be526c0aa2fbb94237e21cf.jpg';
        $node->parent_id=0;
        $node->parent_name='';
        $node->popular='瓜果类';
        $node->academic='葫芦科';

        $rows=$this->_category->addCropAndDiseaseNode($node);
        return $rows;

    }

    public function add2()
    {
        //dump($this->_categ)

        $node=new \stdClass;
        //$node->name='戴帽出土';
        $node->name='猝倒病';
        $node->classify='病害类';
        $node->img_url='20161227/f49b3b3d3be526c0aa2fbb94237e21cf.jpg';
        $node->parent_id=1;
        $node->parent_name='西瓜';
        $node->popular='';
        $node->academic='';

        $rows=$this->_category->addCropAndDiseaseNode($node);
        return $rows;

    }

    public function query()
    {
        //$result=$this->_category->getCropClassify();
        //$result=$this->_category->getSelectCropDisease(1);
        $result=$this->_category->getBlurSearch('猝倒');

        return Json($result);
    }

    public function getCropClassify($callback='')
    {
        return $callback!=''?Json($this->_category->getCropClassify()):Jsonp($this->_category->getCropClassify());
    }
}
