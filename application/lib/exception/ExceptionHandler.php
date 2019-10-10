<?php


namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //需要返回客户端当前请求的URL路径

    public function render(Exception $e)
    {
        if($e instanceof BaseException){
            //如果是自定义异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }
        else{
            //控制错误信息和日志是TP5的详细页面，还是自定义的json
            if(config('app_debug')){
                return parent::render($e);
            }
            else{
                $this->code = 500;
                $this->msg = '服务器错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }

        }
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => Request::url()
        ];
        return json($result,$this->code);
    }

    private function recordErrorLog(Exception $e){
        Log::record($e->getMessage(),'error');
    }
}