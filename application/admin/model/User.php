<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class User extends Model
{

    //-------------------------------------------------------
    // 登录
    //-------------------------------------------------------

    /**
     * 获取超级管理员信息
     *
     */
    public function getSuperAdmin($admin)
    {
        $rows=Db::query('select id,name,phone,email,password,token from ep_admin_users 
            where is_enabled=1 and is_super=1 and (phone=:phone or email=:email)',
            ['phone'=>$admin->username,'email'=>$admin->username]);
        return reset($rows);
    }


    // 普通管理员登录
    public function getAdmin($admin)
    {
        $rows=Db::query('select id,name,phone,email,password,token from ep_admin_users 
            where is_enabled=1 and is_super=0 and (phone=:phone or email=:email)',
            ['phone'=>$admin->username,'email'=>$admin->username]);
        return reset($rows);
    }


    // 口令重置
    public function resetSubmit($user)
    {
        return Db::execute('update ep_admin_users set password=:password,token=:token,timestamp=now() where id=:id',
            ['password'=>$user->password,'token'=>$user->token,'id'=>$user->id]);
    }


    //-------------------------------------------------------
    // 用户CRUD
    //-------------------------------------------------------

    /**
     * 返回所有有效的管理员
     *
     */
    
    public function ajaxQueryAllUsers($user)
    {
        return Db::query('select @rowid:=@rowid+1 as rowid,id,name,register,phone,
                        email,is_super,if(id=?,1,0) as status 
                            from ep_admin_users,(select @rowid:=0) as init 
                            where is_enabled=true',[$user->id]);
    }

    /* 模块管理员列表 */
    public function ajaxQueryAllAdminUsers($options)
    {
        return Db::query('select id,name,register,phone,email,is_enabled 
                            from ep_admin_users 
                            where is_super=false and 
                                (name REGEXP ? or phone REGEXP ? or email REGEXP ?) limit ?,?',
                                [$options->filter,$options->filter,$options->filter,
                                    ($options->page-1)*$options->pageSize,$options->pageSize]);
    }

    /* 模块管理员列表行数 */
    public function ajaxQueryAllAdminUsersCount($options)
    {
        $rows= Db::query('select count(*) as count
                            from ep_admin_users 
                            where is_super=false and 
                                (name REGEXP ? or phone REGEXP ? or email REGEXP ?)',
                                [$options->filter,$options->filter,$options->filter]);
        return reset($rows);
    }

    // ---------------------- 管理员新增开始 -----------------------------------

    /**
     * 判断手机号是否唯一
     * 新增时 $id值为NULL
     *
     */
    public function phoneIsUnique($phone,$id)
    {
        $sql='select count(*) as amount from ep_admin_users where phone=:phone';
        $data=['phone'=>$phone];

        if(isset($id)){
            $sql='select count(*) as amount from ep_admin_users where phone=:phone and id<>:id';    
            $data=['phone'=>$phone,'id'=>$id];
        }

        $rows=Db::query($sql,$data);

        return reset($rows);

    }

    /**
     * 判断邮箱是否唯一
     * 新增时 $id值为NULL
     */
    public function emailIsUnique($email,$id)
    {
        $sql='select count(*) as amount from ep_admin_users where email=:email';
        $data=['email'=>$email];

        if(isset($id)){
            $sql='select count(*) as amount from ep_admin_users where email=:email and id<>:id';    
            $data=['email'=>$email,'id'=>$id];
        }

        $rows=Db::query($sql,$data);

        return reset($rows);

    }

    /**
     * 新增一个管理员
     * 口令和令牌组合加密产生口令
     */

    public function ajaxAddUser($admin)
    {
        return Db::execute('insert into ep_admin_users(name,register,phone,email,is_enabled,
            is_super,password,token,timestamp) values(
                :name,curdate(),:phone,:email,1,:is_super,:password,:token,now())',
                ['name'=>$admin->name,'phone'=>$admin->phone,
                    'email'=>$admin->email,
                    'is_super'=>$admin->is_super,
                    'password'=>$admin->password,
                    'token'=>$admin->token]); 

    }

    // ---------------------- 管理员新增结束 -----------------------------------

    /**
     * 获取单行管理员记录
     *
     */

    public function ajaxQueryUser($admin)
    {
        $rows=Db::query('select id,name,phone,email from ep_admin_users
          where is_enabled=1 and id=:id',
          ['id'=>$admin->id]);          
        
        return reset($rows);
    }

    /**
     * 更新管理员内容
     *
     */

    public function ajaxEditUser($admin)
    {
        return Db::execute('update ep_admin_users set name=:name,phone=:phone,email=:email,
                timestamp=now() where id=:id',
            ['name'=>$admin->name,'phone'=>$admin->phone,'email'=>$admin->email,'id'=>$admin->id]);
    }







    /**
     * 彻底删除
     * 业务功能绑定真删除
     *
     */
    public function ajaxUserRemove($admin)
    {

        Db::startTrans();
        try{
            // 设置删除标记
            Db::execute('delete from ep_admin_app where ep_admin_users_id=:id',['id'=>$admin->id]);
            Db::execute('delete from ep_admin_users where id=:id',['id'=>$admin->id]);

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;
      
    }

    /**
     * 管理员权限提升和下降
     *
     */
    public function ajaxUserRightsChange($admin)
    {
        Db::startTrans();
        try{
            // 设置超级管理员标记
            Db::execute('update ep_admin_users set is_super=:is_super,timestamp=now() 
                where id=:id',['is_super'=>$admin->is_super,'id'=>$admin->id]); 

            // 提升为超级管理员时，需要释放功能绑定
            if($admin->is_super==1){
                Db::execute('delete from ep_admin_app where ep_admin_users_id=:id',['id'=>$admin->id]);
            }         

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;
  
    }

    /* 生成手机验证码 */
    public function createSecurityCode($phone){
        return Db::execute('update ep_admin_users set security_code=right(?,6),
            expire_time=date_add(now(),interval 2 hour) where phone=?',
            [$phone,$phone]);
    }

    /* 获取手机验证码 */
    public function getSecurityCode($phone){
        $rows=Db::query('select security_code from ep_admin_users
          where phone=? and expire_time>now()',[$phone]);
        return reset($rows);
    }

    /* 重置用户口令 */
    public function resetUserPassword($user){
        return Db::execute('update ep_admin_users 
            set token=:token,password=:password,timestamp=now() 
                where phone=:phone',
            ['token'=>$user->token,'password'=>$user->password,'phone'=>$user->phone]);
    }

    /* 模块管理员启用禁用 */
    public function ajaxUserEnabled($user){
        return Db::execute('update ep_admin_users set is_enabled=!is_enabled,
            timestamp=now() 
            where id=:id',
            ['id'=>$user->id]);
    }






    /**
     * 普通管理员绑定功能列表和未绑定的  
     * 
     */

    public function ajaxAdminBindAllActivities($admin)
    {
        return Db::query(
            "select a.id as activity_id,a.name,
	            if(aa.ep_admin_users_id>0,1,0) as checked,
	            if(aa.ep_admin_users_id>0,1,0) as origin
	            from ep_activities a 
		            left join ep_admin_app aa on a.id=aa.ep_activities_id 
                    and aa.ep_admin_users_id=? where a.is_enabled=1 order by a.id",[$admin->id]);
    }    

    /**
     * 普通管理员绑定功能列表
     *
     */

    public function getAdminBindActivities($admin)
    {
        return Db::query(
            'select a.id as activity_id,a.name,a.entrance_alias as alias
	            from ep_activities a 
		            inner join ep_admin_app aa on a.id=aa.ep_activities_id 
                        and aa.ep_admin_users_id=?',[$admin->id]);
                        
        
    }

    /**
     * 普通管理员绑定应用功能
     *
     */
    public function ajaxAdminBinding($rights)
    {
        Db::startTrans();
        try{
            // 绑定
            foreach($rights->binding as $item){
                Db::execute('insert into ep_admin_app(ep_activities_id,ep_admin_users_id) values(?,?)',
                    [$item->activity_id,$item->id]);
                
            }       

            foreach($rights->unbinding as $item){
                Db::execute('delete from ep_admin_app where ep_activities_id=? and ep_admin_users_id=?',
                    [$item->activity_id,$item->id]);
                
            }               

            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false;
    }
    
    

}
