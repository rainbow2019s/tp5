<?php
namespace app\knowledge\model;

use think\Db;
use think\Model;

class Category extends Model
{

    // 返回所有作物分类
    public function getCropClassify()
    {
        return Db::query('select id,name,classify,img_url,parent_id,popular,academic
            from ep_category where parent_id=0');

    }

    // 返回当前作物结点的所有病虫害
    public function getSelectCropDisease($parent_id)
    {
        return Db::query('select id,name,classify,img_url,parent_id,parent_name
            from ep_category where parent_id=?',[$parent_id]);

    }

    // 返回所有查询字相关的的病虫害结点
    public function getBlurSearch($word)
    {
        return Db::query("select id,concat(parent_name,'-',name) as name,classify,img_url
                from ep_category where name regexp ?",[$word]);
    }

    // 新增一个作物或病虫害节点
    // 如果是病虫害节点（popular和academic可选）
    public function addCropAndDiseaseNode($node)
    {
        $rows = Db::execute('insert into ep_category(name,classify,img_url,
            parent_id,parent_name,popular,academic,timestamp) values(
                :name,:classify,:img_url,:parent_id,:parent_name,:popular,:academic,now())',
                ['name'=>$node->name,
                    'classify'=>$node->classify,
                    'img_url'=>$node->img_url,
                    'parent_id'=>$node->parent_id,
                    'parent_name'=>$node->parent_name,
                    'popular'=>$node->popular,
                    'academic'=>$node->academic]); 

        return $rows;
    }


    // 删除一个作物或病虫害节点节点
    public function remove($id)
    {
        Db::startTrans();
        try{
            Db::execute('update ep_detail set ep_category_id = NULL where ep_category_id=?',[$id]);
            Db::execute('delete from ep_category where parent_id=?',[$id]);               
            Db::execute('delete from ep_category where id=?',[$id]);           

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;      
    }
}
