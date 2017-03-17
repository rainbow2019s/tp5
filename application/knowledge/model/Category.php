<?php
namespace app\knowledge\model;

use think\Db;
use think\Model;

class Category extends Model
{
    /**
     * 树的第一层节点新增
     *
     */
    public function ajaxFirstLevelAdd($node)
    {
        return Db::execute('insert into tech_category(name,academic,parent_id,depth,weight,timestamp) 
            values(:name,:academic,:parent_id,:depth,:weight,now())',
            ['name'=>$node->name,'academic'=>$node->academic,'parent_id'=>$node->parent_id,
                'depth'=>$node->depth,'weight'=>$node->weight]);
    }  


    public function nameHasExist($node){

        $sql="select count(*) as amount from tech_category where name=:name and id<>:id";
        
        $rows= Db::query($sql,['name'=>$node->name,'id'=>isset($node->id)?$node->id:0]);       
        return reset($rows);
    } 

    /**
     * 树的第一层节点更新
     *
     */

    public function ajaxFirstLevelEdit($node)
    {
        return Db::execute('update tech_category set name=:name,academic=:academic,weight=:weight,timestamp=now()
            where id=:id',
            ['name'=>$node->name,'academic'=>$node->academic,'weight'=>$node->weight,'id'=>$node->id]);
    }

    /**
     * 树的第二层节点新增
     *
     */
    public function ajaxSecondLevelAdd($node)
    {
        return Db::execute('insert into tech_category(name,popular,parent_id,depth,weight,timestamp) 
            values(:name,:popular,:parent_id,:depth,:weight,now())',
            ['name'=>$node->name,'popular'=>$node->popular,'parent_id'=>$node->parent_id,
                'depth'=>$node->depth,'weight'=>$node->weight]);
    }


    /**
     * 树的第二层节点更新
     *
     */
    public function ajaxSecondLevelEdit($node)
    {
        return Db::execute('update tech_category set name=:name,popular=:popular,weight=:weight,timestamp=now()
            where id=:id',
            ['name'=>$node->name,'popular'=>$node->popular,'weight'=>$node->weight,'id'=>$node->id]);
    }

    /**
     * 树的第三层节点新增
     *
     */
    public function ajaxThirdLevelAdd($node)
    {
        return Db::execute('insert into tech_category(name,img_url_1,img_url_2,parent_id,depth,timestamp) 
            values(:name,:img_url_1,:img_url_2,:parent_id,:depth,now())',
            ['name'=>$node->name,'img_url_1'=>$node->img_url_1,'img_url_2'=>$node->img_url_2,
                'parent_id'=>$node->parent_id,'depth'=>$node->depth]);
    }

    public function thirdNameHasExist($node){

        $sql="select count(*) as amount from tech_category where name 
                in( select name from tech_category where name=:name and parent_id=:parent_id and id<>:id)";
        
        $rows= Db::query($sql,['name'=>$node->name,'parent_id'=>$node->parent_id,'id'=>isset($node->id)?$node->id:0]);       
        return reset($rows);
    } 

    /**
     * 树的第三层节点编辑
     *
     */
    public function ajaxThirdLevelEdit($node)
    {
        return Db::execute('update tech_category set name=:name,img_url_1=:img_url_1,img_url_2=:img_url_2,
            timestamp=now() where id=:id',
            ['name'=>$node->name,'img_url_1'=>$node->img_url_1,'img_url_2'=>$node->img_url_2,'id'=>$node->id]);
    }

    public function ajaxThirdLevelTemplateEdit($node)
    {
        return Db::execute('update tech_category set file_name=:file_name,timestamp=now() where id=:id',
            ['file_name'=>$node->file_name,'id'=>$node->id]);
    }


    /**
     * 根据父结点查询子集
     *
     */
    public function ajaxQuery($node)
    {
        return Db::query('select id,name,img_url_1,img_url_2,parent_id,popular,academic,depth,func_node_amount(id) as amount
           from tech_category where parent_id=:parent_id order by weight desc',
           ['parent_id'=>$node->parent_id]);
    }

    /**
     * 获取单个结点
     *
     */
    public function getElement($id)
    {
        $rows= Db::query('select id,name,img_url_1,img_url_2,parent_id,popular,academic,depth,weight,file_name
           from tech_category where id=:id',['id'=>$id]);

        return reset($rows);
    }

    /**
     * 删除一个节点
     *
     */

    public function ajaxRemove($node)
    {
        return Db::query('delete from tech_category where id=:id',['id'=>$node->id]);
    }

    // -------------------------------------------------------------------------------------------
    // 微信界面查询
    // -------------------------------------------------------------------------------------------

    public function ajaxCategory()
    {
        return Db::query('select id,name from tech_category where parent_id=0 order by weight desc');
    }

    public function ajaxSubCategory($parentId)
    {
        return Db::query('select id,name,img_url_1,img_url_2 from tech_category where parent_id=:parent_id order by weight desc',
            ['parent_id'=>$parentId]);
    }

    public function ajaxCategorySearch($text)
    {
        // return Db::query('select t1.id, t2.name as category,t1.name from tech_category
        //     t1 inner join tech_category t2 on t1.parent_id = t2.id
        //             where t2.name REGEXP ? and t1.depth=3',[$text]);
        return Db::query('select id,crop,pest from 
                (select t1.id,t2.name as crop,t1.name as pest from tech_category t1
                    inner join tech_category t2 on t1.parent_id=t2.id 
                        where t2.depth=2 ) as t
                            where locate(crop,?)>0',[$text]);
    }

    public function ajaxDetailSearch($text)
    {
        // $rows= Db::query('select t1.id, t2.name as category,t1.name from tech_category
        //     t1 inner join tech_category t2 on t1.parent_id = t2.id and t2.name REGEXP ?
        //             where t1.name REGEXP ? and t1.depth=3',[$crop,$pest]);
        $rows=Db::query('select id,crop,pest from 
                (select t1.id,t2.name as crop,t1.name as pest from tech_category t1
                    inner join tech_category t2 on t1.parent_id=t2.id
                        where t1.depth=3 ) as t
                            where locate(crop,?)>0 and locate(pest,?)>0
                                limit 1',[$text,$text]);

        return reset($rows);
    }

}
