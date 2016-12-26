<?php
namespace ecopro;

class Verification
{
    public static function generate($length=4)
    {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }

    
}