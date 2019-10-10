<?php


namespace app\api\validate;


class AddCategory extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty'
    ];
}