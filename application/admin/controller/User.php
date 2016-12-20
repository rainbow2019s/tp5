<?php
namespace app\admin\controller;

use think\Controller;
use think\Url;
use think\Session;

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

    public function get()
    {
        echo $this->request->domain();
        echo "<br/>";
        echo $this->request->baseFile();
        echo "<br/>";
        echo $this->request->url();
        echo "<br/>";
        echo $this->request->url(true);
        echo "<br/>";
        echo $this->request->baseUrl();
        echo "<br/>";
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
        echo $this->request->url();

        $user=new \stdClass;
        $user->id=23;
        $user->name='刘好';

        $this->request->bind('user',$user);

        echo "<br/>";
        echo $this->request->user->id;

    }

    public function bbc()
    {
        define("PI",3.14);
        
        echo PI*3*3;
        echo "<br/>";
        echo constant("PI")*3*3;
    }

    public function magicConst()
    {
        echo __FILE__;
        echo __DIR__;
        echo __LINE__;
    }

    public function urlParams($name,$city)
    {
        echo $name.','.$city;
        echo Url::build('urlparams',['name'=>'lh2016','city'=>'shanghai']);

        //Session::set('name','l.hao');
        //echo Session::get('name');
        //echo Session::pull('name');
        echo "<br/>-----------------------------<br/>";
        if(!Session::has('name')){
            Session::set('name','l.hao.2012@qq.com');
        }
        
        echo Session::get('name');
    }
}
