<?php
namespace app\app\controller;

use ecopro\Encryption;
use ecopro\ImageHelper;

class Index
{
    public function index()
    {
        return 'Hello ThinkPHP5';
    }
    
    public function test1()
    {
        $str = 'abc';
        
        $key1 = Encryption::encryptCode($str, 60);
        
        echo $key1;
        
    }
    
    public function test2()
    {
        $str = 'bfd759bVSJXltuQ0A3pom3a7pfIkY1IfUk87nIUxH3m0w8';
        
        //$		key1 = Encryption::encryptCode($str, 60);
        $key2 = Encryption::encryptDecode($str, 60);
        
        echo $key2;
        echo 'ok';
    }
    
    public function test3()
    {
        $str = 'http://www.163.com';
        
        $key1 = Encryption::encodeUrl($str);
        echo $key1;
        echo "<br/>";
        $key2 = Encryption::decodeUrl($key1);
        echo $key2;
    }
    
    public function test4()
    {
        $password = '123456';
        
        $key1 = Encryption::encodePassport($password);
        echo $key1;
        echo "<br/>";
        $key2 = Encryption::decodePassport($key1);
        echo $key2;
    }
    
    public function test5()
    {
        $str = '12345';
        
        $key1 = Encryption::SHA1($str, true);
        echo $key1;
        echo "<br/>";
        $key2 = Encryption::SHA1($key1, false);
        echo $key2;
    }
    
    public function test6()
    {
        $key = Encryption::random();
        echo $key;
        $str  = 'aaabbb';
        $key1 = Encryption::desEncrypt($str, $key);
        echo $key1;
        echo "<br/>";
        $key2 = Encryption::desDecrypt($key1, $key);
        echo $key2;
        
    }
    
    public function test7()
    {
        // 		ImageHelper::thumbnail('http://pic31.nipic.com/20130713/7447430_161806835000_2.jpg', 320, 180);
        //
        ImageHelper::cropThumbnail('http://pic31.nipic.com/20130713/7447430_161806835000_2.jpg', 300, 300);
    }
    
    public function test8()
    {
        return "<img src='http://localhost/thinkphp/tp5/app/index/test7' />";
    }
    
    
}