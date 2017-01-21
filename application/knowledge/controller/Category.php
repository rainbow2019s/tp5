<?php
namespace app\knowledge\controller;

use think\Controller;
use think\View;
use think\Session;
use think\Request;

use app\knowledge\model\Category as CategoryModel;
use app\admin\model\User as UserModel;

use ecopro\AjaxOutput;
use ecopro\SubMenu;

class Category extends Controller
{
    private $_user;
    private $_category;


    public function __construct(UserModel $userModel,CategoryModel $categoryModel)
    {
        parent::__construct();
        $this->_user=$userModel;
        $this->_category=$categoryModel;
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
     * 增加一个节点（深度最大为3）
     * 客户端算法：深度优先
     */
    public function ajaxAdd()
    {
        $params=input('params');
        $node=json_decode($params);
        $rows=0;
         switch($node->depth){
             case 1:
                $rows=$this->_category->ajaxFirstLevelAdd($node);
                break;
            case 2:
                $rows=$this->_category->ajaxSecondLevelAdd($node);
                break;
            case 3:
                $image1=uniqid();
                $image2=uniqid();

                $img_url_1="public".DS."uploads".DS."knowledge".DS."lib".DS."{$image1}.jpg";
                $img_url_2="public".DS."uploads".DS."knowledge".DS."lib".DS."{$image2}.jpg";

                $path1=ROOT_PATH.$img_url_1;
                $path2=ROOT_PATH.$img_url_2;

                file_put_contents($path1,$node->img_url_1);
                file_put_contents($path2,$node->img_url_2);

                $node->img_url_1=DS.$img_url_1;
                $node->img_url_2=DS.$img_url_2;
                $rows=$this->_category->ajaxThirdLevelAdd($node);

                break;

        }

        return $rows>0?Json(AjaxOutput::success('添加节点成功')):Json(AjaxOutput::error('添加节点失败'));
    }

        /**
     * 增加一个节点（深度最大为3）
     * 客户端算法：深度优先
     */
    public function ajaxEdit()
    {
        $params=input('params');
        $node=json_decode($params);
        $rows=0;
         switch($node->depth){
             case 1:
                $rows=$this->_category->ajaxFirstLevelEdit($node);
                break;
            case 2:
                $rows=$this->_category->ajaxSecondLevelEdit($node);
                break;
            case 3:

                $element=$this->_category->getElement($node->id);

                if($node->changed){
                    
                    $file1=substr(ROOT_PATH,0,-1).$element['img_url_1'];
                    @unlink($file1);

                    $file2=substr(ROOT_PATH,0,-1).$element['img_url_2'];
                    @unlink($file2);

                    $image1=uniqid();
                    $img_url_1="public".DS."uploads".DS."knowledge".DS."lib".DS."{$image1}.jpg";
                    $path1=ROOT_PATH.$img_url_1;
                    file_put_contents($path1,$node->img_url_1);

                    $image2=uniqid();
                    $img_url_2="public".DS."uploads".DS."knowledge".DS."lib".DS."{$image2}.jpg";
                    $path2=ROOT_PATH.$img_url_2;
                    file_put_contents($path2,$node->img_url_2);

                    $node->img_url_1=DS.$img_url_1;
                    $node->img_url_2=DS.$img_url_2;
                
                }else{

                    $node->img_url_1=$element['img_url_1'];
                    $node->img_url_2=$element['img_url_2'];
                }

                $rows=$this->_category->ajaxThirdLevelEdit($node);
                break;

        }

        return $rows>0?Json(AjaxOutput::success('更新节点成功')):Json(AjaxOutput::error('更新节点失败'));
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

    /**
     * 根据一个id返回节点
     *
     */
    public function ajaxElement()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);

        if($node->depth==3){
            $file1=substr(ROOT_PATH,0,-1).$element['img_url_1'];
            $file2=substr(ROOT_PATH,0,-1).$element['img_url_2'];
            
            $element['img_url_1']=file_get_contents($file1);
            $element['img_url_2']=file_get_contents($file2);
        }

        return Json(AjaxOutput::success('',$element));
    }

    /**
     * 删除一个节点
     *
     */
    public function ajaxRemove()
    {
        $params=input('params');
        $node=json_decode($params);

        $element=$this->_category->getElement($node->id);
        if($element['depth']==3){
            // 图片也要清除
            if(!empty($element['img_url_1'])){
                $file1=substr(ROOT_PATH,0,-1).$element['img_url_1'];
                @unlink($file1);
            }

            if(!empty($element['img_url_2'])){
                $file2=substr(ROOT_PATH,0,-1).$element['img_url_2'];
                @unlink($file2);
            }
        }

        $rows=$this->_category->ajaxRemove($node);        

        return $rows>0?Json(AjaxOutput::success('删除结点成功')):Json(AjaxOutput::error('删除结点失败'));
    }
}
