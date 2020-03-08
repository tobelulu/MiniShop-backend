<?php


namespace app\api\validate;


class UserInfo extends BaseValidate
{
    protected $rule = [
        'nickname' => 'require',
        'avatarUrl' => 'require',
        'extend' => 'require',
    ];
}
