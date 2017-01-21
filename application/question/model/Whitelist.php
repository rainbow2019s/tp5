<?php
namespace app\question\model;

use think\Db;
use think\Model;

class Whitelist extends Model
{
    /**
     * 获取白名单列表
     *
     */
    public function ajaxWhitelist($page)
    {
        return Db::query('select @rowid:=@rowid+1 as rowid,id,name,identity,phone,email,
            is_enabled,is_white,register_time,
            weixin,city,rank,sex from qa_white_list,(select @rowid:=0) as init
            where locate(?,name)>0 or locate(?,name)>0 or locate(?,email)>0
            limit ?,?',
            [$page->text,$page->text,$page->text,
                (intval($page->num)-1)*intval($page->size),intval($page->size)]);
    }  

    public function getWhitelistTotal($page)
    {
        $result=Db::query('select count(*) as amount from qa_white_list
           where locate(?,name)>0 or locate(?,name)>0 or locate(?,email)>0',
           [$page->text,$page->text,$page->text]);
        
        return reset($result);
    } 

    /**
      * 新增专家
      *
      */

    public function ajaxExpertAdd($expert)
    {
         return Db::execute('insert into qa_white_list(name,identity,phone,email,city,sex,
             is_enabled,is_white,rank,register_time) values(:name,:identity,:phone,:email,:city,:sex,
                true,true,1,curdate())',
             ['name'=>$expert->name,'identity'=>'专家','phone'=>$expert->phone,
                'email'=>$expert->email,'city'=>$expert->city,'sex'=>$expert->sex]);
    }

    public function ajaxUserEdit($user)
    {
        $result=Db::query('select id,name,identity,phone,email,is_enabled,is_white,register_time,
            weixin,city,rank,sex from qa_white_list where id=?',[$user->id]);

        return reset($result);
    }

    /**
      * 编辑专家
      *
      */
    public function ajaxExpertEditSubmit($expert)
    {
         return Db::execute('update qa_white_list set name=:name,identity=:identity,phone=:phone,
            email=:email,rank=:rank,city=:city,sex=:sex where id=:id',
             ['name'=>$expert->name,
                'identity'=>$expert->identity,
                'phone'=>$expert->phone,
                'email'=>$expert->email,
                'rank'=>$expert->rank,
                'city'=>$expert->city,
                'sex'=>$expert->sex,'id'=>$expert->id]);
    }

    public function ajaxEnabledStatus($value,$id)
    {
        return Db::execute('update qa_white_list set is_enabled=? where id=?',[$value,$id]);        
    }

    public function ajaxWhiteStatus($value,$id)
    {
        return Db::execute('update qa_white_list set is_white=? where id=?',[$value,$id]);
    }

    /** 
     * 删除用户
     *
     */

    public function ajaxUserRemove($id)
    {
        return Db::execute('delete from qa_white_list where id=?',[$id]);
    }
}
