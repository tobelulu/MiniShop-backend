<?php


namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product as ProductModel;
use app\api\model\Sku as SkuModel;
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

    /**
     * 减库存、加销量
     * @param $stockStatus
     * @throws Exception
     */
    private function reduceStock($stockStatus){
        $skuArr = [];
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            $sku = SkuModel::get($singlePStatus['id']);
            $sku->stock = ['dec',$singlePStatus['counts']]; // 减sku库存
            $sku->sale = ['inc',$singlePStatus['counts']]; // 增sku销量
            $sku->save();
            if (array_key_exists($sku->product_id, $skuArr)) {
                $skuArr[$sku->product_id] += $singlePStatus['counts'];
            } else {
                $skuArr[$sku->product_id] = $singlePStatus['counts'];
            }
        }
        // 对product的遍历单独拿出来，避免多个sku属于同一个product是重复查询product
        foreach ($skuArr as $id=>$sale) {
            $product = ProductModel::with(['sku'])->find($id);
            $product->sale = ['inc',$sale];// 增product销量
            // 判断sku库存，所有sku库存都小于1时，将product库存状态改为0
            $empty = true;
            foreach ($product->sku as $item) {
                if($item['stock'] > 0) {
                    $empty = false;
                }
            }
            if ($empty) {
                $product->stock = 0;
            }
            $product->save();
        }
    }
}
