<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";




//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();

//②、统一下单

function setorder($openid,$money,$attach){
	$input = new WxPayUnifiedOrder();
	$input->SetBody("test");
	$input->SetAttach($attach);
	$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
	$input->SetTotal_fee($money);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("test");
	//回调地址  外网必须访问
	$input->SetNotify_url("http://noturl.com");
	$input->SetTrade_type("JSAPI");
	$input->SetOpenid($openid);
	return $input;
}
///再次传 openid 和money


function setpay($openid,$money,$attach){
	$tools = new JsApiPay();
	$input=setorder($openid,$money,$attach);
	$order = WxPayApi::unifiedOrder($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	return $jsApiParameters;
}

echo setpay($_POST['openid'],intval($_POST['money']),$_POST['attach']);
//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

