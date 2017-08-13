<?php
/*
* 公共用户注册验证器
* Author: 初心 [jialin507@foxmail.com]
*/
namespace app\common\validate;

use think\Validate;

class User extends Validate{
    protected $rule = [
        ['username','require|min:3|max:25|unique:user','用户名不能为空！|用户名最少不能低于3个字符|用户名不能超过25个字符|用户名重复！'],
        ['password','require|min:6|max:12|confirm:repassword','密码不能为空！|密码最少不能低于6位|密码不能超过12位|两次密码不一致'],
    ];

}

