<?php
/**
 * 异步通知
 * 技术联系人 陈荣江 17602115638 微信同号
 * 文档地址 https://portal.glocash.com/merchant/index/document
 * 商户后台 https://portal.glocash.com/merchant/index/login
 *
 */

file_put_contents ('notify.log',var_export ($_POST,true).PHP_EOL,FILE_APPEND);
file_put_contents ('notify.log',var_export ($_GET,true).PHP_EOL,FILE_APPEND);
file_put_contents ('notify.log',var_export ($_SERVER,true).PHP_EOL,FILE_APPEND);
//该数据为post 或者get 发送过来 具体类型看以商户后台设置
//TODO 具体的post 还是get 可以在商户后台进行设置
$data = $_POST;
$key = "";//后台支付密钥
$sign = hash('sha256', $key . $data['REQ_TIMES'] . $data['REQ_EMAIL'] . $data['CUS_EMAIL'] .$data['TNS_GCID'] .$data['BIL_STATUS'] . $data['BIL_METHOD'] . $data['PGW_PRICE'] . $data['PGW_CURRENCY']);

//TODO 也可以咨询相关出口ip 进行限制
if($data['REQ_SIGN'] == "" || $sign != $data['REQ_SIGN']){
    file_put_contents ('notify_error.log',var_export ($data,true).PHP_EOL,FILE_APPEND);
    return false;
}

//TODO 接下来是业务逻辑操作 比如修改订单状态 以及发货

$status = 'unpaid';//取自业务系统
if('paid' == $data['BIL_STATUS'] && 'unpaid' == $status){
    //支付成功 操做具体业务逻辑
}elseif('paid'==$status&& 'paid' == $data['BIL_STATUS']){
    //部分退款
}else if('refunded' == $data['BIL_STATUS']){
    //退款成功 操做具体业务逻辑
}else {
    //其他业务逻辑 refunding pending chargeback 等
}


