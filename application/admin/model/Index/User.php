<?php
namespace app\admin\model\index;

use think\Db;
use think\Model;

class User extends Model
{
    public function getId()
    {
        return '12345';
    }

    public function getFactory()
    {
        return Db::query('select id,code,nums from scg_factory where nums>:nums', ['nums' => 0]);
    }
}
