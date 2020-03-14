<?php


namespace app\api\validate;


class LogNew extends BaseValidate
{
    protected $rule = [
        'behavior' => 'require|isNotEmpty',
    ];
}
