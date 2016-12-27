<?php
namespace app\knowledge\controller;

use think\Controller;
use think\View;
use think\Request;

class Index extends Controller
{
    public function index()
    {
        return view();
    }

    public function upload()
    {
        $file=$this->request->file('image');
        $path=ROOT_PATH.'public'.DS.'uploads'.DS.'knowledge';
        $info=$file->move($path);
        if($info){
            echo $info->getSaveName();
            exit;
        }

        echo $file->getError();
    }
}
