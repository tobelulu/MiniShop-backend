<?php


namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\facade\Log;

require_once '../extend/WxPay/WxPay.Api.php';
require_once '../extend/WxPay/WxPay.Notify.php';

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, &$msg)
    {
        //更新订单状态
        //减库存
        //如果成功处理，返回微信成功处理的消息。
        if($objData->values['result_code'] == 'SUCCESS'){
            $orderNo = $objData->values['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no','=',$orderNo)->find();
                if($order->status == 1){
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if($stockStatus['pass']){
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }
                    else{
                        $this->updateOrderStatus($order->id,false);
                    }
                }
                Db::commit();
                return true;
            }
            catch (Exception $ex){
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }
        else{
            return true;
        }
    }

    /**
     * 更新订单状态
     * @param $orderID
     * @param $success
     */
    private function updateOrderStatus($orderID,$success){
        $status = $success?
            OrderStatusEnum::PAID :
            OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
    }

    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            Product::where('id','=',$singlePStatus['id'])->setDec('stock',$singlePStatus['counts']);
        }
    }
}