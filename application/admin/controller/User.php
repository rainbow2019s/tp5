<?php
namespace app\admin\controller;

use think\Controller;

class User extends Controller
{

    public function index()
    {
        //echo $id;
        //$this->error('我也不知道'); # 默认返回前一页

        $this->assign('value', 'World!');
        return view();

        //return $this->fetch();
    }

    public function test($id)
    {
        echo $id;
        $this->redirect('go', ['id' => $id, 'id2' => 444]);
    }

    public function go($id, $id2)
    {
        echo $id . ',' . $id2;
    }

    public function abc()
    {
        $aa=null;
        $type=empty($aa);
        var_dump($type);  # bool(false)

        $bb;
        $type2=empty($bb);
        var_dump($type2); # bool(false);

        $type3=empty($cc);
        var_dump($type3);

        $cc2=[];
        $type4=empty($cc2);
        var_dump($type4);


        

    }
}
