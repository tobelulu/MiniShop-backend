<?php


namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use think\facade\Log;
//这个相对路径时相对于入口文件的，即public/think.php
require_once '../extend/WxPay/WxPay.Api.php';

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID){
        if(!$orderID){
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay(){
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);

    }

    /**
     * 检查订单
     */
    private function checkOrderValid(){
        //订单号可能不存在
        //订单号可能和当前用户不匹配
        //订单可能已经支付过
        //进行库存量检测
        $order = OrderModel::where('id','=',$this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }
        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单已支付',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }

    /**
     * 获得预订单
     * @param $totalPrice
     * @throws Exception
     * @throws TokenException
     */
    private function makeWxPreOrder($totalPrice){
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);//订单号
        $wxOrderData->SetTrade_type('JSAPI');//订单类型
        $wxOrderData->SetTotal_fee($totalPrice*100);//总金额
        $wxOrderData->SetBody('厚德云创美妆商城');//商品描述
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));//微信返回支付结果的接口
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * 调用微信预订单接口,返回带签名的预订单参数
     * @param $wxOrderData
     * @throws \WxPayException
     */
    private function getPaySignature($wxOrderData){
        $config = new \WxPayConfig();
        $wxOrder = \WxPayApi::unifiedOrder($config,$wxOrderData);
        if($wxOrder['return_code']!='SUCCESS'||$wxOrder['result_code']!='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
            throw new WeChatException();
        }
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    /**
     * 生成签名
     * @param $wxOrder
     * @return array
     * @throws \WxPayException
     */
    private function sign($wxOrder){
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $config = new \WxPayConfig();
        $sign = $jsApiPayData->MakeSign($config);
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);//删除appID字段
        return $rawValues;
    }

    /**
     * 更新数据库中的预订单字段
     * @param $wxOrder
     */
    private function recordPreOrder($wxOrder){
        OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }
}