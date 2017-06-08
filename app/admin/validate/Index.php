<?php
namespace app\admin\validate;

use think\Validate;

class Index extends Validate
{

    protected $rule = [
        'email|登录邮箱' => ['require', 'email'],
        'password|登录密码' => ['require', 'password'],
        'confirm|确认密码' => ['require', 'confirm:password'],
        'captcha|验证码' => 'require|captcha',
    ];
    
    //protected $rule =['email' => 'require|number'];

    protected $scene = [
        'login' => ['email', 'password', 'captcha'],
        'updatepersoninfo' => ['password', 'confirm'],
    ];
}
