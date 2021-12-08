<?php
/**
 * 经典模式
 * 技术联系人 陈荣江 17602115638 微信同号
 * 文档地址 https://docs.glocash.com
 * 商户后台 https://portal.glocashpayment.com/#/login
 */

/**
 * 测试卡
 *   Visa | 4907639999990022 | 12/2020 | 029 paid
 *   MC   | 5546989999990033 | 12/2020 | 464 paid
 *   Visa | 4000000000000002 | 01/2022 | 237 | 14  3ds paid
 *   Visa | 4000000000000028 | 03/2022 | 999 | 54  3ds paid
 *   Visa | 4000000000000051 | 07/2022 | 745 | 94  3ds paid
 *   MC   | 5200000000000007 | 01/2022 | 356 | 34  3ds paid
 *   MC   | 5200000000000023 | 03/2022 | 431 | 74  3ds paid
 *   MC   | 5200000000000106 | 04/2022 | 578 | 104 3ds paid
 *
 */
//TODO 请仔细查看TODO的注释 请仔细查看TODO的注释 请仔细查看TODO的注释
$sandbox_url = 'https://sandbox.glocashpayment.com/gateway/payment/index'; //测试地址
$live_url = 'https://pay.glocashpayment.com/gateway/payment/index'; //正式地址

//秘钥 测试地址请用测试秘钥 正式地址用正式秘钥 请登录商户后台查看
$sandbox_key = '9dc6a0682d7cb718fa140d0b8017a01c4e9a9820beeb45da020601a2e0a63514'; //TODO 测试秘钥 商户后台查看
$live_key = 'c2e38e7d93dbdd3efaa61028c3d27a1a2577df84fa62ae752df587b4f90b8ef7'; //TODO 正式秘钥 商户后台查看(必须材料通过以后才能使用)

//支付参数
$data['REQ_SANDBOX']  = 1; //TODO 是否开启测试模式 0 正式环境 1 测试环境
$data['REQ_EMAIL']    = '2101653220@qq.com'; //TODO 商户邮箱 商户后台申请的邮箱
$data['REQ_TIMES']    = time(); //请求时间
$data['REQ_INVOICE']  = 'TEST'.date ( "YmdHis" ).rand ( 1000, 9999 ); //订单号
$data['BIL_METHOD']   = 'C01'; //请求方式
$data['REQ_MERCHANT']   = 'Merchant Name'; //商户名
$data['BIL_GOODSNAME']   = '#gold#Runescape/OSRS Old School/ 10M Gold'; //TODO 商品名称必填 而且必须是正确的否则无法结算
$data['CUS_EMAIL']    = 'rongjiang.chen@witsion.com'; //客户邮箱
$data['BIL_PRICE']    = '15'; //价格
$data['BIL_CURRENCY'] = 'USD'; //币种
$data['BIL_CC3DS']    = 1; //是否开启3ds 1 开启 0 不开启
$data['URL_SUCCESS']  = 'http://example.v2gc.test/success.php';//支付成功跳转页面
$data['URL_FAILED']   = 'http://example.v2gc.test/failed.php'; //支付失败跳转页面
$data['URL_NOTIFY']   = 'http://example.v2gc.test/notify.php'; //异步回调跳转页面
$data['MCH_DOMAIN_KEY']   = ''; //作为商户通知地址
$url = $data['REQ_SANDBOX'] ? $sandbox_url : $live_url;//根据REQ_SANDBOX调整地址
$key = $data['REQ_SANDBOX'] ?$sandbox_key: $live_key;//根据REQ_SANDBOX调整秘钥

$data['REQ_SIGN']   = hash('sha256', $key . $data['REQ_TIMES'] . $data['REQ_EMAIL'] . $data['REQ_INVOICE'] . $data['CUS_EMAIL'] . $data['BIL_METHOD'] . $data['BIL_PRICE'] . $data['BIL_CURRENCY']);
try {
    file_put_contents ('request.log',var_export ($url,true).PHP_EOL,FILE_APPEND);
    file_put_contents ('request.log',var_export ($data,true).PHP_EOL,FILE_APPEND);
    $res = file_get_contents($url);
    $data      = curl_request($url, 'post', $data, true);
    $parseData = json_decode($data, true);
    if(isset($parseData['REQ_ERROR'])){
        echo "<pre>";
        print_r($parseData);
        echo "</pre>";die;
    }
    file_put_contents ('request.log',var_export ($data,true).PHP_EOL,FILE_APPEND);
    file_put_contents ('request.log',var_export ($parseData,true).PHP_EOL,FILE_APPEND);
    if ($data && $parseData) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $parseData['URL_PAYMENT']);
    } else {
        echo "<pre>";
        print_r($parseData);
        print_r($data);
        echo "</pre>";die;
    }
} catch (Exception $e) {
    echo "<pre>";
    print_r($e->getMessage());
    echo "</pre>";die;
}
function curl_request($url, $method = 'get', $data = null, $https = true)
{
    //1.初识化curl
    $ch = curl_init($url);
    //2.根据实际请求需求进行参数封装
    //返回数据不直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //如果是https请求
    if ($https === true) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    //如果是post请求
    if ($method === 'post') {
        //开启发送post请求选项
        curl_setopt($ch, CURLOPT_POST, true);
        //发送post的数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    //3.发送请求
    $result = curl_exec($ch);
    if($result === false){
        return curl_error($ch);
    }
    //4.返回返回值，关闭连接
    curl_close($ch);
    return $result;
}






