<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class User extends Model
{

    // 判断手机号是否唯一
    public function phoneIsUnique($phone,$id)
    {
        $sql='select count(*) as amount from ep_admin_users where phone=:phone';
        $data=['phone'=>$phone];

        if(isset($id)){
            $sql='select count(*) as amount from ep_admin_users where phone=:phone and id<>:id';    
            $data=['phone'=>$phone,'id'=>$id];
        }

         return Db::query($sql,$data);

    }

    // 判断邮箱是否唯一
    public function emailIsUnique($email,$id)
    {
        $sql='select count(*) as amount from ep_admin_users where email=:email';
        $data=['email'=>$email];

        if(isset($id)){
            $sql='select count(*) as amount from ep_admin_users where email=:email and id<>:id';    
            $data=['email'=>$email,'id'=>$id];
        }

         return Db::query($sql,$data);

    }

    // 返回所有有效的管理员
    public function getAllEnabled()
    {
        return Db::query('select id,name,register,phone,is_super from ep_admin_users where is_enabled=true');

    }

    // 返回所有进回收站的管理员
    public function getAllDisabled()
    {
        return Db::query('select id,name,register,phone,is_super from ep_admin_users where is_enabled=false');

    }

    // 返回所有管理员
    public function getAll()
    {
        return Db::query('select id,name,register,phone,is_super from ep_admin_users');
    }

    // 新增一个管理员
    // 口令和令牌组合加密产生口令
    public function add($admin)
    {
        //dump($admin);
        $rows = Db::execute('insert into ep_admin_users(name,register,phone,email,is_enabled,
            is_super,password,token,timestamp) values(
                :name,curdate(),:phone,:email,1,:is_super,:password,:token,now())',
                ['name'=>$admin->name,'phone'=>$admin->phone,
                    'email'=>$admin->email,
                    'is_super'=>$admin->is_super,
                    'password'=>$admin->password,
                    'token'=>$admin->token]); 

        return $rows;
    }

    // 获取管理员信息
    public function getSuperAdmin()
    {
        $rows=Db::query('select id,name,phone,email,password,token from ep_admin_users 
            where is_enabled=1 and is_super=1 limit 1');
        return reset($rows);
    }

    // 获取管理员
    public function getAdmin($admin)
    {
        $rows=Db::query('select id,name,phone,email,password,token from ep_admin_users
          where is_enabled=1 and is_super=0 and phone=:phone limit 1',
          ['phone'=>$admin->phone]);          
          return reset($rows);
    }

    // 更新管理员信息
    public function edit($admin)
    {

    }

    // 删除一个管理员
    // 假删除进回收站
    public function remove($id)
    {
        return Db::execute('update ep_admin_users set is_enabled=!is_enabled where id=:id',['id'=>$id]);        
    }

    // 普通管理员绑定功能列表和未绑定的    
    public function adminBindAllActivities($adminId)
    {
        return Db::query(
            'select a.id as activity_id,a.name,
	            ifnull(aa.ep_admin_users_id,0) as selected
	            from ep_activities a 
		            left join ep_admin_app aa on a.id=aa.ep_activities_id 
                    and aa.ep_admin_users_id=?',[$adminId]);
    }

    // 普通管理员绑定功能列表
    public function adminBindActivities($adminId)
    {
        return Db::query(
            'select a.id as activity_id,a.name
	            from ep_activities a 
		            inner join ep_admin_app aa on a.id=aa.ep_activities_id 
                        and aa.ep_admin_users_id=?',[$adminId]);
    }

    // 普通管理员绑定应用功能
    public function bind($adminId,$activityId=[])
    {
        Db::startTrans();
        try{
            foreach($activityId as $id){
                Db::execute('insert into ep_admin_app(ep_activities_id,ep_admin_users_id) values(?,?)',
                    [$id,$adminId]);
                
            }           

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;
    }
    
    // 普通管理员解绑应用功能
    public function unbind($adminId,$activityId=[])
    {
        Db::startTrans();
        try{
            foreach($activityId as $id){
                Db::execute('delete from ep_admin_app where ep_activities_id=? and ep_admin_users_id=?',
                    [$id,$adminId]);
            }          

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }   

        return false;     
    }



}
