<?php
/**
 * 直连模式
 * 技术联系人 chenrj 17602115638 微信同号
 * 文档地址 https://portal.glocash.com/merchant/index/document
 * 商户后台 https://portal.glocash.com/merchant/index/login
 *
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
 *  想测试失败 可以填错年月日或者ccv即可
 */

//TODO 请仔细查看TODO的注释 请仔细查看TODO的注释 请仔细查看TODO的注释


$sandbox_url = 'https://sandbox.glocash.com/gateway/payment/ccDirect'; //测试地址
$live_url    = 'http://pay.v2gc.test/gateway/payment/ccDirect'; //正式地址

//秘钥 测试地址请用测试秘钥 正式地址用正式秘钥 请登录商户后台查看
$sandbox_key = '85f89b981e120f601f6f9fcd65*********8a0b2eee937f48ad3e9b57bf67d9e'; //TODO 测试秘钥 商户后台查看
$live_key = '776cecacb325b2e8e9d5e2dea122*********e6cd22d40e25935e64cb8a90da7'; //TODO 正式秘钥 商户后台查看(必须材料通过以后才能使用)



//支付参数
$data[ 'REQ_SANDBOX' ]  = 0; //TODO 是否开启测试模式 注意秘钥是否对应
$data[ 'REQ_EMAIL' ]    = 'rongjiang.chen@witsion.com'; //TODO 需要换成自己的 商户邮箱 商户后台申请的邮箱
$data[ 'REQ_TIMES' ]    = time (); //请求时间
$data[ 'REQ_INVOICE' ]  = 'TEST'.date ( "YmdHis" ).rand ( 1000, 9999 ); //订单号
$data[ 'BIL_METHOD' ]   = 'C01'; //请求方式
$data[ 'CUS_EMAIL' ]    = 'rongjiang.chen@witsion.com'; //客户邮箱
$data[ 'BIL_PRICE' ]    = '0.1'; //价格
$data[ 'BIL_CURRENCY' ] = 'USD'; //币种
$data[ 'BIL_CC3DS' ]    = 1; //是否开启3ds 1 开启 0 不开启
$data[ 'URL_SUCCESS' ]  = 'http://hs.crjblog.cn/success.php';//支付成功跳转页面
$data[ 'URL_FAILED' ]   = 'http://hs.crjblog.cn/failed.php'; //支付失败跳转页面
$data[ 'URL_NOTIFY' ]   = 'http://hs.crjblog.cn/notify.php'; //异步回调跳转页面

$card = [
    'BIL_CCNUMBER'  => '5546989999990033',            //信用卡卡号
    'BIL_CCHOLDER'  => 'zuochengdong',          //信用卡持卡人姓名
    'BIL_CCEXPM'    => '01',            //信用卡过期月份
    'BIL_CCEXPY'    => '2022',            //信用卡过期年份
    'BIL_CCCVV2'    => '1234',            //信用卡CVV2码
    'BIL_IPADDR'    => '58.247.45.36',    //付款人IP
    'BIL_GOODSNAME' => 'iphone xs ',         //商品名称或描述
];

//更多支付参数请参考文档 经典模式->附录2：付款请求参数表
//签名
$url = $data[ 'REQ_SANDBOX' ] ?$sandbox_url: $live_url;//根据REQ_SANDBOX调整地址
$key = $data[ 'REQ_SANDBOX' ] ?$sandbox_key: $live_key;//根据REQ_SANDBOX调整秘钥
echo $url;
if ( !empty( $_POST ) ) {
    $data               = array_merge ( $data, $_POST );
    $data[ 'REQ_SIGN' ] = hash ( 'sha256', $key.$data[ 'REQ_TIMES' ].$data[ 'REQ_EMAIL' ].$data[ 'REQ_INVOICE' ].$data[ 'CUS_EMAIL' ].$data[ 'BIL_METHOD' ].$data[ 'BIL_PRICE' ].$data[ 'BIL_CURRENCY' ] );
    try {
        file_put_contents ('ccDirect.log',var_export ($url,true).PHP_EOL,FILE_APPEND);
        file_put_contents ('ccDirect.log',var_export ($data,true).PHP_EOL,FILE_APPEND);

        $data      = curl_request ( $url, 'post', $data, true );
        $parseData = json_decode ( $data, true );

        echo "<pre>";
        print_r ($data);
        echo "</pre>";
        if ( isset( $parseData[ 'REQ_ERROR' ] ) ) {
            echo "<pre>";
            print_r ( $parseData );
            echo "</pre>";
            die;
        }
        file_put_contents ('ccDirect.log',var_export ($data,true).PHP_EOL,FILE_APPEND);
        file_put_contents ('ccDirect.log',var_export ($parseData,true).PHP_EOL,FILE_APPEND);
        if ( $data&&$parseData ) {
            header ( 'HTTP/1.1 301 Moved Permanently' );
            header ( 'Location: '.$parseData[ 'URL_CC3DS' ] );
        }
        else {
            echo "<pre>";
            print_r ( $parseData );
            print_r ( $data );
            echo "</pre>";
        }
    }catch ( Exception $e ) {
        echo "<pre>";
        print_r ( $e->getMessage () );
        echo "</pre>";
    }
}

function curl_request ( $url, $method = 'post', $data = null, $https = true )
{
    //1.初识化curl
    $ch = curl_init ( $url );
    //2.根据实际请求需求进行参数封装
    //返回数据不直接输出
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    //如果是https请求
    if ( $https === true ) {
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
    }
    //如果是post请求
    if ( $method === 'post' ) {
        //开启发送post请求选项
        curl_setopt ( $ch, CURLOPT_POST, true );
        //发送post的数据
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    }
    //3.发送请求
    $result = curl_exec ( $ch );
    //4.返回返回值，关闭连接
    curl_close ( $ch );
    return $result;
}


?>
<html>
<body>
<form method="post" action="index.php">
    <?php foreach ( $card as $key => $val ) { ?>
        <div><span style="display: inline-block;width: 150px;text-align: right;padding-right: 15px;"><?php echo $key; ?>
                :</span><input value="<?php echo $val; ?>" name="<?php echo $key; ?>"/></div>
    <?php } ?>
    <input type="submit" value="提交"/>
</form>
</body>
</html>


