<?php


namespace app\api\controller\v1;


use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

require_once '../extend/WxPay/WxPay.Api.php';

class Pay
{

    //请求预订单
    public function getPreOrder($id=''){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    //接收微信回调
    public function receiveNotify(){
        //检测库存量
        $notify = new WxNotify();
        $config = new \WxPayConfig();
        $notify->Handle($config);
    }

}
