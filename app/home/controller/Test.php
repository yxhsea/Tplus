<?php
/*
* 
* Created by PhpStorm.
* Author: yxhsea [yxhsea@foxmail.com]
* Date: 2017/7/29
*/
namespace app\home\controller;

class Test{
    public function index(){
        var_dump(file_put_contents('install.lock','Tplus-lock'));
    }
}