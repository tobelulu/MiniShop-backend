<?php


namespace app\api\controller\v1;

use app\api\model\Order as OrderModel;
use app\api\model\Config as ConfigModel;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order
{
    //用户在选择商品后，向API提交包含它所选商品的相关信息
    //API在接受到消息后，需要检测相关商品的库存量
    //有库存则把订单数据存入数据库中=下单成功，返回客户端消息，告诉客户端可以支付
    //客户端调用支付接口进行支付
    //支付时还需要再次检测库存量
    //服务器调用微信的支付接口进行下预订单
    //小程序根据服务器返回的结果拉起微信支付
    //微信会返回支付结果
    //成功：进行库存量检测
    //  成功：进行库存量扣除

    //创建订单
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.sku/a');// /a是为了获取数组
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }

    /**
     * 获取某用户的订单列表
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page=1,$size=10){
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size);
        if($pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $page,
            ];
        }
        $data = $pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return $data;
    }

    /**
     * 获取订单的详细数据
     * @param $id
     * @return mixed
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $orderDetail = OrderModel::where('user_id','=', $uid)->with(['deliverRecord'])->find($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        if ($orderDetail->deliver_record['comp']) {
            $orderDetail->deliver_record['comp'] = config('setting.comp')[$orderDetail->deliver_record['comp']];
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    /**
     * 获取邮费
     * 全场满xx包邮的开关未开时返回['postageFlag' => ?,'postage' => ?,]
     * 打开时返回['postageFlag' => ?,'postage' => ?,'condition' => ?]
     * @throws \app\lib\exception\ParameterException
     */
    public function getPostage(){
        (new OrderPlace())->goCheck();
        $products = input('post.sku/a');
        $postageFlag = false;//是否有包邮商品
        $postageMax = 0;//邮费的最大值
        $totalPrice = 0;//总价
        foreach ($products as $product){
            if($product['postage']==0){
                $postageFlag = true;
                break;
            }else{
                $totalPrice += $product['price'] * $product['count'];
                if($product['postage']>$postageMax){
                    $postageMax=$product['postage'];
                }
            }
        }
        $result = [
            'postageFlag' => 0,
            'postage' => 0,
        ];
        if(!$postageFlag) { // 若无包邮商品
            $config = ConfigModel::all('1,2');
            if ($config[1]->detail) { // 如果全场包邮开关打开
                $result['postageFlag'] = 1;
                $result['condition'] = $config[0]->detail;
                if($totalPrice < $config[0]->detail){//若不满足全场包邮条件
                    $result['postage'] = $postageMax;
                }
            } else {
                $result['postage'] = $postageMax;
            }
        }
        return $result;
    }

    // 关闭订单
    public function close($id) {
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $order = OrderModel::where('user_id','=', $uid)->find($id);
        if(!$order){
            throw new OrderException();
        }
        if ($order->status == OrderStatusEnum::UNPAID) {
            $order->status = OrderStatusEnum::CLOSED;
            $order->save();
            return Json(new SuccessMessage(),201);
        } else {
            throw new OrderException(['msg' => '关闭订单失败']);
        }
    }

    // 确认收货
    public function receive($id) {
        (new IDMustBePositiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $order = OrderModel::where('user_id', '=', $uid)->find($id);
        if(!$order){
            throw new OrderException();
        }
        if ($order->status == OrderStatusEnum::DELIVERED) {
            $order->status = OrderStatusEnum::RECEIVED;
            $order->save();
            return Json(new SuccessMessage(),201);
        } else {
            throw new OrderException(['msg' => '确认收货失败']);
        }
    }

}
