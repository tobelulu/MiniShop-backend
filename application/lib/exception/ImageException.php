<?php


namespace app\lib\exception;


class ImageException extends BaseException
{
    public $code = 400;
    public $msg = '图片错误';
    public $errorCode = 20001;
}