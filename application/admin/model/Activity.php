<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Activity extends Model
{
    /**
     * 返回所有应用列表
     *
     */
    public function ajaxQueryAllActivities($options)
    {
        return Db::query('select id,name,domain,host_ip_address,is_enabled,entrance_url,
            entrance_alias,timestamp from ep_activities
              where name REGEXP ? or domain REGEXP ? or host_ip_address REGEXP ? limit ?,?',
              [$options->filter,$options->filter,$options->filter,
                ($options->page-1)*$options->pageSize,$options->pageSize]);

    }

    /* 应用列表总行数 */
    public function ajaxQueryAllActivitiesCount($options)
    {
        $rows= Db::query('select count(*) as count from ep_activities
              where name REGEXP ? or domain REGEXP ? or host_ip_address REGEXP ?',
              [$options->filter,$options->filter,$options->filter]);
        return reset($rows);
    }

    /**
     * 注册一个应用功能
     *
     */
    public function ajaxActivityAdd($activity)
    {
        return Db::execute('insert into ep_activities(name,domain,host_ip_address,is_enabled,
            entrance_url,entrance_alias,timestamp) values(
                :name,:domain,:host_ip_address,1,:entrance_url,:entrance_alias,now())',
                ['name'=>$activity->name,
                    'domain'=>$activity->domain,
                    'host_ip_address'=>$activity->host_ip_address,
                    'entrance_url'=>$activity->entrance_url,
                    'entrance_alias'=>$activity->entrance_alias]); 

    }

    /**
     * 获取单个应用记录
     *
     */
    public function ajaxQueryActivity($activity)
    {
        $rows= Db::query('select id,name,domain,host_ip_address,entrance_url,
            entrance_alias,timestamp from ep_activities where id=:id',['id'=>$activity->id]);
        
        return reset($rows);
    }

    /**
     * 更新管理员信息
     *
     */
    public function ajaxEditActivity($activity)
    {
        return Db::execute('update ep_activities set name=:name,domain=:domain,host_ip_address=:host_ip_address,
            entrance_url=:entrance_url,entrance_alias=:entrance_alias,timestamp=now() where id=:id',
            ['name'=>$activity->name,
             'domain'=>$activity->domain,
             'host_ip_address'=>$activity->host_ip_address,
             'entrance_url'=>$activity->entrance_url,
             'entrance_alias'=>$activity->entrance_alias,
             'id'=>$activity->id]);
   
    }

    /**
     * 删除一个小应用
     * 级联删除
     */

    public function ajaxRemoveActivity($activity)
    {
        Db::startTrans();
        try{
            // 设置删除标记
            Db::execute('update ep_activities set is_enabled= not is_enabled where id=:id',['id'=>$activity->id]);  
            Db::execute('delete from ep_admin_app where ep_activities_id=:id',['id'=>$activity->id]);
            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false; 
    }

    /* 应用启用或禁止 */
    public function ajaxEnabledActivity($activity)
    {
        Db::startTrans();
        try{
            // 设置删除标记
            if($activity->is_enabled){
                Db::execute('delete from ep_admin_app where ep_activities_id=:id',['id'=>$activity->id]);
            }
            Db::execute('update ep_activities set is_enabled= not is_enabled where id=:id',['id'=>$activity->id]);  
            Db::commit();
            return true;

        }catch(\Exception $e){
            Db::rollback();
        }

        return false; 
    }
}
