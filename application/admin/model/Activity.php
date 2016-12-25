<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Activity extends Model
{

    // 返回所有有效的管理员
    public function getAllEnabled()
    {
        return Db::query('select id,name,domain,host_ip_address,entrance_url,
            entrance_alias,timestamp from ep_activities where is_enabled=true');

    }

    // 返回所有进回收站的管理员
    public function getAllDisabled()
    {
        return Db::query('select id,name,domain,host_ip_address,entrance_url,
            entrance_alias,timestamp from ep_activities where is_enabled=false');

    }

    // 返回所有管理员
    public function getAll()
    {
        return Db::query('select id,name,domain,host_ip_address,entrance_url,
            entrance_alias,timestamp from ep_activities');
    }

    // 新增一个管理员
    // 口令和令牌组合加密产生口令
    public function add($activity)
    {
        //dump($admin);
        $rows = Db::execute('insert into ep_activities(name,domain,host_ip_address,is_enabled,
            entrance_url,entrance_alias,timestamp) values(
                :name,:domain,:host_ip_address,1,:entrance_url,:entrance_alias,now())',
                ['name'=>$activity->name,
                    'domain'=>$activity->domain,
                    'host_ip_address'=>$activity->host_ip_address,
                    'entrance_url'=>$activity->entrance_url,
                    'entrance_alias'=>$activity->entrance_alias]); 

        return $rows;
    }


    // 更新管理员信息
    public function edit($admin)
    {

    }

    // 删除一个管理员
    // 假删除进回收站
    public function remove($id)
    {
        return Db::execute('update ep_activities set is_enabled=!is_enabled where id=:id',['id'=>$id]);        
    }
}
